<style>
    /* Breadcrumb header */
    .page-area {
        background: #0b1320;
        /* fallback */
        background-image: linear-gradient(180deg, rgba(11, 19, 32, 0.9), rgba(11, 19, 32, 0.9)), url('/assets/img/headers/knowledge.jpg');
        background-size: cover;
        background-position: center;
        overflow: hidden;
        padding: 4rem 0 3rem;
        position: relative;
    }

    .page-area .breadcumb-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(60% 60% at 50% 40%, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0) 60%);
        pointer-events: none;
    }

    .section-headline h3 {
        letter-spacing: .3px;
        color: #fff;
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .breadcrumb-wrapper {
        margin-top: 1rem;
    }

    .breadcrumb {
        --bs-breadcrumb-divider: '›';
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        display: inline-flex;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        margin: 0;
        justify-content: center;
    }

    .breadcrumb-item {
        font-size: 0.95rem;
        font-weight: 500;
    }

    .breadcrumb-item a {
        color: #d9e6ff;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 2px 4px;
        border-radius: 4px;
    }

    .breadcrumb-item a:hover {
        color: #fede59;
        background: rgba(254, 222, 89, 0.1);
        text-decoration: none;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, "›");
        color: rgba(255, 255, 255, 0.5);
        padding: 0 0.75rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
    }

    /* Small screens: tighten spacing */
    @media (max-width: 768px) {
        .page-area {
            padding: 3rem 0 2rem;
        }

        .section-headline h3 {
            font-size: 2rem;
        }

        .breadcrumb {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            padding: 0 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .page-area {
            padding: 2.5rem 0 1.5rem;
        }

        .section-headline h3 {
            font-size: 1.75rem;
        }

        .breadcrumb {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="page-area">
    <div class="breadcumb-overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="text-center">
                    <div class="section-headline white-headline text-center">
                        @php
                            $segments = request()->segments();
                            $lastSegment = end($segments);
                            $pageTitle = $lastSegment ? ucfirst(str_replace('-', ' ', $lastSegment)) : 'Home';
                            
                            // Custom titles for specific pages
                            $customTitles = [
                                'blog' => 'Knowledge Base',
                                'courses' => 'Courses & Schedules',
                                'contact' => 'Contact Us',
                                'about' => 'About Us',
                                'faqs' => 'FAQs',
                                'student' => 'Student Portal'
                            ];
                            
                            if (isset($customTitles[$lastSegment])) {
                                $pageTitle = $customTitles[$lastSegment];
                            }
                        @endphp
                        <h3>{{ $pageTitle }}</h3>
                    </div>
                    
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('pages') }}">
                                        <i class="fas fa-home me-1"></i>Home
                                    </a>
                                </li>
                                @php
                                    $url = '';
                                    $segments = request()->segments();
                                @endphp
                                
                                @foreach($segments as $key => $segment)
                                    @php
                                        $url .= '/' . $segment;
                                        $segmentTitle = ucfirst(str_replace('-', ' ', $segment));
                                        
                                        // Apply custom titles for segments
                                        if (isset($customTitles[$segment])) {
                                            $segmentTitle = $customTitles[$segment];
                                        }
                                        
                                        $isLast = ($key === count($segments) - 1);
                                    @endphp
                                    
                                    @if($isLast)
                                        <li class="breadcrumb-item active" aria-current="page">
                                            {{ $segmentTitle }}
                                        </li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ $url }}">{{ $segmentTitle }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
