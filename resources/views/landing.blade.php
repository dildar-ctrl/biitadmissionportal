<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Barani Institute of Information Technology — Official Online Admission Portal" />
    <title>BIIT Admission Portal</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0c7347;
            --primary-dark: #0a5f3b;
            --accent: #34d399;
            --ink: #0f2419;
            --muted: #64748b;
            --border: #e5e7eb;
            --surface: #f7f9f8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: var(--ink);
            background: #fff;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ---------- Hero (dark green) ---------- */
        .hero {
            position: relative;
            background: linear-gradient(160deg, #0d4a30 0%, #0c7347 55%, #0e5c3c 100%);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 42px 42px;
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* Header */
        .topnav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: contain;
            background: #fff;
        }

        .brand-name {
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            line-height: 1.2;
        }

        .brand-sub {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12.5px;
            font-weight: 500;
        }

        .btn-signin {
            background: #fff;
            color: var(--ink);
            font-weight: 600;
            font-size: 14px;
            padding: 10px 22px;
            border-radius: 999px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .btn-signin:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }

        /* Hero content */
        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 40px;
            align-items: center;
            padding: 30px 0 56px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 999px;
            margin-bottom: 26px;
        }

        .hero h1 {
            color: #fff;
            font-size: 56px;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.02em;
            margin-bottom: 22px;
        }

        .hero h1 .accent {
            color: var(--accent);
        }

        .hero p.lead {
            color: rgba(255, 255, 255, 0.82);
            font-size: 17px;
            line-height: 1.6;
            max-width: 520px;
            margin-bottom: 32px;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            margin-bottom: 44px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 24px;
            border-radius: 10px;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .btn-primary {
            background: #fff;
            color: var(--ink);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-outline {
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
        }

        .btn-outline:hover {
            border-color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        /* ---------- Animated one-by-one image slideshow (replaces static seal-card) ---------- */
        .gallery-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
        }

        .gallery-card p.gallery-title {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            font-size: 15px;
            margin-top: 16px;
        }

        .slideshow {
            position: relative;
            width: 100%;
            height: 300px;
            border-radius: 14px;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            inset: 6px;
            border-radius: 12px;
            overflow: hidden;
            opacity: 0;
            box-shadow: 0 0 0px rgba(52, 211, 153, 0);
            animation-name: slideCycle;
            animation-duration: 30s;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .s1  { animation-delay: 0s; }
        .s2  { animation-delay: 2s; }
        .s3  { animation-delay: 4s; }
        .s4  { animation-delay: 6s; }
        .s5  { animation-delay: 8s; }
        .s6  { animation-delay: 10s; }
        .s7  { animation-delay: 12s; }
        .s8  { animation-delay: 14s; }
        .s9  { animation-delay: 16s; }
        .s10 { animation-delay: 18s; }
        .s11 { animation-delay: 20s; }
        .s12 { animation-delay: 22s; }
        .s13 { animation-delay: 24s; }
        .s14 { animation-delay: 26s; }
        .s15 { animation-delay: 28s; }

        @keyframes slideCycle {
            0%     { opacity: 0; transform: scale(1.06); box-shadow: 0 0 0px rgba(52, 211, 153, 0); }
            1%     { opacity: 1; transform: scale(1.03); box-shadow: 0 0 18px 4px rgba(52, 211, 153, 0.55); }
            4%     { opacity: 1; transform: scale(1); box-shadow: 0 0 30px 8px rgba(52, 211, 153, 0.85); }
            5.6%   { opacity: 1; transform: scale(0.99); box-shadow: 0 0 18px 4px rgba(52, 211, 153, 0.5); }
            6.6%   { opacity: 0; transform: scale(0.96); box-shadow: 0 0 0px rgba(52, 211, 153, 0); }
            100%   { opacity: 0; }
        }

        /* Stats */
        .stats {
            display: flex;
            gap: 64px;
            padding-top: 36px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .stat-num {
            color: #fff;
            font-size: 30px;
            font-weight: 800;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ---------- Feature cards ---------- */
        .features {
            max-width: 1240px;
            margin: -46px auto 0;
            padding: 0 40px 70px;
            position: relative;
            z-index: 3;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 24px;
            box-shadow: 0 10px 30px rgba(15, 36, 25, 0.06);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(15, 36, 25, 0.1);
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }

        .feature-icon svg {
            width: 22px;
            height: 22px;
            stroke: #fff;
        }

        .feature-card h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ---------- Programs section ---------- */
        .programs {
            max-width: 1240px;
            margin: 0 auto;
            padding: 20px 40px 80px;
        }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 34px;
            gap: 20px;
        }

        .section-head h2 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.01em;
            margin-bottom: 8px;
        }

        .section-head p {
            color: var(--muted);
            font-size: 15px;
        }

        .btn-ghost {
            background: #fff;
            color: var(--ink);
            border: 1.5px solid var(--border);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 20px;
            border-radius: 10px;
            white-space: nowrap;
            transition: border-color .15s ease, transform .15s ease;
        }

        .btn-ghost:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .program-category {
            margin-bottom: 34px;
        }

        .program-category:last-child {
            margin-bottom: 0;
        }

        .category-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .category-head h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .duration-pill {
            background: #e7f5ee;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            padding: 4px 12px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .program-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .program-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .program-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 36, 25, 0.08);
            border-color: var(--primary);
        }

        .program-tag {
            flex-shrink: 0;
            display: inline-block;
            background: var(--primary);
            color: #fff;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
            padding: 5px 10px;
            border-radius: 999px;
        }

        .program-card span {
            color: var(--ink);
            font-size: 14px;
            font-weight: 500;
            line-height: 1.35;
        }

        /* ---------- Footer ---------- */
        footer {
            border-top: 1px solid var(--border);
            background: var(--surface);
        }

        .footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            padding: 40px;
        }

        .footer-inner h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .footer-inner p {
            color: var(--muted);
            font-size: 13.5px;
            margin-bottom: 4px;
        }

        .footer-copy {
            color: #94a3b8;
            font-size: 12.5px;
            margin-top: 14px;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 980px) {
            .hero h1 {
                font-size: 40px;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            .gallery-card {
                order: -1;
                max-width: 380px;
                margin: 0 auto;
            }

            .slideshow {
                height: 240px;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .program-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats {
                gap: 40px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 600px) {
            .hero-inner,
            .features,
            .programs,
            .footer-inner {
                padding-left: 20px;
                padding-right: 20px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .feature-grid,
            .program-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <div class="hero">
        <div class="hero-inner">
            <nav class="topnav">
                <div class="brand">
                    <img src="{{ asset('logo.png') }}" alt="BIIT Logo">
                    <div>
                        <div class="brand-name">BIIT</div>
                        <div class="brand-sub">Admission Portal</div>
                    </div>
                </div>
                <!-- <a href="{{ route('login') }}" class="btn-signin">Sign in</a> -->
            </nav>

            <div class="hero-grid">
                <div>
                    <span class="badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" />
                        </svg>
                        Affiliated with PMAS-AAUR &middot; Since 1998
                    </span>

<h1>Shape your future in <span class="accent">technology</span>.</h1>
                    <p class="lead">
    Begin your academic journey with Barani Institute of Information Technology.
    Explore our programs, submit your admission application, and stay updated
    on your admission progress all in one place.
</p>

                    <div class="hero-actions">
                        <a href="{{ route('signup') }}" class="btn btn-primary">
                            Apply now
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline">login</a>
                    </div>

                    <div class="stats">
                        <div>
                            <div class="stat-num">25+</div>
                            <div class="stat-label">Years of legacy</div>
                        </div>
                        <div>
                            <div class="stat-num">13</div>
                            <div class="stat-label">Degree programs</div>
                        </div>
                        <div>
                            <div class="stat-num">100%</div>
                            <div class="stat-label">Online process</div>
                        </div>
                    </div>
                </div>

                <div class="gallery-card">
                    <div class="slideshow">
                        <div class="slide s1"><img src="{{ asset('images/biit-1.jpg') }}" alt="BIIT 1"></div>
                        <div class="slide s2"><img src="{{ asset('images/biit-2.jpg') }}" alt="BIIT 2"></div>
                        <div class="slide s3"><img src="{{ asset('images/biit-3.jpg') }}" alt="BIIT 3"></div>
                        <div class="slide s4"><img src="{{ asset('images/biit-4.jpg') }}" alt="BIIT 4"></div>
                        <div class="slide s5"><img src="{{ asset('images/biit-5.jpg') }}" alt="BIIT 5"></div>
                        <div class="slide s6"><img src="{{ asset('images/biit-6.jpg') }}" alt="BIIT 6"></div>
                        <div class="slide s7"><img src="{{ asset('images/biit-7.jpg') }}" alt="BIIT 7"></div>
                        <div class="slide s8"><img src="{{ asset('images/biit-8.jpg') }}" alt="BIIT 8"></div>
                        <div class="slide s9"><img src="{{ asset('images/biit-9.jpg') }}" alt="BIIT 9"></div>
                        <div class="slide s10"><img src="{{ asset('images/biit-10.jpg') }}" alt="BIIT 10"></div>
                        <div class="slide s11"><img src="{{ asset('images/biit-11.jpg') }}" alt="BIIT 11"></div>
                        <div class="slide s12"><img src="{{ asset('images/biit-12.jpg') }}" alt="BIIT 12"></div>
                        <div class="slide s13"><img src="{{ asset('images/biit-13.jpg') }}" alt="BIIT 13"></div>
                        <div class="slide s14"><img src="{{ asset('images/biit-14.jpg') }}" alt="BIIT 14"></div>
                        <div class="slide s15"><img src="{{ asset('images/biit-15.jpg') }}" alt="BIIT 15"></div>
                    </div>
                    <p class="gallery-title">Barani Institute of Information Technology</p>
                </div>
            </div>
        </div>
    </div>

    <section class="features">
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                        <path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5" />
                    </svg>
                </div>
                <h3>Create account</h3>
                <p>Sign up with your email in seconds.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8" />
                    </svg>
                </div>
                <h3>Choose program</h3>
                <p>Pick from BS, ADP and MS degree programs.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 16V4M8 8l4-4 4 4" />
                        <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                    </svg>
                </div>
                <h3>Upload documents</h3>
                <p>Result cards, CNIC, photo and more.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z" />
                        <path d="M14 3v6h6M9 15l2 2 4-4" />
                    </svg>
                </div>
                <h3>Track &amp; download</h3>
                <p>Get your application form instantly.</p>
            </div>
        </div>
    </section>

    <section class="programs">
        <div class="section-head">
            <div>
                <h2>Degree programs</h2>
                <p>Undergraduate, associate and graduate programs open for admission.</p>
            </div>
            <a href="{{ route('signup') }}" class="btn-ghost">
                See all &amp; apply
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align:-2px; margin-left:4px;">
                    <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
            </a>
        </div>

        @php
            $programCategories = [
                [
                    'name' => 'BS Computer Science',
                    'duration' => '4 Years',
                    'programs' => [
                        ['tag' => 'BSCS', 'title' => 'General Computing'],
                        ['tag' => 'BSCS', 'title' => 'Artificial Intelligence'],
                        ['tag' => 'BSCS', 'title' => 'Software Engineering'],
                    ],
                ],
                [
                    'name' => 'Bachelor of Engineering Technology',
                    'duration' => '4 Years',
                    'programs' => [
                        ['tag' => 'BET', 'title' => 'Information'],
                    ],
                ],
                [
                    'name' => 'Associate Degree Program',
                    'duration' => '2 Years',
                    'programs' => [
                        ['tag' => 'ADP', 'title' => 'Artificial Intelligence'],
                        ['tag' => 'ADP', 'title' => 'Mobile App Dev'],
                        ['tag' => 'ADP', 'title' => 'Web App Dev'],
                    ],
                ],
                [
                    'name' => 'Allied Health Degree Program',
                    'duration' => '4 Years',
                    'programs' => [
                        ['tag' => 'BS', 'title' => 'MLT'],
                        ['tag' => 'BS', 'title' => 'Biochemistry'],
                        ['tag' => 'BS', 'title' => 'Biotechnology'],
                        ['tag' => 'BS', 'title' => 'Microbiology'],
                    ],
                ],
                [
                    'name' => 'Business Administration',
                    'duration' => '4 Years',
                    'programs' => [
                        ['tag' => 'BBA', 'title' => 'BBA (Hons)'],
                        ['tag' => 'BS', 'title' => 'Accounting & Finance'],
                    ],
                ],
            ];
        @endphp

        @foreach ($programCategories as $category)
            <div class="program-category">
                <div class="category-head">
                    <h3>{{ $category['name'] }}</h3>
                    <span class="duration-pill">{{ $category['duration'] }}</span>
                </div>
                <div class="program-grid">
                    @foreach ($category['programs'] as $program)
                        <div class="program-card">
                            <span class="program-tag">{{ $program['tag'] }}</span>
                            <span>{{ $program['title'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <footer>
        <div class="footer-inner">
            <h4>Barani Institute of Information Technology</h4>
            <p>Affiliated to PMAS Arid Agriculture University, Rawalpindi &middot; Established 1998</p>
            <p class="footer-copy">&copy; {{ date('Y') }} BIIT Admission Portal</p>
        </div>
    </footer>

</body>

</html>