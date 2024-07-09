@extends('layouts.minipay.main')
@section('title', 'Trasactions')
@section('body')
<section class="section pb-0 bg-light" style="padding: 10px 0 !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card" style="border-radius: 15px;">
                            <div style="flex: 1 1 auto;padding: 1rem 1rem 0 1rem;">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="fs-3 mb-0">All transactions</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ri-more-fill align-middle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">                            
                                @foreach ($items as $item)
                                <a href="{{route('minipay.mpesa.pay.review',$item->transaction_hash)}}">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <ul class="list-unstyled vstack gap-2 mb-0" style="float:left;">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                                            <i class="ri-outlet-2-line "></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">
                                                                {{ strtolower($item->type) }} 
                                                                @if($item->is_disbursed == "COMPLETE")
                                                                <span class="badge rounded-pill" style="background-color: #15645e;color:white;font-size:9px;">
                                                                    {{$item->is_disbursed}}
                                                                </span>
                                                                @elseif($item->is_disbursed == "PENDING")
                                                                <span class="badge rounded-pill" style="background-color: orange;color:white;font-size:9px;">
                                                                    {{$item->is_disbursed}}
                                                                </span>
                                                                @elseif($item->is_disbursed == "REFUNDED")
                                                                <span class="badge rounded-pill" style="background-color:orange;color:white;font-size:9px;">
                                                                    {{$item->is_disbursed}}
                                                                </span>
                                                                @else
                                                                <span class="badge rounded-pill" style="background-color: red;color:white;font-size:9px;">
                                                                    {{$item->is_disbursed}}
                                                                </span>
                                                                @endif
                                                            </h6>
                                                            <small class="text-muted">{{$item->currency_code}} {{number_format($item->amount,2)}}</small>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <ul class="list-unstyled vstack gap-2 mb-0" style="float:right;">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                                            <i class="ri-mac-line"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">
                                                                chain
                                                                <span class="badge rounded-pill" style="background-color: {{$item->status == "success" ? "#15645e" : "red" }};color:white;font-size:9px;">
                                                                    {{$item->status}}
                                                                </span>
                                                            </h6>
                                
                                                            @php
                                                                $hexString = $item->transaction_hash;
                                                                $firstDigits = substr($hexString, 0, 6);
                                                                $lastDigits = substr($hexString, -3);
                                                            @endphp
                                
                                                            <small class="text-muted">
                                                                {{ $firstDigits }}...{{ $lastDigits }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </a>
                                <hr>
                            @endforeach
                            @if($items->count() < 1)
                            <div class="py-4 text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                </lord-icon>
                                <h5 class="mt-4">No recent transactions!</h5>
                            </div>
                            @endif


                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection