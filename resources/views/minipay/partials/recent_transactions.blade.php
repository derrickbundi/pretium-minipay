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
    <h5 class="mt-0">No recent transactions!</h5>
</div>
@endif
