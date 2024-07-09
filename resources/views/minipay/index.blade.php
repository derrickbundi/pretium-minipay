@extends('layouts.minipay.main')
@section('title', 'Minipay')
@section('body')
<section class="section pb-0 bg-light" style="padding: 10px 0 !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">
                
                <div class="row">
                    <div class="col md-12">
                        <div class="card card-animate mt-3" style="border-radius:15px;">
                            <img src="{{asset('minipayv1.png')}}" clas="mb-3 mt-3" alt="Pretium <> MiniPay" style="height: auto;width:100%;border-radius:15px;">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <lord-icon src="https://cdn.lordicon.com/qhviklyi.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <select name="" id="country-select" class="form-select" style="width: 80px;height: 30px;font-size: 12px;padding: 4px;margin: 5px;">
                                            <option value="ke">KE &nbsp;🇰🇪</option>
                                            <option value="ng">NG &nbsp;🇳🇬</option>
                                            <option value="sa">SA &nbsp;🇿🇦</option>
                                        </select>
                                    </div>
                                </div>                           
                                <h3 class="mb-2">
                                    <span id="balance_in_fiat"></span>
                                    <span class="text-muted fs-13" id="currency-code" style="color:#15645e !important;"></span>
                                </h3>
                                {{-- <h6 class="mb-0">
                                    <span id="balance"></span>
                                    cUSD
                                </h6> --}}

                                <div class="d-flex">
                                    <div class="flex-grow-1">
            
                                        <h6 class="mb-0">
                                            <span id="balance"></span>
                                            <span style="color:#15645e;">cUSD</span>
                                        </h6>

                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('minipay.profile') }}" id="profileButton" style="color:#15645e">
                                            favorites <i class="ri-arrow-right-line align-bottom"></i>
                                        </a>
                                        {{-- <a href="{{ route('minipay.profile') }}" class="fs-14" id="profileButton" style="padding-right:5px;">                                            
                                            Spend & Earn
                                            <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-1"><span class="visually-hidden">unread messages</span></span>
                                        </a> --}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p style="padding-left:5px;">Select payments&nbsp;<i class="ri-arrow-right-down-line align-middle"></i></p>
                    </div>
                </div>

                <div class="row" id="paybill-row" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border-radius:15px;">
                            <div class="card-body bg-danger-subtle">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <img src="{{asset('icons/mpesa.png')}}" alt="Mpesa Icon" style="height: 50px;width:50px;">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h5>Paybill</h5>
                                        <span class="badge rounded-pill mpesa-badge" style="background-color: #3aa335;color:white;font-size:9px;">MPESA</span>
                                    </div>
                                    <div>
                                        <a href="{{route('minipay.mpesa.paybill')}}" class="btn btn-outline-success btn-md" id="payButton">
                                            pay <i class="ri ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="till-row" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border-radius:15px;">
                            <div class="card-body bg-danger-subtle">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <img src="{{asset('icons/mpesa.png')}}" alt="Mpesa Icon" style="height: 50px;width:50px;">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h5>Till / Buy Goods</h5>
                                        <span class="badge rounded-pill mpesa-badge" style="background-color: #3aa335;color:white;font-size:9px;">MPESA</span>
                                    </div>
                                    <div>
                                        <a href="{{route('minipay.mpesa.till')}}" class="btn btn-outline-success btn-md" id="payButton1">
                                            pay <i class="ri ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="send-money-row" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border-radius:15px;">
                            <div class="card-body bg-danger-subtle">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <img src="{{asset('icons/mpesa.png')}}" alt="Mpesa Icon" style="height: 50px;width:50px;">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h5>Send Money</h5>
                                        <span class="badge rounded-pill mpesa-badge" style="background-color: #3aa335;color:white;font-size:9px;">MPESA</span>
                                    </div>
                                    <div>
                                        <a href="{{route('minipay.mpesa.send.money')}}" class="btn btn-outline-success btn-md" id="sendButton">
                                            pay <i class="ri ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="airtime-row" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border-radius:15px;">
                            <div class="card-body bg-danger-subtle">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <img src="{{asset('icons/phone-call.png')}}" class="mt-2" alt="Mpesa Icon" style="height: 30px;width:30px;">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h5>Buy Airtime</h5>
                                        <span class="badge rounded-pill mpesa-badge" style="background-color: #15645e;color:white;font-size:9px;">utility bill</span>
                                    </div>
                                    <div>
                                        <a href="{{route('minipay.airtime.buy')}}" class="btn btn-outline-success btn-md" id="airtimeButton">
                                            buy <i class="ri ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="placeholder-row" style="display: none;">
                    <div class="col-md-12">
                        <div class="live-preview">
                            <span class="placeholder col-6"></span>
                            <span class="placeholder w-75"></span>
                            <span class="placeholder" style="width: 25%;"></span>
                        </div>
                        <div class="text-center mt-3 mb-3">
                            <p style="color:orange">
                                <i class="ri-error-warning-line align-midle"></i>
                               Stay tuned! Coming soon...
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body">
                                <h5>
                                    Recent transactions
                                    <span id="view-all-btn" style="float: right;"></span>
                                </h5>
                                <br>
                                <div class="minipay-loader-container">
                                    <div class="minipay-loader" id="cardloader" style="display: none; text-align: center;"></div>
                                  </div>
                                <div id="records-container"></div>                          
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.minipay.footer')
</section>
@endsection
@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    function handleSelectChange(event) {
        const selectedValue = event.target.value
        localStorage.setItem('selected_country', selectedValue)
        window.location.reload()
    }
    const selectElement = document.getElementById('country-select')
    selectElement.addEventListener('change', handleSelectChange)

    const selectedCountry = localStorage.getItem('selected_country')
    if (selectedCountry) {
        selectElement.value = selectedCountry
    }
</script>
<script>
    var address = localStorage.getItem('address');

    document.addEventListener('DOMContentLoaded', function() {
        $('#cardloader').show();        

        function loadTransactions() {
            if (address !== null && address !== undefined) {
                clearInterval(addressCheckInterval);

                var currency_code = localStorage.getItem('currency_code')
                const currencyElement = document.getElementById('currency-code')
                currencyElement.innerText = currency_code;

                const uri = "{{ route('minipay.recent.transactions', ['address' => 'ADDRESS_PLACEHOLDER']) }}".replace('ADDRESS_PLACEHOLDER', encodeURIComponent(address));
                $.ajax({
                    url: uri,
                    method: 'GET',
                    success: function(response) {
                        $('#cardloader').hide();
                        $('#records-container').html(response);

                        var uri = '{{ route("minipay.mpesa.transactions", ":address") }}';
                        uri = uri.replace(':address', address);

                        $('#view-all-btn').html(
                            '<a href="'+uri+'" class="btn btn-default-success btn-sm" style="float: right;">' +
                            'view all <i class="ri-arrow-right-line align-middle"></i>' +
                            '</a>'
                        );
                    },
                    error: function() {
                        $('#cardloader').hide();
                        $('#records-container').html('<p>No recent transactions</p>').show();
                    }
                });
            }
        }

        loadTransactions();

        var addressCheckInterval = setInterval(function() {
            address = localStorage.getItem('address');
            loadTransactions();
        }, 1000); // Check every second
    });
    // document.addEventListener('DOMContentLoaded', function() {
    //     $('#cardloader').show();

    //     setTimeout(function() {
    //         const address = window.ethereumAddress
    //         let currency_code = window.currencyCode

    //         if(currency_code === undefined) {
    //             currency_code = "KES"
    //         }

    //         const currencyElement = document.getElementById('currency-code');
    //         currencyElement.innerText = currency_code

    //         if(address !== null && address !== undefined) {
    //             const uri = "{{ route('minipay.recent.transactions', ['address' => 'ADDRESS_PLACEHOLDER']) }}".replace('ADDRESS_PLACEHOLDER', encodeURIComponent(address));
    //             $.ajax({
    //                 url: uri,
    //                 method: 'GET',
    //                 success: function(response) {
    //                     $('#cardloader').hide();
    //                     $('#records-container').html(response);

    //                     var uri = '{{ route("minipay.mpesa.transactions", ":address") }}';
    //                     uri = uri.replace(':address', address);

    //                     $('#view-all-btn').html(
    //                         '<a href="'+uri+'" class="btn btn-default-success btn-sm" style="float: right;">' +
    //                         'view all <i class="ri-arrow-right-line align-middle"></i>' +
    //                         '</a>'
    //                     );
    //                 },
    //                 error: function() {
    //                     $('#cardloader').hide();
    //                     $('#records-container').html('<p>No recent transactions</p>').show();
    //                 }
    //             });
    //         }
    //     }, 3000);
    // });
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let country = localStorage.getItem('selected_country')
    if(country === "ke" || country === null) {
        document.getElementById('paybill-row').style.display = 'block';
        document.getElementById('till-row').style.display = 'block';
        document.getElementById('send-money-row').style.display = 'block';
    } else if(country === "sa") {
        document.getElementById('airtime-row').style.display = 'block';
    } else {
        document.getElementById('placeholder-row').style.display = 'block';
    }
});
</script>
<script>
    var address = localStorage.getItem('address');
    if (address) {
        var payButton = document.getElementById('payButton');
        var route = payButton.getAttribute('href');
        route += '/' + encodeURIComponent(address); // Append address as a route parameter
        payButton.setAttribute('href', route);

        var payButton1 = document.getElementById('payButton1');
        var route1 = payButton1.getAttribute('href');
        route1 += '/' + encodeURIComponent(address); // Append address as a route parameter
        payButton1.setAttribute('href', route1);

        var sendButton = document.getElementById('sendButton');
        var sendRoute = sendButton.getAttribute('href');
        sendRoute += '/' + encodeURIComponent(address); // Append address as a route parameter
        sendButton.setAttribute('href', sendRoute);

        var profileButton = document.getElementById('profileButton');
        var profileRoute = profileButton.getAttribute('href');
        profileRoute += '/' + encodeURIComponent(address); // Append address as a route parameter
        profileButton.setAttribute('href', profileRoute);
    }
</script>
<script>
    var currency_code = localStorage.getItem('selected_country');
    if(currency_code) {
        var airtimeButton = document.getElementById('airtimeButton');
        var airtime_route = airtimeButton.getAttribute('href');
        airtime_route += '/' + encodeURIComponent(currency_code); // Append address as a route parameter
        airtimeButton.setAttribute('href', airtime_route);
    }
</script>
@endpush