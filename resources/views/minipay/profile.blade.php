@extends('layouts.minipay.main')
@section('title', 'Profile')
@section('body')
<section class="section pb-0 bg-light" style="padding: 10px 0 !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">

                <div class="row justify-content-center mt-3">
                    <div class="col-md-10">

                        <div class="card text-center" style="border-radius: 15px;">
                            <div class="card-body">
                                <img src="{{ asset('user.gif') }}" style="height: 100px;width:100px;" alt="Avatar">
                                <div class="mt-4">
                                    <h5>{{ $user->username }}</h5>
                                    @php
                                        $hexString = $user->public_key;
                                        $firstDigits = substr($hexString, 0, 6);
                                        $lastDigits = substr($hexString, -3);
                                    @endphp
                                    <p class="lh-base">
                                        {{ $firstDigits }}...{{ $lastDigits }}
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#favoritesModal" style="color:#15645e;">
                                            <i class="ri-edit-line align-middle"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="card" style="border-radius: 15px;">
                            <div class="card-body">
                                <div class="mb-4 pb-2">
                                    <img src="{{ asset('gif/gift.gif') }}" alt="Gift" class="avatar-sm">
                                </div>
                                <h6 class="fs-15 fw-semibold">Leaderboard 🎉🎉</h6>
                                <span class="text-success" style="font-size:12px;"><strong>Top 5 daily spenders earn $5 cashback!</strong> 💸💸</span><br>
                                <span class="text-success" style="font-size:12px;"><strong>Top 5 weekly spenders earn $20 cashback!</strong> 💸💸</span>
                                <div class="row mt-3">
                                    <div class="col-md-13 text-center">
                                        <a href="{{ route('minipay.leaderboard') }}" target="_blank" class="btn btn-lg btn-outline-warning rounded-pill">
                                            <strong>
                                                Leaderboard <i class="ri-arrow-right-line align-middle"></i>
                                            </strong>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="row">
                            <div class="col-md-12">

                                <div class="d-flex mb-3 border-bottom pb-2">
                                    <div class="flex-grow-1">
            
                                        <h6 class="mb-0" style="color:#15645e">
                                            Favorites
                                        </h6>

                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ri-more-2-fill align-bottom"></i>
                                    </div>
                                </div>

                                <div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="favoritesModalLabel">Update Account</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('minipay.update.profile',base64_encode($user->id)) }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="">Username</label>
                                                        <input type="text" value="{{ $user->username }}" name="username" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <label for="">Email *</label>
                                                        <input type="email" name="email" class="form-control" required placeholder="jane@website.com">
                                                    </div>
                                                </div>
                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <button class="btn btn-success w-100">Update <i class="ri-arrow-right-line align-middle"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                      </div>
                                    </div>
                                </div>

                                @forelse ($favorites as $item)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 avatar-sm">
                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                            <i class="ri-outlet-2-line "></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <span class="badge rounded-pill" style="background-color: {{$item->category == "PAYBILL" ? "#15645e" : "orange" }};color:white;font-size:9px;">
                                            {{$item->category}}
                                        </span>
                                        <p class="fs-13 mb-0">{{ $item->public_name ?? $item->shortcode }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('minipay.delete.favorite',base64_encode($item->id)) }}" class="btn btn-sm btn-outline-danger rounded-pill">
                                            delete <i class="ri-delete-bin-2-line "></i>
                                        </a>
                                    </div>
                                </div>
                                @empty
                                <center>
                                    <small style="font-size:12px;">
                                        You don't have any favorite shortcode!
                                    </small>
                                </center>
                                @endforelse
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
@endpush