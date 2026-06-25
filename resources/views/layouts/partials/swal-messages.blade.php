{{-- SUCCESS VE UPDATE SONRASI SWEET ALERT GELMEYECEK --}}
{{-- SADECE SILME ISLEMI GIBI AJAX ISLEMLERI ICIN SwalHelper KULLANILACAK --}}

@if (session('error'))
<script>
    Swal.fire({
        title: "{{ __('Hata!') }}",
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: "{{ __('Tamam') }}",
        color: '#ffffff',
        background: '#1e1e2d',
        customClass: {
            confirmButton: "btn btn-danger"
        }
    });
</script>
@endif
