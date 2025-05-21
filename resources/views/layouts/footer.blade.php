<footer class="bg-footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer-py-60 footer-border">
                    <div class="row">
                        <!-- LOGO Y ESLOGAN -->
                        <div class="col-lg-3 col-12 mb-0 mb-md-4 pb-0 pb-md-2">
                            <a href="{{ route('home') }}" class="logo-footer">
                                <img src="{{ asset('images/logo-light.png') }}" width="125" height="125" alt="Logo">
                            </a>
                            <p class="para-desc mb-0 mt-4 fst-italic">Una obra, mil formas de sentirla</p>
                        </div>
                        
                        <!-- SOBRE NOSOTROS -->
                        <div class="col-lg-3 col-md-4 col-12 mt-4 pt-2">
                            <h5 class="footer-head">Sobre Nosotros</h5>
                            <ul class="list-unstyled footer-list mt-4">
                                <li class="ms-0">
                                    <a href="{{ route('home') }}" class="text-foot">
                                        <i class="bi bi-chevron-right me-1"></i> Conócenos
                                    </a>
                                </li>
                                <li class="ms-0">
                                    <a href="{{ route('home') }}" class="text-foot">
                                        <i class="bi bi-chevron-right me-1"></i> Términos y Condiciones
                                    </a>
                                </li>
                                <li class="ms-0">
                                    <a href="{{ route('home') }}" class="text-foot">
                                        <i class="bi bi-chevron-right me-1"></i> Política de Privacidad
                                    </a>
                                </li>
                                <li class="ms-0">
                                    <a href="{{ route('home') }}" class="text-foot">
                                        <i class="bi bi-chevron-right me-1"></i> Contacto
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- COLABORACIONES CON INSTITUCIONES -->
                        <div class="col-lg-3 col-md-4 col-12 mt-4 pt-2">
                            <h5 class="footer-head">Con el apoyo de:</h5>
                            <ul class="list-unstyled footer-list mt-4 d-flex flex-column gap-3">
                                <!-- AQUÍ SE PONEN LOS LOGOS DE LOS COLABORADORES -->
                                <img src="{{ asset('images/andalucia-emprende-cadiz.jpg') }}" alt="colaborador1" width="80" height="80">
                                <img src="{{ asset('images/open-future-andalucia.png') }}" alt="colaborador2" width="170" height="80">
                            </ul>
                        </div>
                        
                        <!-- REDES SOCIALES -->
                        <div class="col-lg-3 col-md-4 col-12 mt-4 pt-2">
                            <div class="footer-head">
                                <h5 class="text-light fw-normal title-dark">Redes Sociales</h5>
                                <ul class="list-unstyled social-icon foot-social-icon d-flex gap-3 mt-4">
                                    <!-- AQUÍ SE PONEN LOS ENLACES DE LAS REDES SOCIALES -->
                                    <li>
                                        <a href="#" class="rounded text-light fs-5">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="rounded text-light fs-5">
                                            <i class="bi bi-instagram"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="rounded text-light fs-5">
                                            <i class="bi bi-tiktok"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- SUSCRIPCIÓN -->
                        <div class="mt-1">
                            <h5 class="footer-head">Suscripción</h5>
                            <p class="mt-4">Date de alta y únete a nuestra comunidad.</p>
                            <form>
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="foot-subscribe mb-3">
                                            <label class="form-label">Escribe tu email<span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-icon position-relative flex-grow-1 email-input-wrapper">
                                                    <i class="bi bi-envelope icons" style="top: 15px; left: 15px; position: absolute;"></i>
                                                    <input type="email" name="email" id="emailsubscribe" class="form-control ps-5 rounded" placeholder="Email:" required>
                                                </div>
                                                <div>
                                                    <input type="submit" id="submitsubscribe" name="send" class="btn btn-suscripcion" value="Suscríbete">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PARTE INFERIOR DEL FOOTER -->
    <div class="footer-py-30 footer-bar">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="text-sm-start">
                        <p class="mb-0">© {{ date('Y') }} Everlasting Art <i class="bi bi-heart-fill text-danger"></i></p>
                    </div>
                </div>
                
                <div class="col-sm-6 mt-4 mt-sm-0 pt-2 pt-sm-0">
                    <ul class="list-unstyled footer-list text-sm-end mb-0">
                        <li class="list-inline-item mb-0"><a href="{{ route('home') }}" class="text-foot me-2">Privacidad</a></li>
                        <li class="list-inline-item mb-0"><a href="{{ route('home') }}" class="text-foot me-2">Términos</a></li>
                        <li class="list-inline-item mb-0"><a href="{{ route('home') }}" class="text-foot me-2">Centro de Ayuda</a></li>
                        <li class="list-inline-item mb-0"><a href="{{ route('home') }}" class="text-foot">Contacto</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Botón Volver Arriba -->
<a href="#" id="back-to-top" class="back-to-top rounded-pill" style="display: none;">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- Script para el botón Volver Arriba -->
<script>
    // Mostrar/ocultar el botón "Volver arriba" según el scroll
    document.addEventListener('DOMContentLoaded', function() {
        const backToTop = document.getElementById('back-to-top');
        
        const toggleVisible = () => {
            const scrolled = document.documentElement.scrollTop;
            if (scrolled > 300) {
                backToTop.style.display = 'inline';
            } else {
                backToTop.style.display = 'none';
            }
        };
        
        window.addEventListener('scroll', toggleVisible);
        
        // Función para volver arriba
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>