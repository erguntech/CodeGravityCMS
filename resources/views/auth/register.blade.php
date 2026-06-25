@extends('layouts.auth')

@section('title', 'Üye Ol')

@section('content')
<!--begin::Form-->
<form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" method="POST" action="{{ route('register') }}">
    @csrf
    <!--begin::Heading-->
    <div class="text-center mb-11">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bolder mb-3">Üye Ol</h1>
        <!--end::Title-->
        <!--begin::Subtitle-->
        <div class="text-gray-500 fw-semibold fs-6">Sisteme kayıt olmak için bilgilerinizi girin</div>
        <!--end::Subtitle=-->
    </div>
    <!--begin::Heading-->

    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Name-->
        <input type="text" placeholder="Ad Soyad" name="name" value="{{ old('name') }}" autocomplete="off" class="form-control bg-transparent @error('name') is-invalid @enderror" required />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <!--end::Name-->
    </div>
    <!--end::Input group=-->

    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="text" placeholder="E-posta" name="email" value="{{ old('email') }}" autocomplete="off" class="form-control bg-transparent @error('email') is-invalid @enderror" required />
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <!--end::Email-->
    </div>
    <!--end::Input group=-->

    <!--begin::Input group-->
    <div class="fv-row mb-8" data-kt-password-meter="true">
        <!--begin::Wrapper-->
        <div class="mb-1">
            <!--begin::Input wrapper-->
            <div class="position-relative mb-3">
                <input class="form-control bg-transparent @error('password') is-invalid @enderror" type="password" placeholder="Şifre" name="password" autocomplete="off" required />
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                    <i class="ki-duotone ki-eye-slash fs-2"></i>
                    <i class="ki-duotone ki-eye fs-2 d-none"></i>
                </span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--end::Input wrapper-->
            <!--begin::Meter-->
            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
            </div>
            <!--end::Meter-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Hint-->
        <div class="text-muted">Harf, rakam ve sembollerden oluşan 8 veya daha fazla karakter kullanın.</div>
        <!--end::Hint-->
    </div>
    <!--end::Input group=-->

    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Repeat Password-->
        <input placeholder="Şifreyi Tekrarla" name="password_confirmation" type="password" autocomplete="off" class="form-control bg-transparent" required />
        <!--end::Repeat Password-->
    </div>
    <!--end::Input group=-->

    <!--begin::Submit button-->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
            <!--begin::Indicator label-->
            <span class="indicator-label">Üye Ol</span>
            <!--end::Indicator label-->
            <!--begin::Indicator progress-->
            <span class="indicator-progress">Lütfen bekleyin... 
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            <!--end::Indicator progress-->
        </button>
    </div>
    <!--end::Submit button-->
    <!--begin::Sign up-->
    <div class="text-gray-500 text-center fw-semibold fs-6">Zaten bir hesabınız var mı? 
    <a href="{{ route('login') }}" class="link-primary fw-semibold">Giriş Yap</a></div>
    <!--end::Sign up-->
</form>
<!--end::Form-->
@endsection


