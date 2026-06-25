    <footer class="footer mt-50">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <a href="{{ url('/') }}"><img alt="jobhub" src="{{ asset('themes/frontend/assets/imgs/theme/jobhub-logo.svg') }}" /></a>
                    <div class="mt-20 mb-20">{{ __('Sadece doğrulanmış, bağımsız eskortlar ve elit ajansların yer aldığı, yüksek standartlı yetişkin rehber platformu.') }}</div>
                </div>
                <div class="col-md-2 col-xs-6">
                    <h6>{{ __('Sayfalar') }}</h6>
                    <ul class="menu-footer mt-40">
                        <li><a href="{{ url('/') }}">{{ __('Ana Sayfa') }}</a></li>
                        <li><a href="{{ url('/?type=model') }}">{{ __('Modeller') }}</a></li>
                        <li><a href="{{ url('/?type=agency') }}">{{ __('Ajanslar') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-2 col-xs-6">
                    <h6>{{ __('Popüler Şehirler') }}</h6>
                    <ul class="menu-footer mt-40">
                        <li><a href="{{ url('/?city_id=1') }}">Istanbul</a></li>
                        <li><a href="{{ url('/?city_id=2') }}">London</a></li>
                        <li><a href="{{ url('/?city_id=3') }}">Paris</a></li>
                        <li><a href="{{ url('/?city_id=4') }}">Antalya</a></li>
                    </ul>
                </div>
                <div class="col-md-2 col-xs-6">
                    <h6>{{ __('Kurumsal') }}</h6>
                    <ul class="menu-footer mt-40">
                        <li><a href="#">{{ __('Hakkımızda') }}</a></li>
                        <li><a href="#">{{ __('Gizlilik Sözleşmesi') }}</a></li>
                        <li><a href="#">{{ __('Kullanım Şartları') }}</a></li>
                        <li><a href="#">{{ __('İletişim') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-2 col-xs-6">
                    <h6>{{ __('Destek') }}</h6>
                    <ul class="menu-footer mt-40">
                        <li><a href="#">S.S.S.</a></li>
                        <li><a href="#">{{ __('Yardım') }}</a></li>
                        <li><a href="#">{{ __('Güvenlik') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom mt-50">
                <div class="row">
                    <div class="col-md-6">
                        Copyright ©2026 <a href="{{ url('/') }}"><strong>{{ $settings['app_name'] ?? 'PRIVE' }}</strong></a>. Tüm Hakları Saklıdır.
                    </div>
                    <div class="col-md-6 text-md-end text-start">
                        <div class="footer-social">
                            <a href="#" class="icon-socials icon-facebook"></a>
                            <a href="#" class="icon-socials icon-twitter"></a>
                            <a href="#" class="icon-socials icon-instagram"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
