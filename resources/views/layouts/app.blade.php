<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BoulangeriePro CI') — {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            /* Fond blanc */
            --bg-page:      #F4F5F7;
            --bg-white:     #FFFFFF;
            --bg-surface:   #F8F9FB;

            /* Noir */
            --noir:         #111111;
            --noir-soft:    #1C1C1C;
            --noir-text:    #222222;
            --noir-mid:     #555555;
            --noir-light:   #888888;
            --noir-border:  #E2E4E8;
            --noir-hover:   #F0F1F3;

            /* Or */
            --or:           #C8960C;
            --or-vif:       #E8AE14;
            --or-pale:      #FEF8E7;
            --or-border:    #F0D080;

            /* Sémantiques */
            --succes:       #1A8A4A;
            --succes-bg:    #EBF8F0;
            --warning:      #C8960C;
            --warning-bg:   #FEF8E7;
            --danger:       #C0392B;
            --danger-bg:    #FDECEB;
            --info:         #1E6FA8;
            --info-bg:      #EBF4FD;

            --sidebar-w:    260px;
            --header-h:     62px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-page);
            color: var(--noir-text);
            min-height: 100vh;
            display: flex;
        }

        /* ─── SIDEBAR ──────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--noir);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-logo {
            padding: 22px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--or), var(--or-vif));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(200,150,12,.35);
        }

        .sidebar-logo-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 21px;
            font-weight: 600;
            color: #FFFFFF;
            line-height: 1.1;
        }

        .sidebar-logo-text span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 9.5px;
            font-weight: 400;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            margin-top: 2px;
        }

        .sidebar-nav { flex: 1; padding: 14px 0; overflow-y: auto; }

        .nav-section {
            padding: 10px 18px 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.2);
            margin-top: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            margin: 1px 10px;
            border-radius: 8px;
            color: rgba(255,255,255,.5);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .18s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.07);
            color: rgba(255,255,255,.85);
        }

        .nav-item.active {
            background: rgba(200,150,12,.18);
            color: var(--or-vif);
        }

        .nav-item.active i { color: var(--or-vif); }
        .nav-item i { font-size: 17px; width: 20px; text-align: center; }

        /* Gold dot actif */
        .nav-item.active::before {
            content: '';
            width: 3px; height: 20px;
            background: linear-gradient(180deg, var(--or), var(--or-vif));
            border-radius: 2px;
            margin-right: -2px;
            margin-left: -6px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 11px;
            background: rgba(255,255,255,.06);
            border-radius: 9px;
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--or), var(--or-vif));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: var(--noir);
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 11px; color: rgba(255,255,255,.35); text-transform: capitalize; }

        .logout-btn {
            background: none; border: none;
            color: rgba(255,255,255,.3);
            cursor: pointer; padding: 4px;
            border-radius: 4px; font-size: 16px;
            transition: color .2s;
        }
        .logout-btn:hover { color: var(--or-vif); }

        /* ─── MAIN ─────────────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--header-h);
            background: var(--bg-white);
            border-bottom: 1px solid var(--noir-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 600;
            color: var(--noir-text);
            letter-spacing: .2px;
        }

        .topbar-left p { font-size: 12px; color: var(--noir-light); margin-top: 1px; }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .topbar-date {
            font-family: 'DM Mono', monospace;
            font-size: 11.5px;
            color: var(--noir-light);
            background: var(--bg-surface);
            border: 1px solid var(--noir-border);
            padding: 6px 12px;
            border-radius: 6px;
            display: flex; align-items: center; gap: 6px;
        }

        .notif-btn {
            width: 36px; height: 36px;
            background: var(--bg-surface);
            border: 1px solid var(--noir-border);
            border-radius: 8px;
            color: var(--noir-light);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            transition: all .2s;
            position: relative;
        }
        .notif-btn:hover { border-color: var(--or-border); color: var(--or); background: var(--or-pale); }

        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            width: 17px; height: 17px;
            background: var(--or);
            border-radius: 50%;
            font-size: 9px; font-weight: 700;
            color: var(--noir);
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-white);
        }

        .content { padding: 28px 32px; flex: 1; }

        /* ─── ALERTS ───────────────────────────────────────────── */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 16px; border-radius: 10px;
            margin-bottom: 22px; font-size: 13.5px; font-weight: 500;
            border: 1px solid;
        }
        .alert-success { background: var(--succes-bg); border-color: rgba(26,138,74,.2); color: var(--succes); }
        .alert-error   { background: var(--danger-bg); border-color: rgba(192,57,43,.2); color: var(--danger); }
        .alert-warning { background: var(--warning-bg); border-color: var(--or-border); color: var(--or); }
        .alert i { font-size: 17px; margin-top: 1px; flex-shrink: 0; }

        /* ─── CARDS ────────────────────────────────────────────── */
        .card {
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .card-header {
            padding: 15px 22px;
            border-bottom: 1px solid var(--noir-border);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-white);
        }

        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 17px; font-weight: 600;
            color: var(--noir-text);
            letter-spacing: .2px;
        }

        .card-body { padding: 22px; }

        /* ─── KPI ──────────────────────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .kpi-card {
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 12px;
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .kpi-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.09); transform: translateY(-2px); }

        /* Barre latérale gauche colorée */
        .kpi-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--or), var(--or-vif));
        }
        .kpi-card.green::before  { background: var(--succes); }
        .kpi-card.blue::before   { background: var(--info); }
        .kpi-card.danger::before { background: var(--danger); }

        .kpi-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.8px; text-transform: uppercase;
            color: var(--noir-light);
            margin-bottom: 10px;
        }

        .kpi-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 40px; font-weight: 600;
            color: var(--noir-text);
            line-height: 1;
        }

        .kpi-sub { font-size: 12px; color: var(--noir-light); margin-top: 6px; }

        .kpi-icon {
            position: absolute; top: 18px; right: 18px;
            font-size: 28px; color: var(--noir-border);
        }

        /* ─── TABLES ───────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }

        thead tr { border-bottom: 2px solid var(--noir-border); }

        thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--noir-light);
            white-space: nowrap;
            background: var(--bg-surface);
        }

        tbody tr { border-bottom: 1px solid var(--noir-border); transition: background .12s; }
        tbody tr:hover { background: var(--bg-surface); }
        tbody td { padding: 13px 16px; color: var(--noir-mid); vertical-align: middle; }

        /* ─── BADGES ───────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            letter-spacing: .4px; text-transform: uppercase;
        }
        .badge-gold   { background: var(--or-pale);     color: var(--or);      border: 1px solid var(--or-border); }
        .badge-green  { background: var(--succes-bg);   color: var(--succes);  }
        .badge-red    { background: var(--danger-bg);   color: var(--danger);  }
        .badge-orange { background: var(--warning-bg);  color: var(--warning); }
        .badge-blue   { background: var(--info-bg);     color: var(--info);    }
        .badge-gray   { background: var(--bg-surface);  color: var(--noir-light); border: 1px solid var(--noir-border); }

        /* ─── BUTTONS ──────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all .18s; white-space: nowrap;
        }

        .btn-primary {
            background: var(--noir);
            color: #FFFFFF;
        }
        .btn-primary:hover { background: #2a2a2a; box-shadow: 0 3px 12px rgba(0,0,0,.2); transform: translateY(-1px); }

        .btn-gold {
            background: linear-gradient(135deg, var(--or), var(--or-vif));
            color: var(--noir);
        }
        .btn-gold:hover { filter: brightness(1.08); box-shadow: 0 3px 12px rgba(200,150,12,.3); transform: translateY(-1px); }

        .btn-outline {
            background: var(--bg-white);
            color: var(--noir-mid);
            border: 1px solid var(--noir-border);
        }
        .btn-outline:hover { border-color: var(--noir-mid); color: var(--noir-text); background: var(--bg-surface); }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(192,57,43,.2);
        }
        .btn-danger:hover { background: var(--danger); color: #fff; }

        .btn-success {
            background: var(--succes-bg);
            color: var(--succes);
            border: 1px solid rgba(26,138,74,.2);
        }
        .btn-success:hover { background: var(--succes); color: #fff; }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-lg { padding: 11px 24px; font-size: 14px; }

        /* ─── FORMS ────────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: var(--noir-mid); margin-bottom: 7px;
        }

        .form-control {
            width: 100%;
            padding: 10px 13px;
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 8px;
            color: var(--noir-text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--or);
            box-shadow: 0 0 0 3px rgba(200,150,12,.1);
        }
        .form-control::placeholder { color: var(--noir-border); }

        .form-error { font-size: 12px; color: var(--danger); margin-top: 5px; display: flex; align-items: center; gap: 4px; }

        .form-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
        @media (max-width: 768px) { .form-grid, .form-grid-3 { grid-template-columns: 1fr; } }

        /* ─── PROGRESS ─────────────────────────────────────────── */
        .progress { height: 5px; background: var(--noir-border); border-radius: 10px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--or), var(--or-vif)); transition: width .4s; }
        .progress-bar.green  { background: var(--succes); }
        .progress-bar.orange { background: var(--warning); }

        /* ─── PAGINATION ───────────────────────────────────────── */
        .pagination { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
        .page-link {
            padding: 6px 12px;
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 6px; color: var(--noir-mid);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all .18s;
        }
        .page-link:hover { border-color: var(--or); color: var(--or); background: var(--or-pale); }
        .page-link.active { background: var(--noir); color: #fff; border-color: var(--noir); }

        /* ─── STOCK INDICATOR ──────────────────────────────────── */
        .stock-indicator { display: flex; align-items: center; gap: 6px; font-size: 13px; }
        .stock-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .stock-dot.ok       { background: var(--succes); }
        .stock-dot.bas      { background: var(--warning); }
        .stock-dot.critique { background: var(--danger); }

        /* ─── DIVIDER ──────────────────────────────────────────── */
        .divider { border: none; border-top: 1px solid var(--noir-border); margin: 20px 0; }

        /* ─── PAGE HEADER ──────────────────────────────────────── */
        .page-header {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 26px; gap: 16px; flex-wrap: wrap;
        }

        .page-header h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px; font-weight: 600;
            color: var(--noir-text); line-height: 1;
        }

        .page-header p { font-size: 13px; color: var(--noir-light); margin-top: 4px; }

        .or-line {
            width: 36px; height: 2px;
            background: linear-gradient(90deg, var(--or), var(--or-vif));
            border-radius: 2px; margin-top: 7px;
        }

        /* ─── SCROLLBAR ────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-page); }
        ::-webkit-scrollbar-thumb { background: var(--noir-border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--or); }

        /* ─── MOBILE ───────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .content { padding: 18px; }
        }

        /* ─── ANIMATION ────────────────────────────────────────── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        .fade-in { animation: fadeIn .3s ease forwards; }

        /* ─── LIGNE INGREDIANT ─────────────────────────────────── */
        .ligne-ingrediant {
            display: grid;
            grid-template-columns: 1fr 120px 36px;
            gap: 10px; align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--noir-border);
        }
        .ligne-ingrediant:last-child { border-bottom: none; }

        .btn-remove-ligne {
            width: 32px; height: 32px;
            background: var(--danger-bg);
            border: 1px solid rgba(192,57,43,.2);
            border-radius: 6px; color: var(--danger);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; transition: all .18s;
        }
        .btn-remove-ligne:hover { background: var(--danger); color: #fff; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ─── SIDEBAR NOIRE ─── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">🍞</div>
        <div class="sidebar-logo-text">
            BoulangeriePro
            <span>Côte d'Ivoire</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-3-line"></i> Tableau de bord
        </a>

        <div class="nav-section">Production</div>
        <a href="{{ route('productions.index') }}" class="nav-item {{ request()->routeIs('productions.*') ? 'active' : '' }}">
            <i class="ri-fire-line"></i> Fournées
        </a>
        <a href="{{ route('recettes.index') }}" class="nav-item {{ request()->routeIs('recettes.*') ? 'active' : '' }}">
            <i class="ri-book-2-line"></i> Recettes
        </a>
        <a href="{{ route('produits.index') }}" class="nav-item {{ request()->routeIs('produits.*') ? 'active' : '' }}">
            <i class="ri-store-2-line"></i> Produits
        </a>
        <a href="{{ route('matieres-premieres.index') }}" class="nav-item {{ request()->routeIs('matieres-premieres.*') ? 'active' : '' }}">
            <i class="ri-stack-line"></i> Matières premières
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                <div class="user-role">{{ auth()->user()->roles->first()->name ?? 'Rôle' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Déconnexion">
                    <i class="ri-logout-box-r-line"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ─── MAIN CONTENT ─── --}}
<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-left">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <p>@yield('page-subtitle', '')</p>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="ri-calendar-line"></i>
                {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </div>
            <button class="notif-btn" title="Notifications">
                <i class="ri-notification-3-line"></i>
                @php $alertes = \App\Models\MatierePremiere::whereColumn('stock_actuel', '<=', 'stock_minimum')->count(); @endphp
                @if($alertes > 0)
                    <span class="notif-badge">{{ $alertes }}</span>
                @endif
            </button>
        </div>
    </header>

    <main class="content fade-in">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="ri-check-double-line"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->has('error'))
            <div class="alert alert-error">
                <i class="ri-error-warning-line"></i> {{ session('error') ?? $errors->first('error') }}
            </div>
        @endif

        @if(session('alerte_stock'))
            <div class="alert alert-warning">
                <i class="ri-alert-line"></i> {{ session('alerte_stock') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
