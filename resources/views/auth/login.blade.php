@extends('layouts.auth')

@section('title', 'Giriş')

@push('styles')
<style>
.glitch-wrapper {
    position: relative;
    display: inline-block;
}
.glitch-img {
    animation: horror-flicker 10s infinite;
}
.glitch-wrapper::before,
.glitch-wrapper::after {
    content: "";
    background-image: url("{{ asset('themes/backend/metronic/assets/media/logos/default-dark.svg') }}");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
}
.glitch-wrapper::before {
    left: 3px;
    animation: horror-glitch-1 6s infinite linear alternate-reverse;
    filter: drop-shadow(-3px 0 0 rgba(180,0,0,0.8));
}
.glitch-wrapper::after {
    left: -3px;
    animation: horror-glitch-2 8s infinite linear alternate-reverse;
    filter: drop-shadow(3px 0 0 rgba(150,150,150,0.8));
}

@keyframes horror-flicker {
    0%, 90%, 94%, 98%, 100% { opacity: 1; filter: grayscale(0%) contrast(100%); transform: skewX(0deg); }
    92% { opacity: 0.8; filter: grayscale(50%) contrast(150%); transform: skewX(3deg); }
    96% { opacity: 0.4; filter: grayscale(80%) contrast(200%); transform: skewX(-3deg); }
}

@keyframes horror-glitch-1 {
  0%, 80% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
  81% { opacity: 1; clip-path: inset(10% 0 80% 0); transform: translate(-5px, 2px); }
  82% { opacity: 1; clip-path: inset(80% 0 5% 0); transform: translate(5px, -2px); }
  83% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
  
  92% { opacity: 1; clip-path: inset(40% 0 40% 0); transform: translate(-3px, 1px); }
  93% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
}
@keyframes horror-glitch-2 {
  0%, 75% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
  76% { opacity: 0.8; clip-path: inset(20% 0 60% 0); transform: translate(4px, -1px); }
  77% { opacity: 1; clip-path: inset(60% 0 20% 0); transform: translate(-4px, 1px); }
  78% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
  
  88% { opacity: 1; clip-path: inset(10% 0 70% 0); transform: translate(3px, 3px); }
  89% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
}
</style>
@endpush

@section('content')
<!--begin::Form-->
<form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
    @csrf
    <!--begin::Heading-->
    <div class="text-center mb-11">
        <!--begin::Logo-->
        <a href="{{ url('/') }}" class="d-inline-block glitch-wrapper" style="margin-top: 1.5rem; margin-bottom: 3.0rem;">
            <img alt="Logo" src="{{ asset('themes/backend/metronic/assets/media/logos/default-dark.svg') }}" class="h-35px glitch-img" />
        </a>
        <!--end::Logo-->
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bolder mb-3" style="font-size: 1.50rem;">İçerik Yönetim Paneli</h1>
        <!--end::Title-->
        <!--begin::Subtitle-->
        <div class="text-gray-500 fw-semibold fs-6">Sisteme erişmek için kullanıcı bilgilerinizi giriniz.</div>
        <!--end::Subtitle=-->
    </div>
    <!--begin::Heading-->

    <!--begin::Input group=-->
    <div class="fv-row mb-4">
        <!--begin::Email-->
        <input type="text" placeholder="E-posta" name="email" value="{{ old('email') }}" autocomplete="off" class="form-control bg-transparent @error('email') is-invalid border-danger @enderror" required autofocus />
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        <!--end::Email-->
    </div>
    <!--end::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Password-->
        <input type="password" placeholder="Şifre" name="password" autocomplete="off" class="form-control bg-transparent @error('password') is-invalid border-danger @enderror" required />
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        <!--end::Password-->
    </div>
    <!--end::Input group=-->
    <!--begin::Submit button-->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
            <!--begin::Indicator label-->
            <span class="indicator-label">Giriş Yap</span>
            <!--end::Indicator label-->
            <!--begin::Indicator progress-->
            <span class="indicator-progress">Lütfen bekleyin... 
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            <!--end::Indicator progress-->
        </button>
    </div>
    <!--end::Submit button-->
</form>
<!--end::Form-->
@endsection
