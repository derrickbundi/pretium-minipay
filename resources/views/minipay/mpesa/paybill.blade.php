@extends('layouts.minipay.main')
@section('title', 'Paybill')
@push('js')
<script>
    const exchange_rate = {{ $rate['exchange_rate'] }};
</script>
@endpush
@section('body')
<section class="section pb-0 bg-light" style="padding: 10px 0 !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">

                <div class="row justify-content-center mt-3">
                    <div class="col-md-10">
                        <div class="card" style="border-radius:15px;">
                            <div style="flex: 1 1 auto;padding: 1rem 1rem 0.1rem 1rem;">
                                MPESA Paybill
                            </div>
                            <div class="card-body">
                                <form id="myForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="d-flex mb-2">
                                                <div class="flex-grow-1">
                                                    <p class="fs-13 mb-0">Select from favorites</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button type="button" class="btn btn-sm btn-default fs-13" data-bs-toggle="modal" data-bs-target="#favoritesModal">
                                                        favorites <i class="ri-arrow-right-line align-bottom"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="favoritesModalLabel">Favorites</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @forelse($favorites as $item)
                                                    <button class="btn btn-outline-success btn-sm mx-1 my-1" type="button" onclick="fillPaybillNumber(this, {{ $item->shortcode }})">{{ $item->public_name ?? $item->shortcode }}</button>
                                                @empty
                                                <small style="font-size:12px;">
                                                    You don't have any added favorite paybill!
                                                </small>
                                                @endforelse
                                            </div>
                                          </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <label for="iconrightInput" class="form-label">Paybill number</label>
                                            <div class="form-icon right">
                                            <input type="number" class="form-control @error('shortcode') is-invalid @enderror" name="shortcode" placeholder="4029669" required id="shortcode">
                                            <i class="ri-archive-drawer-fill"></i>
                                            </div>
                                            @error('account_number')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small style="font-size:12px;color:#15645e;">
                                                <i class="ri-information-line align-center" style="font-size: 14px;"></i>
                                                Payment to wrong Paybill number is non-refundable.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="favorite" name="favorite">
                                                <label class="form-check-label fs-13 mb-0" for="favorite">
                                                    Add to favorite
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label for="iconrightInput" class="form-label">Account number</label>
                                            <div class="form-icon right">
                                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" placeholder="KFE-2023-7109" required id="account_number">
                                            <i class="ri-shield-line"></i>
                                            </div>
                                            @error('account_number')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <label for="iconrightInput" class="form-label">Amount (KES)</label>
                                            <div class="form-icon right">
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" placeholder="100" min="10" id="amount" required>
                                            <i class="ri-coin-line"></i>
                                            </div>
                                            <div class="mt-2">
                                                <p style="font-size:14px;color:#15645e">
                                                    <i class="ri-error-warning-line align-middle"></i>
                                                    wallet balance KES <span id="balance_in_fiat">0.00</span>
                                                </p>
                                            </div>
                                            @error('amount')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-1 pt-2">
                                        <div class="d-flex mb-2">
                                            <div class="flex-grow-1">
                                                <p class="fs-5 mb-0">Facilitation fees<span class="text-muted ms-1 fs-11">({{config('services.minipay_deduction')}}%)</span></p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h6 class="mb-0"> <small>KES</small>
                                                    <span id="fee">0.00</span></h6>
                                            </div>
                                        </div>

                                        <div class="d-flex mb-2">
                                            <div class="flex-grow-1">
                                                <p class="fs-5 mb-0">You will pay<span class="text-muted ms-1 fs-11">(cUSD)</span></p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h6 class="mb-0"><span id="amount_to_pay">0.00</span> cUSD</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn btn-success btn-lg w-100 rounded-pill" type="submit">
                                            Continue
                                        </button>
                                        <div id="loader" style="display: none;">
                                            <center>
                                                <div class="loading mt-4">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <h6 class="mt-2">initiating transaction...</h6>
                                                </div>
                                            </center>
                                        </div>
                                    </div>
                                </form>                              
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
        $('#amount').on('keyup', function(e) {
            const amount = parseFloat(e.target.value)
            let fee = (0.8 / 100) * amount
            fee = parseFloat(fee)
            fee = fee < 1 ? 1 : fee;
            
            const total_amount = amount + fee
            let exchange = "{{ $rate['exchange_rate'] }}"
            exchange = parseFloat(exchange)

            const amount_in_usd = total_amount / exchange
            
            $('#fee').html(fee.toFixed(2))
            $('#amount_to_pay').html(amount_in_usd.toFixed(2))
        })
    })
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fiat = localStorage.getItem('balance_in_fiat') || "0.00"
        document.getElementById('balance_in_fiat').innerHTML = fiat
    })
</script>
<script>
    function fillPaybillNumber(element,number) {
        document.getElementById('shortcode').value = number;
        // var modal = new bootstrap.Modal(document.getElementById('favoritesModal'));

        var buttons = document.querySelectorAll('#favoritesModal .btn-outline-success');
        buttons.forEach(function(btn) {
            btn.classList.remove('active');
        });
        
        element.classList.add('active');

        // modal.hide();
    }
  </script>
@endpush