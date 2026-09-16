<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Carta Digital — {{ $companyName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff8c00;
            --primary-light: #ffebcc;
            --dark: #1e1e2d;
            --gray-bg: #f8f9fa;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--gray-bg);
            color: #333;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 80px;
        }

        /* HEADER / HERO */
        .hero {
            background: linear-gradient(135deg, var(--dark) 0%, #34344f 100%);
            color: white;
            padding: 42px 20px 58px;
            text-align: center;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,.12);
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 22px;
            transform: translateX(-50%);
            width: 46px;
            height: 3px;
            border-radius: 10px;
            background: var(--primary);
        }
        .hero-logo {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 22px;
            box-shadow: 0 10px 25px rgba(0,0,0,.28);
            margin-bottom: 17px;
            background: white;
            padding: 5px;
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-weight: 800;
            font-size: clamp(1.65rem, 5vw, 2.15rem);
            margin-bottom: 7px;
            letter-spacing: -.6px;
            line-height: 1.15;
            position: relative;
            z-index: 1;
        }
        .hero p {
            font-weight: 400;
            opacity: .78;
            font-size: .9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        /* DISTRIBUCION RESPONSIVE DE PRODUCTOS */
        @media (min-width: 768px) {
            section {
                max-width: 1280px;
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                padding-left: 24px;
                padding-right: 24px;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            section .section-title {
                grid-column: 1 / -1;
            }

            section .product-card {
            background: #fff;
            border-radius: 16px;
            margin: 0;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 118px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, .06);
            border: 1px solid rgba(15, 23, 42, .05);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            position: relative;
            overflow: hidden;
        }
        }

        @media (min-width: 1200px) {
            section {
                max-width: 1180px;
                gap: 18px;
            }
        }
        /* BUSCADOR DE PLATOS */
        .menu-search-wrapper {
            max-width: 720px;
            margin: -22px auto 18px;
            padding: 0 20px;
            position: relative;
            z-index: 110;
        }

        .menu-search {
            height: 52px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            background: #fff;
            border: 1px solid #eceef1;
            border-radius: 16px;
            box-shadow: 0 7px 24px rgba(0, 0, 0, .10);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .menu-search:focus-within {
            border-color: var(--primary);
            box-shadow: 0 8px 26px rgba(255, 140, 0, .16);
        }

        .menu-search > i {
            color: var(--primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .menu-search input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--dark);
            font-family: inherit;
            font-size: .92rem;
        }

        .menu-search input::placeholder {
            color: #9ca3af;
        }

        .search-clear {
            width: 30px;
            height: 30px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            border-radius: 9px;
            background: #f1f3f5;
            color: #6b7280;
            cursor: pointer;
        }

        .search-clear.visible {
            display: inline-flex;
        }

        .search-clear:hover {
            background: #e5e7eb;
            color: var(--dark);
        }

        .search-message {
            display: none;
            padding: 12px 4px 0;
            text-align: center;
            color: #6b7280;
            font-size: .82rem;
            font-weight: 500;
        }
        /* HORIZONTAL NAV */
        .category-nav {
            display: flex;
            overflow-x: auto;
            gap: 9px;
            padding: 10px 20px 12px;
            margin-top: -25px;
            scrollbar-width: none;
            position: relative;
        }
        .category-nav::-webkit-scrollbar { display: none; } /* Chrome */
        .category-pill {
            background: rgba(255,255,255,.96);
            color: #475569;
            padding: 9px 16px;
            border-radius: 13px;
            font-weight: 650;
            font-size: .82rem;
            white-space: nowrap;
            box-shadow: 0 3px 12px rgba(15,23,42,.06);
            text-decoration: none;
            transition: all .2s ease;
            border: 1px solid #e5e7eb;
        }

        .category-pill:hover {
            color: var(--dark);
            border-color: #d1d5db;
            transform: translateY(-1px);
        }
        .category-pill.active,
.category-pill:active {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
            box-shadow: 0 4px 14px rgba(194,65,12,.10);
        }

        /* SECTION TITLES */
        .section-title {
            font-weight: 800;
            font-size: 1.18rem;
            color: var(--dark);
            margin: 32px 20px 15px;
            display: flex;
            align-items: center;
            gap: 9px;
            letter-spacing: -.2px;
            position: relative;
        }

        .section-title::after {
            content: "";
            width: 28px;
            height: 3px;
            border-radius: 10px;
            background: var(--primary);
            margin-left: 2px;
        }

        /* PRODUCT CARD */
        .product-card {
            background: #fff;
            border-radius: 16px;
            margin: 0;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 118px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, .06);
            border: 1px solid rgba(15, 23, 42, .05);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            position: relative;
            overflow: hidden;
        }
        .product-card:active {
            transform: scale(.985);
        }

        @media (hover: hover) and (pointer: fine) {
            .product-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 24px rgba(15, 23, 42, .10);
                border-color: rgba(255, 140, 0, .18);
            }
        }
        .product-img {
            width: 88px;
            height: 88px;
            border-radius: 13px;
            object-fit: cover;
            background: #f1f3f5;
            flex-shrink: 0;
        }
        .product-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .product-name {
            font-weight: 750;
            font-size: 1rem;
            margin-bottom: 4px;
            color: var(--dark);
            line-height: 1.25;
        }
        .product-desc {
            font-size: .78rem;
            color: #6b7280;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
        }
        .product-price-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: auto;
        }
        .price {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--dark);
            line-height: 1.1;
        }
        .price.promo {
            color: #e53935;
        }
        .price-old {
            font-size: 0.85rem;
            color: #aaa;
            text-decoration: line-through;
            font-weight: 500;
        }

        /* BADGES */
        .badge-custom {
            position: absolute;
            top: 0;
            right: 0;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 700;
            border-bottom-left-radius: 15px;
            border-top-right-radius: 20px;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-promo { background: #e53935; color: white; }
        .badge-chef { background: var(--dark); color: #ffd700; }
        .badge-new { background: #2e7d32; color: white; }
        .badge-popular { background: var(--primary); color: white; }

        /* SCROLL SPY OFFSET */
        section {
            scroll-margin-top: 80px;
        }
    </style>
</head>
<body>

    <!-- HERO -->
    <div class="hero">
        @if($companyLogo)
            <img src="{{ asset('storage/'.$companyLogo) }}" class="hero-logo" alt="Logo">
        @else
            <div class="hero-logo d-flex align-items-center justify-content-center mx-auto" style="background:#fff; color:var(--primary); font-size: 30px;">
                <i class="bi bi-shop"></i>
            </div>
        @endif
        <h1>{{ $companyName }}</h1>
        <p>Explora nuestra deliciosa carta digital</p>
    </div>

    <!-- BUSCADOR DE PLATOS -->
    <div class="menu-search-wrapper">
        <div class="menu-search">
            <i class="bi bi-search"></i>
            <input type="text" id="menuSearch" placeholder="Buscar ceviche, dúo, bebida..." autocomplete="off">
            <button type="button" id="clearSearch" class="search-clear" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="searchMessage" class="search-message"></div>
    </div>

    <!-- CATEGORY NAVIGATION -->
    <div class="category-nav sticky-top" style="top: 15px; z-index: 100;">
        @if($promotions->count() > 0)
            <a href="#promo" class="category-pill active"><i class="bi bi-tag-fill text-danger me-1"></i> Ofertas</a>
        @endif
        @if($chefRecommendations->count() > 0)
            <a href="#chef" class="category-pill"><i class="bi bi-star-fill text-warning me-1"></i> Sugerencias</a>
        @endif
        @if($mostPopular->count() > 0)
            <a href="#popular" class="category-pill"><i class="bi bi-fire text-danger me-1"></i> Favoritos</a>
        @endif
        @foreach($categories as $category)
            @if($category->products->count() > 0)
                <a href="#cat-{{ $category->id }}" class="category-pill">{{ $category->name }}</a>
            @endif
        @endforeach
    </div>

    <!-- 1. PROMOCIONES -->
    @if($promotions->count() > 0)
        <section id="promo">
            <div class="section-title">
                <i class="bi bi-tags-fill text-danger"></i> Ofertas Especiales
            </div>
            @foreach($promotions as $product)
                @include('menu.partials.product-card', ['product' => $product, 'badge' => 'promo', 'badgeText' => 'OFERTA'])
            @endforeach
        </section>
    @endif

    <!-- 2. RECOMENDACIÓN DEL CHEF -->
    @if($chefRecommendations->count() > 0)
        <section id="chef">
            <div class="section-title">
                <i class="bi bi-star-fill text-warning"></i> Sugerencias del Chef
            </div>
            @foreach($chefRecommendations as $product)
                @include('menu.partials.product-card', ['product' => $product, 'badge' => 'chef', 'badgeText' => 'Sugerencia'])
            @endforeach
        </section>
    @endif

    <!-- 3. LO MÁS PEDIDO -->
    @if($mostPopular->count() > 0)
        <section id="popular">
            <div class="section-title">
                <i class="bi bi-fire text-danger"></i> Los Favoritos
            </div>
            @foreach($mostPopular as $product)
                @include('menu.partials.product-card', ['product' => $product, 'badge' => 'popular', 'badgeText' => 'Top Ventas'])
            @endforeach
        </section>
    @endif

    <!-- 4. NUEVOS PLATOS -->
    @if($newProducts->count() > 0)
        <section id="nuevos">
            <div class="section-title">
                <i class="bi bi-stars text-success"></i> Novedades
            </div>
            @foreach($newProducts as $product)
                @include('menu.partials.product-card', ['product' => $product, 'badge' => 'new', 'badgeText' => 'NUEVO'])
            @endforeach
        </section>
    @endif

    <!-- 5. CARTA COMPLETA POR CATEGORÍAS -->
    @foreach($categories as $category)
        @if($category->products->count() > 0)
            <section id="cat-{{ $category->id }}">
                <div class="section-title">
                    {{ $category->name }}
                </div>
                @foreach($category->products as $product)
                    @include('menu.partials.product-card', ['product' => $product, 'badge' => null])
                @endforeach
            </section>
        @endif
    @endforeach

    <div class="text-center mt-5 mb-4 opacity-50" style="font-size: 0.8rem;">
        <i class="bi bi-qr-code-scan mb-2 d-block fs-3"></i>
        Carta Digital &copy; {{ date('Y') }} {{ $companyName }}
    </div>

    <!-- Script for smooth scroll & active pill state -->
    <script>
        // BUSCADOR DE LA CARTA DIGITAL
        const menuSearch = document.getElementById('menuSearch');
        const clearSearch = document.getElementById('clearSearch');
        const searchMessage = document.getElementById('searchMessage');

        function normalizeText(text) {
            return text
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function filterMenu() {
            const query = normalizeText(menuSearch.value.trim());
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.querySelector('.product-name')?.textContent || '';
                const description = card.querySelector('.product-desc')?.textContent || '';
                const searchableText = normalizeText(name + ' ' + description);
                const matches = query === '' || searchableText.includes(query);

                card.style.display = matches ? '' : 'none';

                if (matches) {
                    visibleCount++;
                }
            });

            document.querySelectorAll('section').forEach(section => {
                const visibleCards = Array.from(section.querySelectorAll('.product-card'))
                    .some(card => card.style.display !== 'none');

                section.style.display = visibleCards ? '' : 'none';
            });

            clearSearch.classList.toggle('visible', query !== '');

            if (query !== '') {
                searchMessage.style.display = 'block';
                searchMessage.textContent = visibleCount === 0
                    ? 'No encontramos platos con esa búsqueda.'
                    : `${visibleCount} ${visibleCount === 1 ? 'plato encontrado' : 'platos encontrados'}`;
            } else {
                searchMessage.style.display = 'none';
                searchMessage.textContent = '';
            }
        }

        menuSearch.addEventListener('input', filterMenu);

        clearSearch.addEventListener('click', function () {
            menuSearch.value = '';
            filterMenu();
            menuSearch.focus();
        });
        document.querySelectorAll('.category-pill').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if(targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
