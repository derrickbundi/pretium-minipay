<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Rate,
    MinipayPaybill,
    MinipayUser,
    MinipayFavorite,
    Xpay,
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
            $type = $request->has('account_number') ? self::PAYBILL : self::BUY_GOODS;
            $command_id = $request->has('account_number') ? 'BusinessPayBill' : 'BusinessBuyGoods';
            $new_code = $request->has('account_number') ? $request->account_number : strtoupper(Str::random(6));

            // if($request->has('account_number')) {
            //     $type = self::PAYBILL;
            //     $command_id = 'BusinessPayBill';
            //     $new_code = $request->account_number;
            // } else {
            //     $type = self::BUY_GOODS;
            //     $command_id = 'BusinessBuyGoods';
            //     $new_code = strtoupper(Str::random(6));
            // }

            if($request->has('mobile')) {
                $type = self::MOBILE;
                $prompt = $this->paymentservice->mpesa_prompt_business_pay_bill($request->mobile,$request->amount,$new_code);
            } else {
                $prompt = $this->paymentservice->pay_to_pay_bill_or_buy_goods($request->amount,$command_id,$request->shortcode,$new_code,'0799770833');
            }

            if(isset($prompt->ResponseCode)) {
                $convo_id = $request->has('mobile') ? $new_code : $prompt->ConversationID;
                $message = 'Success! Processing payment...';
                $response_code = $prompt->ResponseCode;
                $is_disbursed = MinipayPaybill::PENDING;

                if($request->favorite == true) {
                    MinipayFavoriteJob::dispatch($user,$type,$request->shortcode,$new_code)->delay(Carbon::now()->addSeconds(30))->onQueue('emails');
                }
            } else {
                $convo_id = $request->has('mobile') ? null : $prompt['requestId'];
                $message = $request->has('mobile') ? "Unknown error encountered" : $prompt['errorMessage'];
                $response_code = $prompt['errorCode'];
                $is_disbursed = MinipayPaybill::FAILED;
            }

            MinipayPaybill::create([
                "minipay_user_id" => $user->id,
                "transaction_hash" => $request->hash,
                "shortcode" => $request->has('mobile') ? $request->mobile : $request->shortcode,
                "amount" => $request->amount,
                "amount_in_usd" => $request->amount_in_usd,
                "facilitation_fee" => $request->fee,
                "account_number" => $new_code,
                "status" => $request->status,
                "type" =>  $type,
                "is_disbursed" => $is_disbursed,
                "message" => $message,
                "conversation_id" => $convo_id
            ]);

            $data = [
                'status' => $is_disbursed,
                'message' => $message
            ];

            $response_data = [
                'status' => $is_disbursed,
                'message' => $message
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
            $distributor = Xpay::where('category','m')->first();
            $prk = $this->encryptionservice->decryptPrk(
                $distributor->private_key,
                $distributor->public_key,
                config('services.dev_email'),
                config('services.ethanol')
            );

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

            $fee_in_usd = $this->userservice->amount_in_usd($country_id,$transaction->facilitation_fee);
            $amount = $transaction->amount_in_usd - $fee_in_usd;

            $issue = $this->blockchainaccountservice->make_payment($amount,$transaction->minipay_user->public_key,$prk,'cUSD');
            if(isset($issue['error_code']) || $issue->status() != 201) {
                if(isset($issue['error_code'])) {
                    $message = $issue['message'];
                } else {
                    $response_data = $issue->json();
                    $message = $response_data['message'];
                }
                $status = false;
            } else {
                $message = "Refunded successfully!";
                $status = true;
            }
            
            $transaction->is_disbursed = MinipayPaybill::REFUNDED;
            $transaction->save();

            $data = [
                'status' => $status,
                'message' => $message
            ];
            
            return $data;
        });
        return response()->json($me,200);
    }
    public function log_error(Request $request) {
        logger($request->all());
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

            $airtime = $this->airtimeservice->buy_airtime($country,$request->amount,$request->mobile);

            $request_id = null;
            if($airtime['data']->errorMessage != "None") {
                $message = $airtime['data']->errorMessage;
                $is_disbursed = MinipayPaybill::FAILED;
            } else {
                $is_sent = null;
                $message = null;
                $airtime_result = $airtime['data']->responses;

                foreach($airtime_result as $result) {
                    $is_sent = $result->status;
                    $request_id = $result->requestId;
                    $message = "Success! Processing airtime...";
                }

                if($is_sent == 'Failed') {
                    $is_disbursed = MinipayPaybill::FAILED;
                } else {
                    $is_disbursed = MinipayPaybill::PENDING;
                }
            }

            MinipayPaybill::create([
                "minipay_user_id" => $user->id,
                "transaction_hash" => $request->hash,
                "shortcode" => $request->mobile,
                "amount" => $request->amount,
                "amount_in_usd" => $request->amount_in_usd,
                "facilitation_fee" => $request->fee,
                "account_number" => strtoupper(Str::random(6)),
                "status" => $request->status,
                "type" =>  self::AIRTIME,
                "is_disbursed" => $is_disbursed,
                "message" => $message,
                "conversation_id" => $request_id,
                "currency_code" => $request->currency_code,
                "mpesa_payload" => json_encode($airtime['data'])
            ]);

            $response_data = [
                'status' => $is_disbursed,
                'message' => $message
            ];
            
            return $response_data;
        });

        return response()->json($me, 200);
    }
}
