<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        /* =========================================================
           COLORES DEL SISTEMA
        ========================================================= */

        :root {
            /* NARANJA */
            --primary: #ff8c00;
            --primary-hover: #e07b00;

            /* AZUL */
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;

            /* FONDOS */
            --light-bg: #eef5fb;
            --card-bg: #ffffff;

            /* TEXTOS */
            --text-main: #172033;
            --text-muted: #64748b;

            /* BORDES */
            --border-soft: #dce7f1;

            /* RADIOS */
            --radius-xl: 22px;
            --radius-md: 15px;
            --radius-sm: 11px;

            /* SOMBRA */
            --shadow-soft: 0 8px 30px rgba(6,57,112,0.10);

            /* Colores auxiliares del dashboard */
            --accent-1: #0b84c6;
            --accent-2: #16a34a;
            --accent-3: #ff8c00;
            --accent-4: #06b6d4;
            --theme-shadow: rgba(6,57,112,.12);
        }

        /* =========================================================
           TEMAS DINÁMICOS DEL SISTEMA
        ========================================================= */

        .theme-ocean-orange {
            --primary: #ff8c00;
            --primary-hover: #e07b00;
            --dark-bg: #063970;
            --dark-bg-2: #0b84c6;
            --dark-bg-3: #042a54;
            --light-bg: #eef8fc;
            --text-main: #17324d;
            --text-muted: #637b91;
            --border-soft: #d8eaf3;
            --accent-1: #0b84c6;
            --accent-2: #16a34a;
            --accent-3: #ff8c00;
            --accent-4: #ffb347;
            --theme-shadow: rgba(6,57,112,.13);
        }

        .theme-lime-blue {
            --primary: #84cc16;
            --primary-hover: #65a30d;
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;
            --light-bg: #f2fbf3;
            --text-main: #173b32;
            --text-muted: #66827a;
            --border-soft: #dcefdc;
            --accent-1: #0b74b8;
            --accent-2: #22a06b;
            --accent-3: #84cc16;
            --accent-4: #f59e0b;
            --theme-shadow: rgba(34,160,107,.13);
        }

        .theme-purple-orange {
            --primary: #ff8c00;
            --primary-hover: #e07b00;
            --dark-bg: #4c1d95;
            --dark-bg-2: #7c3aed;
            --dark-bg-3: #3b176b;
            --light-bg: #f7f2ff;
            --text-main: #33284e;
            --text-muted: #75688e;
            --border-soft: #e8dcf6;
            --accent-1: #7c3aed;
            --accent-2: #a855f7;
            --accent-3: #ff8c00;
            --accent-4: #ec4899;
            --theme-shadow: rgba(76,29,149,.13);
        }

        .theme-sand-navy {
            --primary: #c98a52;
            --primary-hover: #ad7040;
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;
            --light-bg: #f7f1e9;
            --text-main: #42372c;
            --text-muted: #786b5e;
            --border-soft: #eadaca;
            --accent-1: #0b4f8a;
            --accent-2: #7b8f72;
            --accent-3: #c98a52;
            --accent-4: #d9ad78;
            --theme-shadow: rgba(201,138,82,.14);
        }

        .theme-teal-amber {
            --primary: #f59e0b;
            --primary-hover: #d98706;
            --dark-bg: #07575b;
            --dark-bg-2: #0f8b8d;
            --dark-bg-3: #064649;
            --light-bg: #eef9f8;
            --text-main: #183b3c;
            --text-muted: #617f80;
            --border-soft: #d4ece9;
            --accent-1: #0f8b8d;
            --accent-2: #14b8a6;
            --accent-3: #f59e0b;
            --accent-4: #f97316;
            --theme-shadow: rgba(15,139,141,.14);
        }

        .theme-wine-blue {
            --primary: #d94f70;
            --primary-hover: #bd3f5e;
            --dark-bg: #791837;
            --dark-bg-2: #3346a8;
            --dark-bg-3: #5d102a;
            --light-bg: #faf0f4;
            --text-main: #442837;
            --text-muted: #846675;
            --border-soft: #efd9e2;
            --accent-1: #3346a8;
            --accent-2: #7c4dff;
            --accent-3: #d94f70;
            --accent-4: #f59e0b;
            --theme-shadow: rgba(121,24,55,.14);
        }

        /* =========================================================
           COMPONENTES QUE HEREDAN EL TEMA
        ========================================================= */

        body {
            background-color: var(--light-bg) !important;
            color: var(--text-main);
        }

        .sidebar {
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--dark-bg-3) 100%) !important;
        }

        .logo-box,
        .user-avatar,
        #chatbot-bubble {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover)) !important;
            box-shadow: 0 6px 18px var(--theme-shadow) !important;
        }

        .nav-link.active {
            background: var(--primary) !important;
            box-shadow: 0 5px 16px var(--theme-shadow) !important;
        }

        .top-navbar,
        .card {
            box-shadow: 0 8px 30px var(--theme-shadow) !important;
        }

        .card-header,
        #chatbot-input-area,
        .chatbot-message.bot,
        .chatbot-products-table-wrapper,
        .chatbot-products-table td,
        .form-control,
        .form-select {
            border-color: var(--border-soft) !important;
        }

        .btn-primary,
        #chatbot-send,
        .chatbot-message.user,
        .chatbot-header-icon {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .btn-primary:hover,
        #chatbot-send:hover {
            background: var(--primary-hover) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--theme-shadow) !important;
        }

        #chatbot-header {
            background: linear-gradient(135deg, var(--dark-bg), var(--dark-bg-2)) !important;
        }

        #chatbot-messages {
            background: var(--light-bg) !important;
        }

        .chatbot-products-table th {
            background: var(--dark-bg-2) !important;
        }

        .chatbot-suggestion i,
        #chatbot-question-mark,
        .text-primary {
            color: var(--primary) !important;
        }

        /* Tarjetas principales del dashboard: ahora también cambian */
        .bg-gradient-blue {
            background: linear-gradient(135deg, var(--accent-1), var(--dark-bg-2)) !important;
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, var(--accent-2), var(--primary)) !important;
        }

        .bg-gradient-red {
            background: linear-gradient(135deg, var(--accent-3), var(--primary-hover)) !important;
        }

        .bg-gradient-cyan {
            background: linear-gradient(135deg, var(--accent-4), var(--accent-1)) !important;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
            font-size: 0.92rem;
            overflow-x: hidden;
        }


        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 260px;
            height: 100vh;

            position: fixed;

            top: 0;
            left: 0;

            background:
                linear-gradient(
                    180deg,
                    var(--dark-bg) 0%,
                    var(--dark-bg-3) 100%
                );

            color: white;

            z-index: 1050;

            display: flex;
            flex-direction: column;

            transition: transform .3s ease;

            overflow: hidden;
        }

        .sidebar-header {
            min-height: 82px;

            padding: 17px 18px;

            display: flex;
            align-items: center;

            gap: 12px;

            flex-shrink: 0;

            border-bottom:
                1px solid rgba(255,255,255,.08);
        }

        .logo-box {
            width: 46px;
            height: 46px;

            flex-shrink: 0;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff7200
                );

            color: white;

            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;

            box-shadow:
                0 6px 18px rgba(255,140,0,.40);
        }

        .brand-name {
            color: white;

            font-size: 17px;
            font-weight: 800;

            letter-spacing: -.4px;

            min-width: 0;
        }

        .sidebar-menu {
            flex: 1;

            padding: 12px 11px 25px;

            overflow-y: auto;

            scrollbar-width: thin;

            scrollbar-color:
                rgba(255,255,255,.18)
                transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.18);
            border-radius: 20px;
        }

        .menu-category {
            margin: 20px 11px 8px;

            color: rgba(255,255,255,.45);

            font-size: .64rem;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 1.2px;
        }

        .nav-link {
            min-height: 44px;

            margin-bottom: 3px;

            padding: 10px 13px;

            display: flex;
            align-items: center;

            color: rgba(255,255,255,.76);

            text-decoration: none;

            border-radius: 10px;

            font-size: .82rem;
            font-weight: 500;

            transition: all .18s ease;
        }

        .nav-link i {
            width: 22px;

            margin-right: 10px;

            text-align: center;

            font-size: 1.05rem;

            opacity: .85;
        }

        .nav-link:hover {
            color: white;

            background:
                rgba(255,255,255,.10);

            transform: translateX(2px);
        }

        .nav-link.active {
            color: white;

            background: var(--primary);

            box-shadow:
                0 5px 16px rgba(255,140,0,.30);
        }

        .nav-link.active i {
            opacity: 1;
        }


        /* =========================================================
           MAIN CONTENT
        ========================================================= */

        .main-content {
            margin-left: 260px;

            min-height: 100vh;

            padding: 22px 27px;

            display: flex;
            flex-direction: column;

            transition: margin-left .3s;
        }

        body.pos-page .main-content {
            padding: 0;

            height: 100vh;

            overflow: hidden;
        }


        /* =========================================================
           TOP NAVBAR
        ========================================================= */

        .top-navbar {
            min-height: 66px;

            margin-bottom: 22px;

            padding: 10px 17px;

            background: white;

            border-radius: var(--radius-md);

            box-shadow: var(--shadow-soft);

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;
        }

        .user-profile-btn {
            padding: 5px 9px;

            display: flex;
            align-items: center;

            gap: 10px;

            border:
                1px solid transparent;

            border-radius: 50px;

            cursor: pointer;

            transition: all .2s;
        }

        .user-profile-btn:hover {
            background: #f4f8fc;

            border-color: #dce7f1;
        }

        .user-avatar {
            width: 38px;
            height: 38px;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff6b35
                );

            color: white;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 800;

            box-shadow:
                0 3px 10px rgba(255,140,0,.20);
        }


        /* =========================================================
           COMPONENTES
        ========================================================= */

        .card {
            border: none;

            background: var(--card-bg);

            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-soft);

            overflow: hidden;
        }

        .card-header {
            padding: 1.3rem;

            background: transparent;

            border-bottom:
                1px solid #e1eaf3;
        }

        .card-body {
            padding: 1.3rem;
        }

        .btn {
            border: none;

            border-radius: 50px;

            padding: .58rem 1.25rem;

            font-size: .84rem;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);

            color: white;

            box-shadow:
                0 4px 12px rgba(255,140,0,.28);
        }

        .btn-primary:hover {
            background: var(--primary-hover);

            color: white;
        }

        .form-control,
        .form-select {
            padding: .72rem .95rem;

            background: #f7faff;

            border:
                1px solid #dce7f1;

            border-radius:
                var(--radius-sm);
        }

        .form-control:focus,
        .form-select:focus {
            border-color:
                var(--primary);

            background: white;

            box-shadow:
                0 0 0 3px rgba(255,140,0,.13);
        }

        .table {
            --bs-table-hover-bg: #f4f8fc;
        }

        .badge {
            border-radius: 50px;
        }


        /* =========================================================
           TARJETAS DASHBOARD
        ========================================================= */

        .card-solid {
            color: white !important;

            border-radius:
                var(--radius-xl);

            position: relative;

            overflow: hidden;
        }

        .bg-gradient-blue {
            background:
                linear-gradient(
                    135deg,
                    #3b82f6,
                    #2563eb
                ) !important;
        }

        .bg-gradient-green {
            background:
                linear-gradient(
                    135deg,
                    #10b981,
                    #059669
                ) !important;
        }

        .bg-gradient-red {
            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #b91c1c
                ) !important;
        }

        .bg-gradient-cyan {
            background:
                linear-gradient(
                    135deg,
                    #06b6d4,
                    #0891b2
                ) !important;
        }

        .card-solid h2 {
            margin: 10px 0;

            font-size: 2.3rem;

            font-weight: 800;
        }

        .card-solid .icon-bg {
            position: absolute;

            right: 20px;

            top: 50%;

            transform:
                translateY(-50%);

            font-size: 5rem;

            opacity: .15;

            pointer-events: none;
        }


        /* =========================================================
           MOBILE OVERLAY
        ========================================================= */

        .mobile-overlay {
            position: fixed;

            inset: 0;

            display: none;

            background:
                rgba(3,38,75,.76);

            backdrop-filter:
                blur(4px);

            z-index: 1040;
        }

        .mobile-overlay.show {
            display: block;
        }


        /* =========================================================
           CHATBOT
        ========================================================= */

        #chatbot-question-mark {
            position: fixed;

            right: 39px;
            bottom: 86px;

            width: 28px;
            height: 28px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: white;

            color: var(--primary);

            border-radius: 50%;

            font-size: 16px;
            font-weight: 800;

            box-shadow:
                0 5px 16px rgba(6,57,112,.18);

            z-index: 2001;

            pointer-events: none;

            animation:
                chatbotQuestionBlink
                1.3s infinite;
        }

        @keyframes chatbotQuestionBlink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .35;
                transform: scale(.85);
            }
        }

        #chatbot-bubble {
            position: fixed;

            right: 24px;
            bottom: 23px;

            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff7200
                );

            color: white;

            border-radius: 50%;

            font-size: 25px;

            cursor: pointer;

            box-shadow:
                0 8px 25px rgba(255,140,0,.38);

            z-index: 2000;

            transition:
                all .2s ease;
        }

        #chatbot-bubble:hover {
            transform:
                translateY(-2px)
                scale(1.04);

            box-shadow:
                0 11px 30px rgba(255,140,0,.45);
        }

        #chatbot-window {
            position: fixed;

            right: 24px;
            bottom: 94px;

            width: 400px;
            height: 550px;

            max-width:
                calc(100vw - 32px);

            max-height:
                calc(100vh - 125px);

            display: none;

            flex-direction: column;

            background: white;

            border-radius: 20px;

            box-shadow:
                0 18px 55px rgba(6,57,112,.24);

            overflow: hidden;

            z-index: 2000;
        }


        /* =========================================================
           HEADER CHATBOT
        ========================================================= */

        #chatbot-header {
            min-height: 70px;

            padding: 14px 16px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background:
                linear-gradient(
                    135deg,
                    #063970,
                    #0b4f8a
                );

            color: white;
        }

        .chatbot-header-left {
            display: flex;
            align-items: center;

            gap: 10px;
        }

        .chatbot-header-icon {
            width: 40px;
            height: 40px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                var(--primary);

            color: white;

            border-radius: 50%;

            font-size: 19px;

            box-shadow:
                0 4px 12px rgba(255,140,0,.25);
        }

        .chatbot-header-title {
            display: flex;
            flex-direction: column;

            line-height: 1.3;
        }

        .chatbot-header-title strong {
            font-size: 13.5px;
            font-weight: 700;
        }

        .chatbot-header-title span {
            margin-top: 1px;

            color:
                rgba(255,255,255,.72);

            font-size: 10.5px;

            font-weight: 400;
        }

        #chatbot-close {
            width: 32px;
            height: 32px;

            border: none;

            border-radius: 50%;

            background: transparent;

            color:
                rgba(255,255,255,.85);

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;

            transition: .2s;
        }

        #chatbot-close:hover {
            background:
                rgba(255,255,255,.13);

            color: white;
        }


        /* =========================================================
           MENSAJES CHATBOT
        ========================================================= */

        #chatbot-messages {
            flex: 1;

            padding: 16px;

            overflow-y: auto;

            background:
                #f2f7fc;
        }

        .chatbot-message {
            width: fit-content;

            max-width: 84%;

            margin-bottom: 9px;

            padding: 9px 12px;

            border-radius: 14px;

            font-size: 12.7px;

            line-height: 1.45;

            word-break: break-word;

            white-space: normal;
        }

        .chatbot-message.bot {
            margin-right: auto;

            background: white;

            color:
                var(--text-main);

            border:
                1px solid #dce7f1;

            border-bottom-left-radius:
                4px;

            box-shadow:
                0 2px 7px rgba(6,57,112,.06);
        }

        .chatbot-message.user {
            margin-left: auto;

            background:
                var(--primary);

            color: white;

            border-bottom-right-radius:
                4px;

            box-shadow:
                0 3px 9px rgba(255,140,0,.17);
        }


        /* =========================================================
           PREGUNTAS RÁPIDAS
        ========================================================= */

        #chatbot-suggestions {
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .chatbot-suggestions-title {
            margin: 0 0 7px 2px;

            color: #58728c;

            font-size: 10px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .6px;
        }

        .chatbot-suggestion {
            width: 100%;

            min-height: 34px;

            margin-bottom: 5px;

            padding: 7px 9px;

            display: flex;
            align-items: center;

            gap: 7px;

            background: white;

            color: #33475b;

            border:
                1px solid #dce7f1;

            border-radius: 9px;

            text-align: left;

            font-family: inherit;

            font-size: 10.8px;

            font-weight: 500;

            cursor: pointer;

            transition:
                all .17s ease;
        }

        .chatbot-suggestion i {
            width: 17px;

            text-align: center;

            color:
                var(--primary);

            font-size: 12px;
        }

        .chatbot-suggestion:hover {
            background: #fff5e8;

            border-color:
                rgba(255,140,0,.45);

            color: #d66f00;

            transform:
                translateX(2px);
        }


        /* =========================================================
           TABLA PRODUCTOS CHATBOT
        ========================================================= */

        .chatbot-products-table-wrapper {
            width: 100%;

            max-height: 310px;

            margin: 5px 0 10px;

            overflow: auto;

            background: white;

            border:
                1px solid #dce7f1;

            border-radius: 11px;
        }

        .chatbot-products-table {
            width: 100%;

            margin: 0;

            border-collapse:
                collapse;

            background: white;

            font-size: 11px;
        }

        .chatbot-products-table th {
            position: sticky;

            top: 0;

            z-index: 2;

            padding: 9px 7px;

            background:
                #0b4f8a;

            color: white;

            font-weight: 700;

            text-align: left;
        }

        .chatbot-products-table td {
            padding: 8px 7px;

            border-bottom:
                1px solid #e1eaf3;

            vertical-align:
                middle;
        }

        .chatbot-products-table tr:nth-child(even) {
            background:
                #f4f8fc;
        }

        .chatbot-products-table td:nth-child(2),
        .chatbot-products-table th:nth-child(2) {
            text-align: right;

            white-space: nowrap;
        }

        .chatbot-products-table td:nth-child(3),
        .chatbot-products-table th:nth-child(3) {
            text-align: center;

            white-space: nowrap;
        }

        .chatbot-stock-sin {
            color: #dc2626;
            font-weight: 700;
        }

        .chatbot-stock-bajo {
            color: #d97706;
            font-weight: 700;
        }

        .chatbot-stock-ok {
            color: #16a34a;
            font-weight: 700;
        }

        .chatbot-stock-null {
            color: #64748b;
            font-weight: 600;
        }


        /* =========================================================
           INPUT CHATBOT
        ========================================================= */

        #chatbot-input-area {
            padding: 11px;

            display: flex;
            align-items: center;

            gap: 8px;

            background: white;

            border-top:
                1px solid #dce7f1;
        }

        #chatbot-input {
            flex: 1;

            min-width: 0;

            height: 40px;

            padding: 9px 14px;

            border:
                1px solid #d9e5f0;

            border-radius: 50px;

            outline: none;

            background:
                #f7faff;

            font-family: inherit;

            font-size: 12px;
        }

        #chatbot-input:focus {
            border-color:
                var(--primary);

            background: white;

            box-shadow:
                0 0 0 3px rgba(255,140,0,.10);
        }

        #chatbot-send {
            width: 40px;
            height: 40px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;

            border-radius: 50%;

            background:
                var(--primary);

            color: white;

            font-size: 15px;

            cursor: pointer;

            transition: .2s;
        }

        #chatbot-send:hover {
            background:
                var(--primary-hover);
        }

        #chatbot-send:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .chatbot-loading {
            display: inline-flex;
            align-items: center;

            gap: 6px;
        }

        .chatbot-loading i {
            animation:
                chatbotSpin
                1s linear infinite;
        }

        @keyframes chatbotSpin {
            to {
                transform:
                    rotate(360deg);
            }
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 991px) {

            .sidebar {
                width: 250px;

                transform:
                    translateX(-100%);
            }

            .sidebar.show {
                transform:
                    translateX(0);
            }

            .main-content {
                margin-left: 0;

                padding: 15px;
            }
        }

        @media (max-width: 500px) {

            #chatbot-window {
                right: 10px;
                left: 10px;

                bottom: 82px;

                width: auto;

                height: 72vh;
            }

            #chatbot-bubble {
                right: 15px;
                bottom: 15px;
            }

            #chatbot-question-mark {
                right: 25px;
                bottom: 77px;
            }

            .chatbot-products-table {
                font-size: 10px;
            }
        }

    </style>

</head>


@php
    $dashboardTheme = \App\Models\Setting::where('key', 'dashboard_theme')->value('value') ?? 'ocean-orange';

    // Compatibilidad con el nombre anterior del tema.
    if ($dashboardTheme === 'ocean-coral') {
        $dashboardTheme = 'ocean-orange';
    }
@endphp

<body class="{{ request()->routeIs('pos.order') ? 'pos-page' : '' }} theme-{{ $dashboardTheme }}">


<div
    class="mobile-overlay"
    id="mobileOverlay"
    onclick="closeMenu()"
></div>


{{-- =============================================================
     SIDEBAR
============================================================= --}}

<div
    class="sidebar"
    id="sidebar"
>

    <div class="sidebar-header">

        @php
            $logo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        @endphp


        @if($logo)

            <img
                src="{{ asset('storage/'.$logo) }}"
                style="
                    width:46px;
                    height:46px;
                    object-fit:cover;
                    border-radius:14px;
                "
                alt="Logo"
            >

        @else

            <div class="logo-box">

                <i class="bi bi-shop"></i>

            </div>

        @endif


        <div class="brand-name text-truncate">

            {{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}

        </div>


        <button
            type="button"
            class="
                btn
                btn-sm
                text-white-50
                d-lg-none
                ms-auto
                p-1
            "
            onclick="closeMenu()"
        >

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    <div class="sidebar-menu">

        @php
            $role = Auth::user()->role;
        @endphp


        {{-- DASHBOARD --}}

        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('dashboard') }}"
                class="
                    nav-link
                    {{ request()->routeIs('dashboard') ? 'active' : '' }}
                "
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>

        @endif


        {{-- REPORTES --}}

        @if($role === 'admin')

            <a
                href="{{ route('reports.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('reports.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-bar-chart-line-fill"></i>

                Reportes

            </a>

        @endif


        {{-- =====================================================
             OPERACIONES
        ====================================================== --}}

        <div class="menu-category">

            Operaciones

        </div>


        <a
            href="{{ route('pos.index') }}"
            class="
                nav-link
                {{ request()->routeIs('pos.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-bag-check-fill"></i>

            Punto de Venta

        </a>


        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('delivery.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('delivery.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-bicycle"></i>

                Delivery

            </a>

        @endif


        <a
            href="{{ route('reservations.index') }}"
            class="
                nav-link
                {{ request()->routeIs('reservations.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-calendar-event-fill"></i>

            Reservas

        </a>


        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('sales.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('sales.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-receipt"></i>

                Historial de Ventas

            </a>

        @endif


        <a
            href="{{ route('kitchen.index') }}"
            class="
                nav-link
                {{ request()->routeIs('kitchen.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-fire"></i>

            Cocina (KDS)

        </a>


        {{-- =====================================================
             CAJA
        ====================================================== --}}

        @if(in_array($role, ['admin', 'cashier']))

            <div class="menu-category">

                Caja / Arqueo

            </div>


            @if(Auth::user()->activeCashRegister)

                <a
                    href="{{ route('cash_registers.close') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.close') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-box-arrow-left"></i>

                    Cerrar Caja

                </a>

            @else

                <a
                    href="{{ route('cash_registers.create') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.create') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    Abrir Caja

                </a>

            @endif


            @if($role === 'admin')

                <a
                    href="{{ route('cash_registers.index') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.index') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-clock-history"></i>

                    Historial de Turnos

                </a>

            @endif

        @endif


        {{-- =====================================================
             FACTURACIÓN ELECTRÓNICA
        ====================================================== --}}

        @if($role === 'admin')

            <div class="menu-category">

                Facturación Electrónica

            </div>


            <a
                href="{{ route('billing.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('billing.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-receipt-cutoff"></i>

                Comprobantes

            </a>


            <a
                href="{{ route('credit_notes.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('credit_notes.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-arrow-counterclockwise"></i>

                Notas de Crédito

            </a>


            <a
                href="{{ route('daily_summaries.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('daily_summaries.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-calendar-week"></i>

                Resumen Diario

            </a>

        @endif


        {{-- =====================================================
             GESTIÓN
        ====================================================== --}}

        <div class="menu-category">

            Gestión

        </div>


        <a
            href="{{ route('clients.index') }}"
            class="
                nav-link
                {{ request()->routeIs('clients.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-people-fill"></i>

            Clientes

        </a>


        @if($role === 'admin')

            <a
                href="{{ route('categories.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('categories.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-tags-fill"></i>

                Categorías

            </a>


            <a
                href="{{ route('products.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('products.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-box-seam-fill"></i>

                Inventario

            </a>


            <a
                href="{{ route('menu.index') }}"
                target="_blank"
                class="nav-link"
            >

                <i class="bi bi-qr-code-scan"></i>

                Carta Digital


                <i
                    class="
                        bi
                        bi-box-arrow-up-right
                        ms-auto
                    "
                    style="
                        margin-right:0;
                        font-size:.68rem;
                    "
                ></i>

            </a>


            <a
                href="{{ route('tables.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('tables.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-grid-3x3-gap-fill"></i>

                Mesas

            </a>


            <a
                href="{{ route('users.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('users.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-person-badge-fill"></i>

                Personal / Usuarios

            </a>


            <a
                href="{{ route('settings.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('settings.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-gear-fill"></i>

                Configuración

            </a>


            <a
                href="{{ route('system.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('system.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-tools"></i>

                Mantenimiento

            </a>

        @endif

    </div>

</div>


{{-- =============================================================
     CONTENIDO PRINCIPAL
============================================================= --}}

<div class="main-content">


    @if(!request()->routeIs('pos.order'))

        <div class="top-navbar">


            <div class="d-flex align-items-center gap-3">


                <button
                    type="button"
                    class="
                        btn
                        btn-light
                        border
                        d-lg-none
                        px-2
                        py-1
                    "
                    onclick="openMenu()"
                >

                    <i class="bi bi-list fs-5"></i>

                </button>


                <h5 class="fw-bold mb-0 text-dark d-none d-sm-block">


                    @if(request()->routeIs('dashboard'))

                        Panel de Control


                    @elseif(request()->routeIs('pos.*'))

                        Punto de Venta


                    @elseif(request()->routeIs('delivery.*'))

                        Delivery


                    @elseif(request()->routeIs('products.*'))

                        Inventario


                    @elseif(request()->routeIs('sales.*'))

                        Ventas y Movimientos


                    @elseif(request()->routeIs('billing.*'))

                        Facturación Electrónica


                    @elseif(request()->routeIs('credit_notes.*'))

                        Notas de Crédito


                    @elseif(request()->routeIs('daily_summaries.*'))

                        Resumen Diario


                    @elseif(request()->routeIs('cash_registers.*'))

                        Caja / Arqueo


                    @elseif(request()->routeIs('reservations.*'))

                        Reservas


                    @elseif(request()->routeIs('kitchen.*'))

                        Cocina


                    @elseif(request()->routeIs('categories.*'))

                        Categorías


                    @elseif(request()->routeIs('users.*'))

                        Gestión de Personal


                    @elseif(request()->routeIs('settings.*'))

                        Configuración


                    @else

                        Sistema de Restaurante

                    @endif


                </h5>

            </div>


            {{-- USUARIO --}}

            <div class="dropdown">


                <div
                    class="user-profile-btn"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >


                    <div class="text-end d-none d-sm-block">


                        <div class="fw-bold text-dark small">

                            {{ Auth::user()->name }}

                        </div>


                        <div
                            class="text-muted"
                            style="font-size:.68rem;"
                        >

                            {{ ucfirst(Auth::user()->role) }}

                        </div>


                    </div>


                    <div class="user-avatar">

                        {{ substr(Auth::user()->name, 0, 1) }}

                    </div>


                    <i
                        class="
                            bi
                            bi-chevron-down
                            text-muted
                            small
                        "
                    ></i>

                </div>


                <ul
                    class="
                        dropdown-menu
                        dropdown-menu-end
                        border-0
                        shadow-lg
                        p-2
                        rounded-4
                    "
                    style="width:220px;"
                >


                    <li
                        class="
                            px-2
                            py-1
                            text-muted
                            small
                            fw-bold
                        "
                    >

                        MI CUENTA

                    </li>


                    @if($role === 'admin')

                        <li>

                            <button
                                type="button"
                                class="
                                    dropdown-item
                                    rounded-3
                                    mb-1
                                "
                                data-bs-toggle="modal"
                                data-bs-target="#profileModal"
                            >

                                <i
                                    class="
                                        bi
                                        bi-person-gear
                                        me-2
                                        text-primary
                                    "
                                ></i>

                                Editar Perfil

                            </button>

                        </li>

                    @endif


                    <li>

                        <hr class="dropdown-divider">

                    </li>


                    <li>

                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                        >

                            @csrf


                            <button
                                type="submit"
                                class="
                                    dropdown-item
                                    rounded-3
                                    text-danger
                                    fw-bold
                                "
                            >

                                <i
                                    class="
                                        bi
                                        bi-box-arrow-right
                                        me-2
                                    "
                                ></i>

                                Cerrar Sesión

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    @endif


    {{-- =====================================================
         MENSAJES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="
                alert
                border-0
                shadow-sm
                rounded-4
                mb-4
                d-flex
                align-items-center
            "
            style="
                background:#f0fdf4;
                border-left:
                    4px solid #22c55e !important;
            "
        >

            <i
                class="
                    bi
                    bi-check-circle-fill
                    fs-4
                    me-3
                    text-success
                "
            ></i>


            <div>

                <strong>¡Éxito!</strong>

                {{ session('success') }}

            </div>


            <button
                type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div
            class="
                alert
                border-0
                shadow-sm
                rounded-4
                mb-4
                d-flex
                align-items-center
            "
            style="
                background:#fff1f2;
                border-left:
                    4px solid #f43f5e !important;
            "
        >

            <i
                class="
                    bi
                    bi-exclamation-triangle-fill
                    fs-4
                    me-3
                    text-danger
                "
            ></i>


            <div>

                <strong>Error:</strong>

                {{ session('error') }}

            </div>


            <button
                type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @yield('content')

</div>


{{-- =============================================================
     PERFIL
============================================================= --}}

@if($role === 'admin')

    <div
        class="modal fade"
        id="profileModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div
            class="
                modal-dialog
                modal-dialog-centered
            "
        >

            <div
                class="
                    modal-content
                    border-0
                    shadow-lg
                    rounded-4
                "
            >

                <div
                    class="
                        modal-header
                        border-bottom-0
                        pb-0
                    "
                >

                    <h5
                        class="
                            modal-title
                            fw-bold
                        "
                    >

                        Mi Perfil

                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body pt-2">


                    <p class="text-muted small mb-3">

                        Actualiza tus datos de acceso.

                    </p>


                    <form
                        action="{{ route('users.update', Auth::user()->id) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PUT')


                        <div class="mb-3">

                            <label
                                class="
                                    form-label
                                    fw-bold
                                    small
                                "
                            >

                                Nombre

                            </label>


                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ Auth::user()->name }}"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label
                                class="
                                    form-label
                                    fw-bold
                                    small
                                "
                            >

                                Correo

                            </label>


                            <input
                                type="email"
                                class="
                                    form-control
                                    bg-light
                                "
                                value="{{ Auth::user()->email }}"
                                readonly
                            >

                        </div>


                        <hr>


                        <div class="mb-3">

                            <label
                                class="
                                    form-label
                                    fw-bold
                                    small
                                "
                            >

                                Nueva Contraseña

                            </label>


                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Dejar en blanco para no cambiar"
                            >

                        </div>


                        <div class="d-grid">

                            <button
                                type="submit"
                                class="
                                    btn
                                    btn-primary
                                    fw-bold
                                "
                            >

                                Guardar Cambios

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endif


{{-- =============================================================
     CHATBOT ADMIN
============================================================= --}}

@auth

    @if(Auth::user()->role === 'admin')


        <div id="chatbot-question-mark">?</div>


        <div
            id="chatbot-bubble"
            title="Asistente del restaurante"
        >

            <i class="bi bi-robot"></i>

        </div>


        <div id="chatbot-window">


            <div id="chatbot-header">


                <div class="chatbot-header-left">


                    <div class="chatbot-header-icon">

                        <i class="bi bi-robot"></i>

                    </div>


                    <div class="chatbot-header-title">

                        <strong>

                            Asistente de El Capitán

                        </strong>

                        <span>

                            Consultas del restaurante

                        </span>

                    </div>


                </div>


                <button
                    type="button"
                    id="chatbot-close"
                    title="Cerrar"
                >

                    <i class="bi bi-x-lg"></i>

                </button>


            </div>


            <div id="chatbot-messages">


                <div class="chatbot-message bot">Hola, administrador. ¿Qué deseas consultar?</div>


                <div id="chatbot-suggestions">


                    <div class="chatbot-suggestions-title">

                        Preguntas rápidas

                    </div>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuánto vendimos hoy?"
                    >

                        <i class="bi bi-cash-stack"></i>

                        <span>

                            ¿Cuánto vendimos hoy?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuánto vendimos esta semana?"
                    >

                        <i class="bi bi-graph-up"></i>

                        <span>

                            ¿Cuánto vendimos esta semana?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuántos pedidos tenemos hoy?"
                    >

                        <i class="bi bi-receipt"></i>

                        <span>

                            ¿Cuántos pedidos tenemos hoy?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuál es el producto más vendido?"
                    >

                        <i class="bi bi-trophy"></i>

                        <span>

                            ¿Cuál es el producto más vendido?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Qué productos están sin stock?"
                    >

                        <i class="bi bi-box-seam"></i>

                        <span>

                            ¿Qué productos están sin stock?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Qué productos tienen poco stock?"
                    >

                        <i class="bi bi-exclamation-triangle"></i>

                        <span>

                            ¿Qué productos tienen poco stock?

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="Muéstrame los productos"
                    >

                        <i class="bi bi-list-ul"></i>

                        <span>

                            Muéstrame los productos

                        </span>

                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cómo puedo mejorar las ventas?"
                    >

                        <i class="bi bi-lightbulb"></i>

                        <span>

                            ¿Cómo puedo mejorar las ventas?

                        </span>

                    </button>


                </div>

            </div>


            <div id="chatbot-input-area">


                <input
                    type="text"
                    id="chatbot-input"
                    placeholder="Escribe tu pregunta..."
                    autocomplete="off"
                >


                <button
                    type="button"
                    id="chatbot-send"
                    title="Enviar"
                >

                    <i class="bi bi-send-fill"></i>

                </button>


            </div>

        </div>

    @endif

@endauth


{{-- =============================================================
     SCRIPTS
============================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
></script>

<script
    src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
></script>


<script>

    function openMenu() {

        document
            .getElementById('sidebar')
            ?.classList
            .add('show');


        document
            .getElementById('mobileOverlay')
            ?.classList
            .add('show');


        document.body.style.overflow =
            'hidden';

    }


    function closeMenu() {

        document
            .getElementById('sidebar')
            ?.classList
            .remove('show');


        document
            .getElementById('mobileOverlay')
            ?.classList
            .remove('show');


        document.body.style.overflow =
            'auto';

    }


    document
        .querySelectorAll('.alert')
        .forEach(
            alertElement => {

                setTimeout(
                    () => {

                        alertElement.style.transition =
                            'opacity .5s';


                        alertElement.style.opacity =
                            '0';


                        setTimeout(
                            () => {
                                alertElement.remove();
                            },
                            500
                        );

                    },
                    5000
                );

            }
        );

</script>


@auth

    @if(Auth::user()->role === 'admin')

        <script>


            const chatbotBubble =
                document.getElementById(
                    'chatbot-bubble'
                );


            const chatbotWindow =
                document.getElementById(
                    'chatbot-window'
                );


            const chatbotClose =
                document.getElementById(
                    'chatbot-close'
                );


            const chatbotInput =
                document.getElementById(
                    'chatbot-input'
                );


            const chatbotSend =
                document.getElementById(
                    'chatbot-send'
                );


            const chatbotMessages =
                document.getElementById(
                    'chatbot-messages'
                );


            const chatbotQuestionMark =
                document.getElementById(
                    'chatbot-question-mark'
                );


            const chatbotSuggestionButtons =
                document.querySelectorAll(
                    '.chatbot-suggestion'
                );


            function scrollChatbotBottom() {

                if (!chatbotMessages) {
                    return;
                }


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;

            }


            chatbotBubble?.addEventListener(
                'click',
                function () {


                    if (!chatbotWindow) {
                        return;
                    }


                    chatbotWindow.style.display =
                        'flex';


                    if (chatbotQuestionMark) {

                        chatbotQuestionMark.style.display =
                            'none';

                    }


                    setTimeout(
                        () => {

                            chatbotInput?.focus();

                        },
                        100
                    );


                    scrollChatbotBottom();

                }
            );


            chatbotClose?.addEventListener(
                'click',
                function () {


                    if (chatbotWindow) {

                        chatbotWindow.style.display =
                            'none';

                    }


                    if (chatbotQuestionMark) {

                        chatbotQuestionMark.style.display =
                            'flex';

                    }

                }
            );


            function addChatbotMessage(
                text,
                type
            ) {


                if (!chatbotMessages) {
                    return;
                }


                const message =
                    document.createElement(
                        'div'
                    );


                message.className =
                    `chatbot-message ${type}`;


                message.textContent =
                    String(
                        text ?? ''
                    ).trim();


                chatbotMessages.appendChild(
                    message
                );


                scrollChatbotBottom();

            }


            function addChatbotProductsTable(
                products
            ) {


                if (
                    !chatbotMessages ||
                    !Array.isArray(products) ||
                    products.length === 0
                ) {

                    return;

                }


                const wrapper =
                    document.createElement(
                        'div'
                    );


                wrapper.className =
                    'chatbot-products-table-wrapper';


                const table =
                    document.createElement(
                        'table'
                    );


                table.className =
                    'chatbot-products-table';


                const thead =
                    document.createElement(
                        'thead'
                    );


                const headerRow =
                    document.createElement(
                        'tr'
                    );


                [
                    'Producto',
                    'Precio',
                    'Stock'
                ].forEach(
                    label => {


                        const th =
                            document.createElement(
                                'th'
                            );


                        th.textContent =
                            label;


                        headerRow.appendChild(
                            th
                        );


                    }
                );


                thead.appendChild(
                    headerRow
                );


                const tbody =
                    document.createElement(
                        'tbody'
                    );


                products.forEach(
                    product => {


                        const row =
                            document.createElement(
                                'tr'
                            );


                        const nameCell =
                            document.createElement(
                                'td'
                            );


                        nameCell.textContent =
                            product.name ??
                            product.nombre ??
                            '-';


                        const priceCell =
                            document.createElement(
                                'td'
                            );


                        const rawPrice =
                            product.price ??
                            product.precio;


                        const price =
                            Number(rawPrice);


                        priceCell.textContent =
                            'S/ ' +
                            (
                                Number.isFinite(price)
                                    ? price.toFixed(2)
                                    : '0.00'
                            );


                        const stockCell =
                            document.createElement(
                                'td'
                            );


                        const stock =
                            product.stock;


                        if (
                            stock === null ||
                            stock === undefined ||
                            stock === ''
                        ) {


                            stockCell.textContent =
                                'No registrado';


                            stockCell.className =
                                'chatbot-stock-null';

                        }

                        else {


                            const stockNumber =
                                Number(stock);


                            if (
                                Number.isFinite(stockNumber) &&
                                stockNumber === 0
                            ) {


                                stockCell.textContent =
                                    'Sin stock';


                                stockCell.className =
                                    'chatbot-stock-sin';

                            }

                            else if (
                                Number.isFinite(stockNumber) &&
                                stockNumber <= 5
                            ) {


                                stockCell.textContent =
                                    `${stockNumber} (Bajo)`;


                                stockCell.className =
                                    'chatbot-stock-bajo';

                            }

                            else {


                                stockCell.textContent =
                                    Number.isFinite(
                                        stockNumber
                                    )
                                        ? stockNumber
                                        : stock;


                                stockCell.className =
                                    'chatbot-stock-ok';

                            }

                        }


                        row.appendChild(
                            nameCell
                        );


                        row.appendChild(
                            priceCell
                        );


                        row.appendChild(
                            stockCell
                        );


                        tbody.appendChild(
                            row
                        );


                    }
                );


                table.appendChild(
                    thead
                );


                table.appendChild(
                    tbody
                );


                wrapper.appendChild(
                    table
                );


                chatbotMessages.appendChild(
                    wrapper
                );


                scrollChatbotBottom();

            }


            async function sendChatbotMessage() {


                if (
                    !chatbotInput ||
                    !chatbotSend ||
                    !chatbotMessages
                ) {

                    return;

                }


                const message =
                    chatbotInput
                        .value
                        .trim();


                if (!message) {
                    return;
                }


                addChatbotMessage(
                    message,
                    'user'
                );


                chatbotInput.value =
                    '';


                chatbotInput.disabled =
                    true;


                chatbotSend.disabled =
                    true;


                const loadingMessage =
                    document.createElement(
                        'div'
                    );


                loadingMessage.className =
                    'chatbot-message bot';


                loadingMessage.innerHTML =
                    `
                        <span class="chatbot-loading">
                            <i class="bi bi-arrow-repeat"></i>
                            Procesando...
                        </span>
                    `;


                chatbotMessages.appendChild(
                    loadingMessage
                );


                scrollChatbotBottom();


                try {


                    const response =
                        await fetch(
                            '{{ route('chatbot.chat') }}',
                            {

                                method:
                                    'POST',


                                headers: {

                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute(
                                                'content'
                                            )

                                },


                                body:
                                    JSON.stringify({
                                        message: message
                                    })

                            }
                        );


                    let data = {};


                    try {

                        data =
                            await response.json();

                    }

                    catch (jsonError) {

                        console.error(
                            'Respuesta no JSON:',
                            jsonError
                        );

                    }


                    loadingMessage.remove();


                    if (!response.ok) {


                        addChatbotMessage(
                            data.response ??
                            data.message ??
                            'Ocurrió un error al procesar la consulta.',
                            'bot'
                        );


                        return;

                    }


                    addChatbotMessage(
                        data.response ??
                        data.message ??
                        'No se recibió una respuesta.',
                        'bot'
                    );


                    if (
                        Array.isArray(
                            data.products
                        ) &&
                        data.products.length > 0
                    ) {


                        addChatbotProductsTable(
                            data.products
                        );

                    }


                }

                catch (error) {


                    loadingMessage.remove();


                    console.error(
                        'Error del chatbot:',
                        error
                    );


                    addChatbotMessage(
                        'No pude comunicarme con el servidor. Inténtalo nuevamente.',
                        'bot'
                    );


                }

                finally {


                    chatbotInput.disabled =
                        false;


                    chatbotSend.disabled =
                        false;


                    chatbotInput.focus();

                }

            }


            chatbotSend?.addEventListener(
                'click',
                sendChatbotMessage
            );


            chatbotInput?.addEventListener(
                'keydown',
                function (event) {


                    if (
                        event.key === 'Enter'
                    ) {


                        event.preventDefault();


                        sendChatbotMessage();

                    }


                    if (
                        event.key === 'Escape'
                    ) {


                        if (chatbotWindow) {

                            chatbotWindow.style.display =
                                'none';

                        }


                        if (chatbotQuestionMark) {

                            chatbotQuestionMark.style.display =
                                'flex';

                        }

                    }

                }
            );


            chatbotSuggestionButtons.forEach(
                button => {


                    button.addEventListener(
                        'click',
                        function () {


                            const question =
                                this.dataset.question;


                            if (
                                !question ||
                                !chatbotInput
                            ) {

                                return;

                            }


                            chatbotInput.value =
                                question;


                            sendChatbotMessage();

                        }
                    );


                }
            );


        </script>

    @endif

@endauth


@stack('scripts')

</body>
</html>