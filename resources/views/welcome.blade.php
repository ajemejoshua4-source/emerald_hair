@php
    $pageTitle = 'Emerald Hair | Nigerian Hair & Beauty Studio';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Emerald Hair luxury wigs, braids, bundles and beauty essentials inspired by premium Nigerian hair styling.">
    <meta name="theme-color" content="#1b1612">
    <title>{{ $pageTitle }}</title>
    <style>
        :root {
            --bg: #f6efe9;
            --panel: #fffaf5;
            --card: #f7efe8;
            --ink: #1b1716;
            --muted: #5f5754;
            --green: #103b34;
            --green-deep: #0d2d2a;
            --emerald: #1d5a4d;
            --gold: #d7a761;
            --gold-soft: #f3d7aa;
            --gold-deep: #b77b2a;
            --brown: #4d3427;
            --shadow: 0 24px 55px rgba(15, 18, 16, 0.16);
            --radius: 30px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: radial-gradient(circle at top, rgba(17, 71, 63, 0.12), transparent 30%), linear-gradient(180deg, #efe4d9 0%, #f9f5f1 26%, #f2efe9 100%);
            color: var(--ink);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .page-shell {
            width: min(1220px, calc(100% - 32px));
            margin: 24px auto 40px;
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(33, 25, 20, 0.08);
            border-radius: 32px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 42px;
            background: rgba(25, 25, 22, 0.92);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), #f7e2b0);
            color: var(--green);
            font-size: 20px;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.5);
        }

        .brand-name {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            font-size: 13px;
        }

        .brand-name strong {
            font-size: 1.15rem;
        }

        .top-nav {
            display: flex;
            align-items: center;
            gap: 26px;
            font-size: 0.79rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
        }

        .top-nav a:hover {
            color: var(--gold-soft);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            border-radius: 999px;
            padding: 16px 28px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), #f4c77b);
            color: var(--green-deep);
            box-shadow: 0 18px 30px rgba(214, 167, 97, 0.32);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
        }

        .btn-light {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.20);
        }

        .hero {
            position: relative;
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            align-items: center;
            min-height: 700px;
            background:
                linear-gradient(90deg, rgba(7, 17, 15, 0.82), rgba(12, 23, 20, 0.45)),
                url('https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=1500&q=80') center/cover no-repeat;
            padding: 40px 42px 52px;
            color: #fff;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 70% 25%, rgba(215, 167, 97, 0.18), transparent 25%);
            pointer-events: none;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto 7% 26px 7%;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.7), transparent);
        }

        .hero-copy {
            position: relative;
            z-index: 1;
            max-width: 620px;
            padding-right: 20px;
        }

        .eyebrow-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--gold-soft);
        }

        .eyebrow {
            display: inline-block;
            margin: 0;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.06);
            font-size: 0.72rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #f6dbae;
        }

        h1 {
            margin: 0;
            font-size: clamp(3.1rem, 5vw, 5.5rem);
            line-height: 0.98;
            letter-spacing: -0.06em;
            text-transform: uppercase;
        }

        h1 span {
            display: block;
            color: var(--gold-soft);
        }

        .lead {
            margin: 22px 0 30px;
            font-size: 1.1rem;
            line-height: 1.8;
            max-width: 550px;
            color: rgba(255, 255, 255, 0.82);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 26px;
        }

        .meta-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .meta-strip strong {
            color: #fff;
        }

        .hero-visual {
            position: relative;
            z-index: 1;
            display: grid;
            justify-items: end;
            gap: 18px;
        }

        .visual-stack {
            display: grid;
            grid-template-columns: 220px 180px;
            gap: 18px;
            align-items: end;
        }

        .mini-card {
            background: rgba(18, 24, 20, 0.58);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            padding: 16px;
            backdrop-filter: blur(8px);
        }

        .mini-card img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            border-radius: 18px;
        }

        .mini-card.small img {
            height: 170px;
        }

        .floating-panel {
            position: absolute;
            right: 8%;
            bottom: 44px;
            width: min(260px, 68%);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            color: var(--ink);
            padding: 18px 18px 14px;
            box-shadow: 0 18px 32px rgba(0, 0, 0, 0.18);
        }

        .floating-panel .price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .floating-panel strong {
            display: block;
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: var(--green);
        }

        .floating-panel p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        section {
            padding: 42px 42px;
        }

        .section-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }

        .section-header h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3rem);
            line-height: 1.1;
            letter-spacing: -0.05em;
            text-transform: uppercase;
        }

        .subtle {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
            max-width: 620px;
        }

        .highlights {
            background: rgba(255, 255, 255, 0.25);
        }

        .highlight-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .highlight-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(34, 24, 19, 0.08);
            border-radius: 24px;
            padding: 22px 18px;
            box-shadow: 0 10px 30px rgba(23, 17, 15, 0.04);
        }

        .highlight-card .icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(216, 159, 83, 0.16), rgba(18, 60, 52, 0.1));
            font-size: 1.3rem;
            margin-bottom: 18px;
        }

        .highlight-card h3 {
            margin: 0 0 10px;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
            text-transform: uppercase;
        }

        .highlight-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 0.96rem;
        }

        .collections {
            background: #f6efe9;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .style-card {
            background: var(--panel);
            border-radius: 26px;
            overflow: hidden;
            border: 1px solid rgba(34, 24, 19, 0.08);
            box-shadow: 0 10px 25px rgba(27, 22, 18, 0.04);
        }

        .style-card img {
            height: 330px;
            width: 100%;
            object-fit: cover;
        }

        .style-card .content {
            padding: 20px 20px 22px;
        }

        .style-card span {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--gold);
            font-size: 0.74rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .style-card h3 {
            margin: 0 0 8px;
            font-size: 1.5rem;
            letter-spacing: -0.04em;
        }

        .style-card p {
            margin: 0 0 14px;
            color: var(--muted);
            line-height: 1.7;
        }

        .link-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--green);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.76rem;
        }

        .catalog {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.12), rgba(122, 71, 43, 0.04));
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(22, 17, 15, 0.08);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 18px 30px rgba(27, 22, 18, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 34px rgba(27, 22, 18, 0.08);
        }

        .product-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            filter: saturate(1.1) contrast(1.02);
        }

        .product-card .info {
            padding: 18px 18px 20px;
        }

        .product-type {
            margin: 0 0 8px;
            color: var(--gold);
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .product-card h3 {
            margin: 0 0 10px;
            font-size: 1.22rem;
            letter-spacing: -0.03em;
        }

        .price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        .price-row strong {
            color: var(--green);
            font-size: 1.15rem;
        }

        .buy-btn {
            border: none;
            background: linear-gradient(135deg, var(--green), var(--emerald));
            color: #fff;
            border-radius: 999px;
            padding: 11px 18px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 14px 24px rgba(16, 59, 52, 0.2);
        }

        .story {
            background: linear-gradient(135deg, #f8efe7 0%, #f3e0d0 100%);
        }

        .story-layout {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 22px;
            align-items: center;
        }

        .story-layout img {
            height: 560px;
            width: 100%;
            object-fit: cover;
            border-radius: 30px;
            box-shadow: var(--shadow);
        }

        .story-copy {
            padding: 18px 8px;
        }

        .story-copy h2 {
            margin: 0 0 18px;
            font-size: clamp(2.2rem, 3vw, 3rem);
            line-height: 1.08;
            letter-spacing: -0.05em;
            text-transform: uppercase;
        }

        .story-copy p {
            color: var(--muted);
            line-height: 1.9;
            font-size: 1.03rem;
            margin: 0 0 20px;
        }

        .reviews {
            background: rgba(17, 19, 18, 0.96);
            color: #fff;
        }

        .reviews .section-header p,
        .reviews .section-header h2,
        .reviews .section-header .eyebrow {
            color: #fff;
        }

        .review-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .review-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 22px;
        }

        .stars {
            margin-bottom: 14px;
            color: var(--gold-soft);
            letter-spacing: 0.16em;
            font-size: 1rem;
        }

        .review-card p {
            margin: 0 0 18px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1rem;
        }

        .review-card strong {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 6px;
        }

        .review-card span {
            color: rgba(255, 255, 255, 0.62);
            font-size: 0.85rem;
        }

        .footer {
            background: #111211;
            color: rgba(255, 255, 255, 0.8);
            padding: 32px 42px 18px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1.2fr;
            gap: 28px;
            padding-bottom: 26px;
        }

        .footer h4 {
            margin: 0 0 16px;
            color: #fff;
            font-size: 0.86rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .footer p,
        .footer a,
        .footer span {
            display: block;
            margin: 0 0 10px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.8;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.62);
            font-size: 0.82rem;
        }

        @media (max-width: 980px) {
            .topbar {
                flex-wrap: wrap;
                justify-content: center;
                text-align: center;
            }

            .top-nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .hero,
            .story-layout,
            .card-grid,
            .product-grid,
            .review-grid,
            .footer-grid,
            .highlight-grid {
                grid-template-columns: 1fr 1fr;
            }

            .hero {
                min-height: auto;
                padding-bottom: 120px;
            }

            .hero-visual {
                justify-items: center;
            }

            .floating-panel {
                position: static;
                width: 100%;
                margin-top: 18px;
            }
        }

        @media (max-width: 720px) {

            section,
            .topbar,
            .footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            .hero,
            .story-layout,
            .card-grid,
            .product-grid,
            .review-grid,
            .footer-grid,
            .highlight-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                padding-top: 30px;
                padding-bottom: 36px;
            }

            .visual-stack {
                grid-template-columns: 1fr;
                width: 100%;
            }

            .section-header {
                display: grid;
                align-items: start;
            }

            .top-nav {
                display: none;
            }

            .top-actions {
                width: 100%;
                justify-content: center;
            }

            .btn {
                width: 100%;
            }

            .hero-actions {
                flex-direction: column;
            }

            .footer-bottom {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand" aria-label="Emerald Hair home">
                <span class="brand-mark">E</span>
                <span class="brand-name"><strong>Emerald Hair</strong><small>Luxury Hair Studio</small></span>
            </div>

            <nav class="top-nav" aria-label="Main navigation">
                <a href="#collections">Wigs</a>
                <a href="#collections">Braids</a>
                <a href="#catalog">Bundles</a>
                <a href="#story">Our Story</a>
                <a href="#reviews">Reviews</a>
                <a href="#contact">Contact</a>
            </nav>

            <div class="top-actions">
                <a class="btn btn-light" href="#catalog">Shop now</a>
                <a class="btn btn-primary" href="#contact">Book styling</a>
            </div>
        </header>

        <main>
            <section class="hero" aria-label="Emerald Hair hero section">
                <div class="hero-copy">
                    <p class="eyebrow">Luxury Nigerian hair &amp; beauty</p>
                    <h1>Own your <span>crown.</span></h1>
                    <p class="lead">Premium wigs, braids, hair bundles and beauty essentials curated for confident
                        women who want flawless styling with a bold Nigerian glam finish.</p>

                    <div class="eyebrow-row">
                        <span class="eyebrow">Luxury Nigerian hair &amp; beauty</span>
                        <span class="mini-badge">Since 2021</span>
                    </div>
                    <h1>Own your <span>crown.</span></h1>
                    <p class="lead">Premium wigs, braids, hair bundles and beauty essentials curated for confident
                        women who want flawless styling with a bold Nigerian glam finish.</p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#catalog">Shop premium hair</a>
                        <a class="btn btn-secondary" href="#collections">Explore looks</a>
                    </div>

                    <div class="meta-strip">
                        <div><strong>500+</strong> happy queens</div>
                        <div><strong>Same day</strong> styling support</div>
                        <div><strong>Wholesale</strong> available</div>
                    </div>
                </div>

                <div class="hero-visual" aria-label="Featured styles visual">
                    <div class="visual-stack">
                        <div class="mini-card">
                            <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80"
                                alt="Beautiful black woman in elegant hair style">
                        </div>
                        <div class="mini-card small">
                            <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80"
                                alt="Premium hair styling close up">
                        </div>
                    </div>

                    <div class="floating-panel">
                        <div class="price">
                            <span>Trending</span>
                            <span>Royal drop</span>
                        </div>
                        <strong>₦ 58,000</strong>
                        <p>Soft body wave wig with smooth finish and natural shine, made for elegant Nigerian glam
                            styling.</p>
                    </div>
                </div>
            </section>

            <section class="highlights">
                <div class="highlight-grid">
                    <article class="highlight-card">
                        <div class="icon">✦</div>
                        <h3>Premium wigs</h3>
                        <p>Natural looking texture and luxurious finish designed for daily wear and special occasions.
                        </p>
                    </article>

                    <article class="highlight-card">
                        <div class="icon">♛</div>
                        <h3>Braids &amp; styles</h3>
                        <p>Fashion-forward braids, knotless looks and trending styles made to match your personality.
                        </p>
                    </article>

                    <article class="highlight-card">
                        <div class="icon">◎</div>
                        <h3>Hair care</h3>
                        <p>Care essentials that protect your hair and keep every look healthy, soft and glossy.</p>
                    </article>

                    <article class="highlight-card">
                        <div class="icon">✓</div>
                        <h3>Fast support</h3>
                        <p>Friendly styling help, quick answers and delivery support wherever you are in Nigeria.</p>
                    </article>
                </div>
            </section>

            <section class="collections" id="collections">
                <div class="section-header">
                    <div>
                        <p class="eyebrow"
                            style="color: var(--brown); border-color: rgba(79,52,41,0.18); background: rgba(216,159,83,0.1);">
                            What we do best</p>
                        <h2>Signature looks</h2>
                    </div>
                    <p class="subtle">Nigerian-inspired beauty for women who love confidence, texture and polished
                        finish. From box braids to lace front wigs, every style is chosen with impact in mind.</p>
                </div>

                <div class="card-grid">
                    <article class="style-card">
                        <img src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=900&q=80"
                            alt="Nigerian braids and long hair styling">
                        <div class="content">
                            <span>01</span>
                            <h3>Luxury braids</h3>
                            <p>Neat knotless braids and chic braided styles that bring elegance to every occasion.</p>
                            <a class="link-row" href="#catalog">View style →</a>
                        </div>
                    </article>

                    <article class="style-card">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80"
                            alt="Black woman with long flowing wavy hair">
                        <div class="content">
                            <span>02</span>
                            <h3>Premium wigs</h3>
                            <p>Soft texture, natural parting and a flawless finish for daily glam and weekend events.
                            </p>
                            <a class="link-row" href="#catalog">Shop wigs →</a>
                        </div>
                    </article>

                    <article class="style-card">
                        <img src="https://images.unsplash.com/photo-1524250502761-1ac6f2e30d43?auto=format&fit=crop&w=900&q=80"
                            alt="Woman in stylish hair bundle look">
                        <div class="content">
                            <span>03</span>
                            <h3>Hair bundles</h3>
                            <p>Full, thick and glossy textures that give you movement, body and beautifully styled
                                volume.</p>
                            <a class="link-row" href="#catalog">See bundles →</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="catalog" id="catalog">
                <div class="section-header">
                    <div>
                        <p class="eyebrow"
                            style="color: var(--brown); border-color: rgba(79,52,41,0.18); background: rgba(216,159,83,0.1);">
                            Best sellers</p>
                        <h2>Trending items</h2>
                    </div>
                    <p class="subtle">Every piece is selected to fit the Nigerian hair market: elegant, easy to style
                        and made to keep your confidence high.</p>
                </div>

                <div class="product-grid">
                    <article class="product-card">
                        <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80"
                            alt="Body wave wig for Nigerian beauty">
                        <div class="info">
                            <p class="product-type">Body wave</p>
                            <h3>Royal Body Wave</h3>
                            <div class="price-row">
                                <strong>₦ 58,000</strong>
                                <button class="buy-btn" type="button">Buy</button>
                            </div>
                        </div>
                    </article>

                    <article class="product-card">
                        <img src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80"
                            alt="Straight lace front wig">
                        <div class="info">
                            <p class="product-type">Lace front</p>
                            <h3>Silk Straight</h3>
                            <div class="price-row">
                                <strong>₦ 64,000</strong>
                                <button class="buy-btn" type="button">Buy</button>
                            </div>
                        </div>
                    </article>

                    <article class="product-card">
                        <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80"
                            alt="Deep wave hair bundle">
                        <div class="info">
                            <p class="product-type">Deep wave</p>
                            <h3>Velvet Curl</h3>
                            <div class="price-row">
                                <strong>₦ 46,000</strong>
                                <button class="buy-btn" type="button">Buy</button>
                            </div>
                        </div>
                    </article>

                    <article class="product-card">
                        <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80"
                            alt="Hair accessory and styling kit">
                        <div class="info">
                            <p class="product-type">Care set</p>
                            <h3>Emerald Care Kit</h3>
                            <div class="price-row">
                                <strong>₦ 19,500</strong>
                                <button class="buy-btn" type="button">Buy</button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="story" id="story">
                <div class="story-layout">
                    <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80"
                        alt="Woman with distinctive Nigerian glam hairstyle">
                    <div class="story-copy">
                        <p class="eyebrow"
                            style="color: var(--brown); border-color: rgba(79,52,41,0.18); background: rgba(255,255,255,0.38);">
                            Our story</p>
                        <h2>Beauty that feels <span style="color: var(--gold);">bold.</span></h2>
                        <p>Emerald Hair was created for women who want more than just hair — they want a look that turns
                            heads, builds confidence and fits naturally into everyday life and celebration.</p>
                        <p>From premium wigs to braided styles and care essentials, we bring together the beauty of
                            Nigerian fashion, craftsmanship and self-confidence in every look we curate.</p>
                        <a class="btn btn-primary" href="#contact">Book a consultation</a>
                    </div>
                </div>
            </section>

            <section class="reviews" id="reviews">
                <div class="section-header">
                    <div>
                        <p class="eyebrow">What clients say</p>
                        <h2>Queens are glowing</h2>
                    </div>
                </div>

                <div class="review-grid">
                    <article class="review-card">
                        <div class="stars">★★★★★</div>
                        <p>“My lace wig looked so natural and beautiful. I got compliments all weekend and the hair felt
                            premium from the first install.”</p>
                        <strong>Chinwe A.</strong>
                        <span>Lagos</span>
                    </article>

                    <article class="review-card">
                        <div class="stars">★★★★★</div>
                        <p>“The texture was exactly what I wanted, and the braids were neat, light and very stylish.
                            Looking like a queen every single day.”</p>
                        <strong>Amara O.</strong>
                        <span>Abuja</span>
                    </article>

                    <article class="review-card">
                        <div class="stars">★★★★★</div>
                        <p>“Beautiful quality and amazing customer service. The team understood the hairstyle I wanted
                            and delivered a stunning finish.”</p>
                        <strong>Grace K.</strong>
                        <span>Port Harcourt</span>
                    </article>
                </div>
            </section>
        </main>

        <footer class="footer" id="contact">
            <div class="footer-grid">
                <div>
                    <div class="brand" aria-label="Emerald Hair brand footer">
                        <span class="brand-mark">E</span>
                        <span class="brand-name"><strong>Emerald Hair</strong><small>Luxury hair studio</small></span>
                    </div>
                    <p>Premium Nigerian-inspired hair, beauty and styling for women seeking confidence, softness and
                        standout elegance.</p>
                </div>

                <div>
                    <h4>Collections</h4>
                    <a href="#collections">Luxury wigs</a>
                    <a href="#collections">Braids &amp; styles</a>
                    <a href="#catalog">Hair bundles</a>
                    <a href="#catalog">Hair care</a>
                </div>

                <div>
                    <h4>Support</h4>
                    <a href="#story">Our story</a>
                    <a href="#reviews">Customer reviews</a>
                    <a href="#contact">Book styling</a>
                    <a href="#contact">WhatsApp</a>
                </div>

                <div>
                    <h4>Contact</h4>
                    <a href="tel:+2348000000000">+234 800 000 0000</a>
                    <a href="mailto:hello@emeraldhair.com">hello@emeraldhair.com</a>
                    <span>Instagram: @emeraldhair</span>
                    <span>Lagos, Nigeria</span>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© 2026 Emerald Hair. All rights reserved.</span>
                <span>Luxury. Confidence. Glamour.</span>
            </div>
        </footer>
    </div>
</body>

</html>
