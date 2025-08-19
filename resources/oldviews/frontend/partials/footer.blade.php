<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>Frost LMS</h5>
                <p class="text-muted">Your comprehensive learning management system for modern education.</p>
                <div class="social-links">
                    <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('pricing') }}">Pricing</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Support</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>Newsletter</h5>
                <p class="text-muted">Subscribe to get updates on new courses and features.</p>
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Your email">
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; {{ date('Y') }} Frost LMS. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('terms') }}" class="text-muted me-3">Terms of Service</a>
                <a href="#" class="text-muted">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>
