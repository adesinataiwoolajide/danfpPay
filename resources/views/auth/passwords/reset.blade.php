
<?php $ink = "Reser Password"; ?>
@extends('layouts.login')

@section('content')
<div id="wrapper">

        <div class="card card-authentication1 mx-auto my-5">
            {{-- @include('partials._message') --}}

            <div class="card-body">
                <div class="card-content p-2">

                    <div class="text-center">
                        <a href="{{route('admin.logout')}}">
                            <img src="{{asset('styling/assets/logo.png')}}" style="height: 100px;" alt="logo icon">
                        </a>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="card-header" align="center">{{ __('Reset Password') }}</div>
                    <div class="card-title text-uppercase text-center py-3" style="color: red"><b>DANFO PAY <i class="fa fa-car"></i> </b></div>

                    <form method="POST" action="{{ route('password.update') }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                        @csrf
                        <div class="form-group">
                            <div class="position-relative has-icon-left">
                                <label for="" class="sr-only">E-Mail</label>
                                <input type="email" id="exampleInputUsername"
                                class="form-control form-control-rounded {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}"
                                name="email" required autofocus placeholder="Enter Your E-Mail" value="{{$email ?? old('email') }}">
                                <div class="form-control-position">
                                    <i class="icon-user"></i>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="position-relative has-icon-left">
                                <label  class="sr-only">Password</label>
                                <input id="password" type="password" class="form-control form-control-rounded{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                 name="password" required>
                                <div class="form-control-position">
                                    <i class="icon-key"></i>
                                </div>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="position-relative has-icon-left">
                                <label class="sr-only">Repeat Password</label>
                                <input id="password" type="password" class="form-control form-control-rounded" name="password_confirmation" required>
                                <div class="form-control-position">
                                    <i class="icon-lock"></i>
                                </div>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <button type="submit" class="btn btn-danger btn-block waves-effect waves-light">
                                {{ __('Reset Password') }}</button><hr>

                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    </div>


@endsection
