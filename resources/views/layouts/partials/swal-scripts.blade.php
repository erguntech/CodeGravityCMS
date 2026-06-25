<script>
    const SwalHelper = {
        deleteConfirmation: function(url, table, callback) {
            Swal.fire({
                title: "{!! __('Kayıt Sistemden Silinecek!<br>Emin Misiniz?') !!}",
                text: "{{ __('Bu işlem geri alınamaz!') }}",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "{{ __('Evet') }}",
                cancelButtonText: "{{ __('İptal') }}",
                color: '#ffffff',
                background: '#1e1e2d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "{{ __('İşlem Başarılı!') }}",
                                    text: "{{ __('Kayıt sistemden başarıyla silindi.') }}",
                                    icon: 'success',
                                    confirmButtonText: "{{ __('Tamam') }}",
                                    color: '#ffffff',
                                    background: '#1e1e2d'
                                });
                                if (table) table.ajax.reload();
                                if (callback) callback(response);
                            } else {
                                Swal.fire({
                                    title: "{{ __('Hata!') }}",
                                    text: response.message || "{{ __('Bir hata oluştu.') }}",
                                    icon: 'error',
                                    confirmButtonText: "{{ __('Tamam') }}",
                                    color: '#ffffff',
                                    background: '#1e1e2d'
                                });
                            }
                        },
                        error: function(xhr) {
                            let title = "{{ __('Sistem Hatası!') }}";
                            let message = "{{ __('İşlem gerçekleştirilemedi.') }}";
                            
                            if (xhr.status === 403) {
                                title = "{{ __('Yetkilendirme Hatası!') }}";
                                message = "{{ __('İşlemi gerçekleştirebilmek için yeterli yetkiniz bulunmamaktadır.') }}";
                            }
                            
                            Swal.fire({
                                title: title,
                                text: message,
                                icon: 'error',
                                confirmButtonText: "{{ __('Tamam') }}",
                                color: '#ffffff',
                                background: '#1e1e2d'
                            });
                        }
                    });
                }
            });
        },
        
        success: function(message, title = "{{ __('İşlem Başarılı!') }}") {
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonText: "{{ __('Tamam') }}",
                color: '#ffffff',
                background: '#1e1e2d',
                customClass: {
                    confirmButton: "btn btn-success"
                }
            });
        },
        
        error: function(message, title = "{{ __('Hata!') }}") {
            Swal.fire({
                title: title,
                html: message,
                icon: 'error',
                confirmButtonText: "{{ __('Tamam') }}",
                color: '#ffffff',
                background: '#1e1e2d',
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        }
    };

    $(document).on('click', '.unauthorized-action-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        Swal.fire({
            title: "{{ __('Yetkilendirme Hatası!') }}",
            text: "{{ __('İşlemi gerçekleştirebilmek için yeterli yetkiniz bulunmamaktadır.') }}",
            icon: 'warning',
            confirmButtonText: "{{ __('Tamam') }}",
            color: '#ffffff',
            background: '#1e1e2d'
        });
    });
</script>
