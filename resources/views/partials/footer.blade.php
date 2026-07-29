<footer class="site-footer">
    <div class="container">
        <div class="footer-wordmark" aria-hidden="true">WEBIGNITORS</div>

        <div class="row g-5">
            <div class="col-lg-6">
                <h2 class="footer-title">Have a bold idea? <span>Let’s ignite it.</span></h2>
                <a class="btn btn-lime" href="{{ route('contact') }}">
                    Tell us about it <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
            <div class="col-6 col-lg-2 footer-nav">
                <h6>Explore</h6>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('work') }}">Work</a>
                <a href="{{ route('process') }}">Process</a>
            </div>
            <div class="col-6 col-lg-2 footer-nav">
                <h6>Services</h6>
                <a href="{{ route('services.web') }}">Web</a>
                <a href="{{ route('services.mobile') }}">Mobile apps</a>
                <a href="{{ route('services.marketing') }}">Marketing</a>
                <a href="{{ route('services.ai') }}">AI integration</a>
            </div>
            <div class="col-12 col-lg-2 footer-nav">
                <h6>Say hello</h6>
                <a href="mailto:info@webignitors.in">info@webignitors.in</a>
                <a href="tel:+918261973645">+91 82619 73645</a>
                <a href="{{ route('contact') }}">Project inquiry</a>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} WebIgnitors. Built to make sparks.</span>
            <span>Strategy · Design · Code · Growth</span>
        </div>
    </div>
</footer>
