@extends('layouts.minipay.main')
@section('title', 'Review')
@section('body')
<section class="section pb-0 bg-light" style="padding: 10px 0 !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">

                <div class="row justify-content-center mt-5">
                    <div class="col-md-10">
                        <div class="card" style="border-radius:15px;">
                            <div style="flex: 1 1 auto;padding: 1rem 1rem 0 1rem;">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="fs-3 mb-0">Airtime details</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ri-more-fill align-middle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Type</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0">
                                            <span class="badge badge-soft-success fs-11">
                                                {{ strtolower($payment->type) }}
                                               <i class="ri-checkbox-circle-line align-middle"></i>
                                            </span>
                                        </h6>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Reference ID</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0 fs-4" onclick="copyToClipboard()" id="receipt-number">
                                            {{$payment->account_number}} <i class="ri-file-copy-line align-middle"></i>
                                        </h6>
                                        <p id="copy-success" style="display:none;">Copied!</p>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Mobile number</p>
                                    </div>                                  
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0">
                                            {{$payment->shortcode}}
                                        </h6>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Amount paid</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="fs-4 mb-0">
                                            {{$payment->currency_code}} <span>{{number_format($payment->amount,2)}}</span>
                                        </h6>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Amount ($)</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="fs-4 mb-0">
                                            <span>{{number_format($payment->amount_in_usd,2)}} cUSD</span>
                                        </h6>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-4 mb-0">Status</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0">
                                            @if($payment->is_disbursed == "COMPLETE")
                                            <span class="badge badge-soft-success fs-11">
                                                disbursed <i class="ri-checkbox-circle-line align-middle"></i>
                                            </span>
                                            @elseif($payment->is_disbursed == "PENDING")
                                            <span class="badge badge-soft-primary fs-11">
                                                processing <i class="ri-calendar-line align-middle"></i>
                                            </span>
                                            @elseif($payment->is_disbursed == "REFUNDED")
                                            <span class="badge badge-soft-warning fs-11">
                                                refunded <i class="ri-close-line align-middle"></i>
                                            </span>
                                            @else
                                            <span class="badge badge-soft-danger fs-11">
                                                failed <i class="ri-close-line align-middle"></i>
                                            </span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>

                                <div class="text-center">
                                    @if($payment->is_disbursed == "COMPLETE")
                                    <div class="py-4 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/gqjpawbc.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                        </lord-icon>
                                    </div>
                                    @elseif($payment->is_disbursed == "PENDING")
                                    {{-- <div class="py-4 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/rqptwppx.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                        </lord-icon>
                                    </div> --}}
                                    <div id="loader" style="display: block;">
                                        <center>
                                            <div class="loading mt-4">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <h6 class="mt-2">processing payment...</h6>
                                            </div>
                                        </center>
                                    </div>
                                    @else
                                    <div class="text-center mt-3">
                                        <p style="color:red;">
                                            <i class="ri ri-error-warning-line align-middle"></i>
                                            {{$payment->message}}
                                        </p>
                                    </div>
                                    @if($payment->is_disbursed == "FAILED")
                                    <button class="btn btn-md btn-success" type="button" id="initiateReverse">
                                        Initiate refund <i class="ri-refresh-line align-middle"></i>
                                    </button>
                                    @endif
                                    @endif
                                </div>

                            </div>
                            <div style="flex: 1 1 auto;padding: 0 1rem 0.5rem 1rem;">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">{{ $payment->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        var status = {!! json_encode($payment->is_disbursed) !!};

        if (status === "PENDING") {
            checkStatus()
        }

        function checkStatus() {
        setInterval(() => {
            $.ajax({
                type: 'POST',
                url: "{{ route('minipay.mpesa.confirm') }}",
                data: {
                    transaction_hash: "{{ $payment->transaction_hash }}",
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.status === "COMPLETE" || response.status === "FAILED") {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    loader.style.display = "none";
                    var response_error = 'Oops, error checking payment status!';
                    if (xhr.responseJSON) {
                        response_error = xhr.responseJSON.message || response_error;
                    }
                    Toastify({
                        text: response_error,
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "center",
                        stopOnFocus: true,
                        style: {
                            background: "#f06548",
                        },
                    }).showToast();
                }
            });
        }, 10000);
    }
    });
</script>
<script>
    function copyToClipboard() {
        var receiptNumber = document.getElementById("receipt-number").innerText;
        var tempInput = document.createElement("input");
        tempInput.value = receiptNumber;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); 
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        var successMessage = document.getElementById("copy-success");
        successMessage.style.display = "inline";
        setTimeout(function() {
            successMessage.style.display = "none";
        }, 2000);
    }
</script> 
<script>
    $(document).ready(function() {
        $('#initiateReverse').on('click', function() {
            const originalHtml = $(this).html();
            $(this).prop('disabled', true);
            $(this).html(`
                <span class="d-flex align-items-center">
                    <span class="flex-grow-1 me-2">
                        Loading...
                    </span>
                    <span class="spinner-border flex-shrink-0" role="status" style="height: 1rem;width:1rem;border-width: 0.1rem;">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </span>
            `);

            $.ajax({
                type: 'POST',
                url: "{{route('minipay.refund')}}",
                data: {
                    id: "{{ $payment->id }}",
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.status === true) {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: "#0ab39c",
                            },
                        }).showToast()
                        location.reload();
                    } else {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: "#f06548",
                            },
                        }).showToast()
                        $('#initiateReverse').prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(error) {
                    var response_error = 'Oops, something went wrong!'
                    if(error.responseJSON) {
                        var errors = error.responseJSON.errors
                        $.each(errors, function(key, value) {
                            response_error = value
                        });
                    } else {
                        response_error = error.statusText
                    }
                    Toastify({
                        text: response_error,
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "center",
                        stopOnFocus: true,
                        style: {
                            background: "#f06548",
                        },
                    }).showToast()
                    $('#initiateReverse').prop('disabled', false).html(originalHtml);
                }
            })
        })
    })
</script>
@endpush