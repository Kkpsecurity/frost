<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="utf-8" />

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<meta name="application-name" content="{{ env('APP_NAME', 'Frost Security Training') }}" />
<meta name="referrer" content="unsafe-url" />

<meta name="csrf-token" content="{{ csrf_token() }}" />

<meta name="keywords" content="@yield('page-keywords', 'security training, class d license, class g license, florida, armed security, unarmed security')" />
<meta name="description" content="@yield('page-description', 'Professional security training for Florida Class D and Class G licenses. Expert instruction and flexible online learning.')" />

<link rel="preconnect" href="https://cdn.jsdelivr.net" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.bunny.net" />
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

<!-- Fonts -->
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icon Font Stylesheet -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Cookie Consent -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookieconsent@3.1.1/build/cookieconsent.min.css" />

<!-- Custom CSS via Vite -->
@vite(['resources/css/app.css'])

<!-- Favicon Icon -->
<link href="{{ asset('favicon.ico') }}" sizes="32x32" rel="shortcut icon" type="image/x-icon" />
<link href="{{ asset('favicon.ico') }}" sizes="32x32" rel="shortcut icon" />

<!-- Custom CSS -->
<style>
    :root {
        --primary-color: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #06b6d4;
        --light-color: #f8fafc;
        --dark-color: #1e293b;
    }

    body {
        font-family: 'Figtree', sans-serif;
        line-height: 1.6;
    }

    .navbar-brand {
        font-weight: 600;
        font-size: 1.5rem;
    }

    .navbar-dark {
        background: rgba(26, 35, 50, 0.95) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .brand-logo {
        display: flex;
        align-items: center;
    }

    .brand-text {
        font-weight: 700;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }

    .navbar-nav .nav-link {
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.5rem 1rem !important;
    }

    .navbar-nav .nav-link:hover {
        color: #3498db !important;
    }

    .navbar-nav .nav-link.active {
        color: #3498db !important;
    }

    .hero-section {
        background: linear-gradient(135deg, #1a2332 0%, #2c3e50 100%);
        color: white;
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 20s ease-in-out infinite;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .hero-subtitle {
        font-size: 1.125rem;
        margin-bottom: 3rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    /* Course Cards */
    .course-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .course-card:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-5px);
    }

    .course-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .monitor-display {
        width: 120px;
        height: 80px;
        background: #2c3e50;
        border-radius: 8px;
        margin: 0 auto;
        position: relative;
        border: 3px solid #34495e;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .course-badge {
        text-align: center;
        color: white;
    }

    .badge-text {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .class-text {
        display: block;
        font-size: 1.5rem;
        font-weight: 900;
        margin-top: 0.25rem;
        color: #3498db;
    }

    .course-body {
        text-align: center;
    }

    .course-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: white;
    }

    .course-description {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-bottom: 1rem;
    }

    .course-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #3498db;
        margin-bottom: 1.5rem;
    }

    .course-actions .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 5px;
    }

    /* User Profile Card */
    .user-profile-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: #3498db;
        border-radius: 50%;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-initials {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .profile-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: white;
    }

    .profile-email {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-bottom: 0;
    }

    /* Chat Widget */
    .chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 320px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        overflow: hidden;
    }

    .chat-header {
        background: #3498db;
        color: white;
        padding: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .chat-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .chat-avatar img {
        width: 24px;
        height: 24px;
    }

    .chat-info {
        flex: 1;
    }

    .chat-status {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .chat-body {
        padding: 1rem;
        color: #333;
    }

    .chat-body p {
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .chat-options {
        margin-top: 1rem;
    }

    .chat-option-btn {
        display: block;
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        text-align: left;
        font-size: 0.8rem;
        color: #3498db;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .chat-option-btn:hover {
        background: #e3f2fd;
        border-color: #3498db;
    }

    .chat-option-btn:last-child {
        margin-bottom: 0;
    }

    .features-section {
        background-color: var(--light-color);
    }

    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-color);
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: var(--secondary-color);
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .chat-widget {
            width: 280px;
            bottom: 10px;
            right: 10px;
        }
    }
</style>

@yield('styles')

<title>@yield('title', 'Frost Security Training') | Professional Security Training in Florida</title>

<!-- jQuery (Modern Version) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

@if (App::environment() == 'production' && request()->segment(1) != 'classroom')
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17215206357"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'AW-17215206357');
</script>
@endif
