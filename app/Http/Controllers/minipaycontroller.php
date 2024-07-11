<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Rate,
    MinipayPaybill,
    MinipayUser,
    MinipayFavorite,
    MinipayErrorLog,
    Country
};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Log,
    App,
    Session,
    Http
};
use Carbon\Carbon;

class minipaycontroller extends Controller
{
    const PAYBILL = 'PAYBILL';
    const BUY_GOODS = 'BUY_GOODS';
    const MOBILE = 'MOBILE';
    const AIRTIME = 'AIRTIME';

    public function get_rates($country) {
        $deduction = config('services.buying_rate_deduction');
        switch($country) {
            case 'ng':
                $id = 4;
                break;
            case 'sa':
                $id = 6;
                $deduction = config('services.buying_rate_deduction') - 3;
                break;      
            default:
                $id = 1;
                break;
        }
        $exchange_rate = Rate::where([['country_id', $id],['type',Rate::EXCHANGE_TYPE]])->first();
        $payload = json_decode($exchange_rate->payload, true);
        $rate = [
            "currency_code" => $payload['query']['to'],
            "exchange_rate" => round($payload['info']['rate'] - $deduction,2)
        ];
        return response()->json($rate,200);
    }
    public function index() {        
        return view('minipay.index');
    }
    public function profile($address = null) {   
        if($address == null) {
            return redirect()->back();
        }

        $user = MinipayUser::where('public_key', $address)->first();
        $favorites = MinipayFavorite::where('minipay_user_id',$user->id)->get();    
        return view('minipay.profile',compact('favorites','user'));
    }
    public function delete_favorite($id) {
        $favorite = MinipayFavorite::find(base64_decode($id));
        $favorite->delete();
        Session::flash('success','Favorite deleted');
        return redirect()->back();
    }
    public function update_profile(Request $request, $id) {
        $user = MinipayUser::find(base64_decode($id));
        if($user->email != null) {
            $email = $user->email;
        } else {
            $email = $request->email;
        }

        $user->email = $email;
        $user->username = $request->username;
        $user->save();

        Session::flash('success', 'Account updated');
        return redirect()->back();
    }
    public function recent_transactions($address) {
        $user = MinipayUser::firstOrCreate(['public_key' => $address]);
        $items = MinipayPaybill::where('minipay_user_id', $user->id)->orderBy('id','desc')->take(3)->get();
        return view('minipay.partials.recent_transactions', compact('items'));
    }
    public function mpesa_paybill($address = null) {
        $user = MinipayUser::where('public_key', $address)->first();
        if($user) {
            $favorites = MinipayFavorite::where([['category',MinipayFavorite::PAYBILL],['minipay_user_id',$user->id]])->get();
        } else {
            $favorites = collect();
        }

        $kes_rate = Rate::where([['country_id', 1],['type',Rate::EXCHANGE_TYPE]])->first();
        $payload = json_decode($kes_rate->payload, true);
        $rate = [
            "currency_code" => $payload['query']['to'],
            "exchange_rate" => round($payload['info']['rate'] - config('services.buying_rate_deduction'),2)
        ];
        return view('minipay.mpesa.paybill',compact('rate','favorites'));
    }
    public function mpesa_till($address = null) {
        $user = MinipayUser::where('public_key', $address)->first();
        if($user) {
            $favorites = MinipayFavorite::where([['category',MinipayFavorite::BUY_GOODS],['minipay_user_id',$user->id]])->get();
        } else {
            $favorites = collect();
        }

        $kes_rate = Rate::where([['country_id', 1],['type',Rate::EXCHANGE_TYPE]])->first();
        $payload = json_decode($kes_rate->payload, true);
        $rate = [
            "currency_code" => $payload['query']['to'],
            "exchange_rate" => round($payload['info']['rate'] - config('services.buying_rate_deduction'),2)
        ];
        return view('minipay.mpesa.till',compact('rate','favorites'));
    }
    public function mpesa_send_money() {
        $kes_rate = Rate::where([['country_id', 1],['type',Rate::EXCHANGE_TYPE]])->first();
        $payload = json_decode($kes_rate->payload, true);
        $rate = [
            "currency_code" => $payload['query']['to'],
            "exchange_rate" => round($payload['info']['rate'] - config('services.buying_rate_deduction'),2)
        ];
        return view('minipay.mpesa.send_money',compact('rate'));
    }
    public function mpesa_pay(Request $request) {
        $user = MinipayUser::where('public_key', $request->address)->first();

        $me = DB::transaction(function() use($user,$request) {
            $prompt = Http::post(config('services.prod_master_uri') . '/merchant', [
                'mobile' => $request->mobile,
                'amount' => $request->amount,
                'type' => $request->has('account_number') ? self::PAYBILL : self::BUY_GOODS,
                'shortcode' => $request->shortcode,
                'transaction_code' => $request->has('account_number') ? $request->account_number : strtoupper(Str::random(6)),
                'command_id' => $request->has('account_number') ? 'BusinessPayBill' : 'BusinessBuyGoods',
                'user' => $user
            ]);

            $response = $prompt->json();

            $response_data = [
                'status' => $response->status,
                'message' => $response->message
            ];
            
            return $response_data;
        });

        return response()->json($me, 200);
    }
    public function mpesa_pay_review($hash) {
        $payment = MinipayPaybill::where('transaction_hash',$hash)->first();
        if($payment->type == self::AIRTIME) {
            return view('minipay.airtime.review',compact('payment'));
        }
        return view('minipay.mpesa.review',compact('payment'));
    }
    public function mpesa_confirm(Request $request) {
        $payment = MinipayPaybill::where('transaction_hash',$request->transaction_hash)->first();
        $responseData = [
            'status' => $payment->is_disbursed
        ];
        return response()->json($responseData, 200);
    }
    public function mpesa_transactions($address) {
        $user = MinipayUser::where('public_key', $address)->first();
        $items = MinipayPaybill::where('minipay_user_id', $user->id)->orderBy('id','desc')->take(10)->get();
        return view('minipay.mpesa.all',compact('items'));
    }
    public function refund(Request $request) {
        $transaction = MinipayPaybill::find($request->id);
        if($transaction->is_disbursed != MinipayPaybill::FAILED) {
            $data = [
                'status' => false,
                'message' => 'Oops, transaction already processed or processing!'
            ];
            return response()->json($data,200);
        }

        $me = DB::transaction(function() use($transaction) {
            switch($transaction->currency_code) {
                case 'NGN':
                    $country_id = 4;
                    break;
                case 'ZAR':
                    $country_id = 6;
                    break;      
                default:
                    $country_id = 1;
                    break;
            }

            $prompt = Http::post(config('services.prod_master_uri') . '/refund', [
                'facilitation_fee' => $transaction->facilitation_fee,
                'amount' => $request->amount,
                'country_id' => $country_id,
                'public_key' => $transaction->minipay_user->public_key,
                'asset' => "cUSD"
            ]);

            $response = $prompt->json();

            $response_data = [
                'status' => $response->status,
                'message' => $response->message
            ];
            
            return $response_data;
        });
        return response()->json($me,200);
    }
    public function log_error(Request $request) {
        MinipayErrorLog::create([
            "shortcode" => $request->has('mobile') ? $request->mobile : $request->shortcode,
            "amount" => $request->amount,
            "amount_in_usd" => $request->amount_in_usd,
            "facilitation_fee" => $request->fee,
            "account_number" => $request->account_number,
            "address" =>  $request->address,
            "error" => json_encode($request->error)
        ]);
    }
    public function buy_airtime($currency_code = null) {
        $deduction = config('services.buying_rate_deduction');

        switch($currency_code) {
            case 'ng':
                $id = 4;
                $deduction = config('services.buying_rate_deduction') + 1;
                break;
            case 'sa':
                $id = 6;
                $deduction = config('services.buying_rate_deduction') - 3;
                break;      
            default:
                $id = 1;
                break;
        }

        $country_rate = Rate::where([['country_id', $id],['type',Rate::EXCHANGE_TYPE]])->first();
        $payload = json_decode($country_rate->payload, true);

        $rate = [
            "currency_code" => $payload['query']['to'],
            "exchange_rate" => round($payload['info']['rate'] - $deduction,2)
        ];

        return view('minipay.airtime.buy',compact('rate'));
    }
    public function airtime_pay(Request $request) {
        $user = MinipayUser::where('public_key', $request->address)->first();

        $me = DB::transaction(function() use($user,$request) {
            switch($request->currency_code) {
                case 'NGN':
                    $country_id = 4;
                    break;
                case 'ZAR':
                    $country_id = 6;
                    break;      
                default:
                    $country_id = 1;
                    break;
            }

            $country = Country::find($country_id);

            $prompt = Http::post(config('services.prod_master_uri') . '/airtime', [
                'mobile' => $request->mobile,
                'amount' => $request->amount,
                'country' => $country,
                'user' => $user
            ]);

            $response = $prompt->json();

            $response_data = [
                'status' => $response->status,
                'message' => $response->message
            ];
            
            return $response_data;
        });

        return response()->json($me, 200);
    }
}
