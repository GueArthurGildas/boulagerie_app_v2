<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BoulangeriePro CI') — {{ config('app.name') }}</title>

    {{-- Polices : Playfair Display pour titres, Plus Jakarta Sans pour texte, JetBrains Mono pour chiffres --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            /* Fond blanc */
            --bg-page:      #F2F4F7;
            --bg-white:     #FFFFFF;
            --bg-surface:   #F8F9FB;
            --bg-surface2:  #EEF0F4;

            /* Noir */
            --noir:         #0F1117;
            --noir-soft:    #1C1E26;
            --noir-text:    #1A1D27;
            --noir-mid:     #4A4E5E;
            --noir-light:   #8890A4;
            --noir-border:  #DDE1EA;
            --noir-hover:   #F0F2F6;

            /* Or */
            --or:           #C8960C;
            --or-vif:       #E8AE14;
            --or-pale:      #FEF8E7;
            --or-border:    #F0D080;
            --or-dark:      #9A7008;

            /* Sémantiques */
            --succes:       #0F7B3E;
            --succes-bg:    #E8F5EE;
            --succes-border:#A3D5B8;
            --warning:      #C8960C;
            --warning-bg:   #FEF8E7;
            --warning-border:#F0D080;
            --danger:       #B5290E;
            --danger-bg:    #FDECEA;
            --danger-border:#F5A99E;
            --info:         #1358A5;
            --info-bg:      #EAF1FB;
            --info-border:  #9BBDE8;

            --sidebar-w:    268px;
            --header-h:     64px;

            /* Typographie */
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body:    'Plus Jakarta Sans', system-ui, sans-serif;
            --font-mono:    'JetBrains Mono', 'Fira Code', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--bg-page);
            color: var(--noir-text);
            min-height: 100vh;
            display: flex;
            font-size: 14px;
            line-height: 1.6;
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
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 600;
            color: #fff;
            line-height: 1.1;
        }

        .sidebar-logo-text span {
            display: block;
            font-family: var(--font-body);
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            margin-top: 2px;
        }

        .sidebar-nav { flex: 1; padding: 14px 0; overflow-y: auto; }

        .nav-section {
            padding: 10px 18px 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.18);
            margin-top: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            margin: 1px 10px;
            border-radius: 8px;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .15s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.07);
            color: rgba(255,255,255,.85);
        }

        .nav-item.active {
            background: rgba(200,150,12,.18);
            color: var(--or-vif);
        }

        .nav-item.active::before {
            content: '';
            width: 3px; height: 18px;
            background: linear-gradient(180deg, var(--or), var(--or-vif));
            border-radius: 2px;
            margin-right: -2px;
            margin-left: -6px;
            flex-shrink: 0;
        }

        .nav-item i { font-size: 17px; width: 20px; text-align: center; }

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
            font-size: 12px; font-weight: 700;
            color: var(--noir); flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 11px; color: rgba(255,255,255,.3); }

        .logout-btn {
            background: none; border: none;
            color: rgba(255,255,255,.25);
            cursor: pointer; padding: 4px; border-radius: 4px;
            font-size: 16px; transition: color .2s;
        }
        .logout-btn:hover { color: var(--or-vif); }

        /* ─── MAIN ─────────────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1; min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .topbar {
            height: var(--header-h);
            background: var(--bg-white);
            border-bottom: 1px solid var(--noir-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky; top: 0; z-index: 50;
        }

        .topbar-left h1 {
            font-family: var(--font-display);
            font-size: 22px; font-weight: 600;
            color: var(--noir-text);
        }

        .topbar-left p { font-size: 12px; color: var(--noir-light); margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .topbar-date {
            font-family: var(--font-mono);
            font-size: 11px; color: var(--noir-light);
            background: var(--bg-surface);
            border: 1px solid var(--noir-border);
            padding: 6px 12px; border-radius: 6px;
            display: flex; align-items: center; gap: 6px;
        }

        .notif-btn {
            width: 36px; height: 36px;
            background: var(--bg-surface);
            border: 1px solid var(--noir-border);
            border-radius: 8px;
            color: var(--noir-light); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; transition: all .18s; position: relative;
        }
        .notif-btn:hover { border-color: var(--or-border); color: var(--or); background: var(--or-pale); }

        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            width: 17px; height: 17px;
            background: var(--or); border-radius: 50%;
            font-size: 9px; font-weight: 700; color: var(--noir);
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-white);
        }

        .content { padding: 28px 32px; flex: 1; }

        /* ─── PAGE HEADER ──────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 26px;
            gap: 16px; flex-wrap: wrap;
        }

        .page-header-left h2 {
            font-family: var(--font-display);
            font-size: 30px; font-weight: 700;
            color: var(--noir-text); line-height: 1;
        }

        .page-header-left p {
            font-size: 13px; color: var(--noir-light); margin-top: 5px;
        }

        /* Barre décorative sous le titre */
        .title-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .title-bar-line {
            height: 2px; width: 36px;
            background: linear-gradient(90deg, var(--or), var(--or-vif));
            border-radius: 2px;
        }

        .title-bar-text {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--noir-light);
        }

        /* ─── SECTION HEADER (dans les cards) ─────────────────── */
        .section-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--noir-border);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-white);
        }

        .section-title {
            display: flex; align-items: center; gap: 10px;
        }

        .section-title-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .section-title-icon.gold   { background: var(--or-pale);    color: var(--or); }
        .section-title-icon.green  { background: var(--succes-bg);  color: var(--succes); }
        .section-title-icon.red    { background: var(--danger-bg);  color: var(--danger); }
        .section-title-icon.blue   { background: var(--info-bg);    color: var(--info); }
        .section-title-icon.gray   { background: var(--bg-surface2);color: var(--noir-mid); }

        .section-title-text {
            font-family: var(--font-display);
            font-size: 16px; font-weight: 600;
            color: var(--noir-text);
        }

        .section-title-sub {
            font-size: 11px; color: var(--noir-light); margin-top: 1px;
        }

        /* ─── CARDS ────────────────────────────────────────────── */
        .card {
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .card-header {
            padding: 15px 22px;
            border-bottom: 1px solid var(--noir-border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-title {
            font-family: var(--font-display);
            font-size: 16px; font-weight: 600;
            color: var(--noir-text);
        }

        .card-body { padding: 22px; }

        /* ─── KPI CARDS ────────────────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px; margin-bottom: 26px;
        }

        .kpi-card {
            background: var(--bg-white);
            border: 1px solid var(--noir-border);
            border-radius: 12px;
            padding: 18px 20px;
            position: relative; overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .kpi-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-2px); }

        .kpi-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--or), var(--or-vif));
            border-radius: 0 2px 2px 0;
        }
        .kpi-card.green::before  { background: var(--succes); }
        .kpi-card.blue::before   { background: var(--info); }
        .kpi-card.danger::before { background: var(--danger); }
        .kpi-card.orange::before { background: var(--warning); }

        .kpi-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--noir-light); margin-bottom: 8px;
        }

        /* Chiffres KPI avec JetBrains Mono */
        .kpi-value {
            font-family: var(--font-mono);
            font-size: 28px; font-weight: 600;
            color: var(--noir-text); line-height: 1;
            letter-spacing: -1px;
        }

        .kpi-value-currency {
            font-family: var(--font-mono);
            font-size: 22px; font-weight: 600;
            color: var(--noir-text); line-height: 1;
            letter-spacing: -0.5px;
        }

        .kpi-sub { font-size: 11px; color: var(--noir-light); margin-top: 6px; }
        .kpi-icon {
            position: absolute; top: 18px; right: 18px;
            font-size: 26px; color: var(--bg-surface2);
        }

        /* ─── MONTANTS — format FCFA lisible ───────────────────── */
        .montant {
            font-family: var(--font-mono);
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .montant-lg {
            font-family: var(--font-mono);
            font-size: 26px; font-weight: 700;
            letter-spacing: -1px;
            line-height: 1;
        }

        .montant-xl {
            font-family: var(--font-mono);
            font-size: 34px; font-weight: 700;
            letter-spacing: -1.5px;
            line-height: 1;
        }

        .montant-unit {
            font-family: var(--font-body);
            font-size: 11px; font-weight: 600;
            color: var(--noir-light);
            letter-spacing: 0.5px;
            margin-left: 3px;
        }

        /* ─── ALERTS ───────────────────────────────────────────── */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 16px; border-radius: 10px;
            margin-bottom: 20px; font-size: 13.5px; font-weight: 500;
            border: 1px solid;
        }
        .alert-success { background: var(--succes-bg); border-color: var(--succes-border); color: var(--succes); }
        .alert-error   { background: var(--danger-bg); border-color: var(--danger-border); color: var(--danger); }
        .alert-warning { background: var(--warning-bg); border-color: var(--warning-border); color: var(--warning); }
        .alert-info    { background: var(--info-bg);    border-color: var(--info-border);   color: var(--info); }
        .alert i { font-size: 17px; margin-top: 1px; flex-shrink: 0; }

        /* ─── TABLES ───────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }

        thead tr {
            background: var(--bg-surface);
            border-bottom: 2px solid var(--noir-border);
        }

        thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--noir-light);
            white-space: nowrap;
        }

        tbody tr { border-bottom: 1px solid var(--noir-border); transition: background .12s; }
        tbody tr:hover { background: var(--bg-surface); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 13px 16px; color: var(--noir-mid); vertical-align: middle; }

        /* ─── BADGES ───────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            letter-spacing: .3px; text-transform: uppercase;
        }
        .badge-gold    { background: var(--or-pale);      color: var(--or-dark);   border: 1px solid var(--or-border); }
        .badge-green   { background: var(--succes-bg);    color: var(--succes);    border: 1px solid var(--succes-border); }
        .badge-red     { background: var(--danger-bg);    color: var(--danger);    border: 1px solid var(--danger-border); }
        .badge-orange  { background: var(--warning-bg);   color: var(--warning);   border: 1px solid var(--warning-border); }
        .badge-blue    { background: var(--info-bg);      color: var(--info);      border: 1px solid var(--info-border); }
        .badge-gray    { background: var(--bg-surface2);  color: var(--noir-mid);  border: 1px solid var(--noir-border); }

        /* Badge brouillon — bien visible */
        .badge-brouillon {
            background: #FFF3CD;
            color: #856404;
            border: 1px solid #FFD452;
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(200,150,12,.3); }
            50%       { box-shadow: 0 0 0 4px rgba(200,150,12,.1); }
        }

        /* ─── BUTTONS ──────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 8px;
            font-family: var(--font-body);
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all .18s; white-space: nowrap;
        }

        /* Bouton primaire — Noir */
        .btn-primary {
            background: var(--noir);
            color: #fff;
        }
        .btn-primary:hover { background: #2a2d38; box-shadow: 0 3px 12px rgba(0,0,0,.2); transform: translateY(-1px); }

        /* Bouton or */
        .btn-gold {
            background: linear-gradient(135deg, var(--or), var(--or-vif));
            color: var(--noir);
        }
        .btn-gold:hover { filter: brightness(1.08); box-shadow: 0 3px 12px rgba(200,150,12,.3); transform: translateY(-1px); }

        /* Bouton retour — bien visible */
        .btn-back {
            background: var(--noir-text);
            color: #fff;
            border: none;
        }
        .btn-back:hover { background: #2a2d38; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.2); }

        /* Bouton reset/effacer — rouge */
        .btn-reset {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid var(--danger-border);
        }
        .btn-reset:hover { background: var(--danger); color: #fff; }

        /* Bouton outline standard */
        .btn-outline {
            background: var(--bg-white);
            color: var(--noir-mid);
            border: 1px solid var(--noir-border);
        }
        .btn-outline:hover { border-color: var(--noir-mid); color: var(--noir-text); background: var(--bg-surface); }

        .btn-danger {
            background: var(--danger-bg); color: var(--danger);
            border: 1px solid var(--danger-border);
        }
        .btn-danger:hover { background: var(--danger); color: #fff; }

        .btn-success {
            background: var(--succes-bg); color: var(--succes);
            border: 1px solid var(--succes-border);
        }
        .btn-success:hover { background: var(--succes); color: #fff; }

        .btn-warning {
            background: var(--warning-bg); color: var(--warning);
            border: 1px solid var(--warning-border);
        }
        .btn-warning:hover { background: var(--warning); color: #fff; }

        .btn-sm  { padding: 6px 12px; font-size: 12px; }
        .btn-lg  { padding: 11px 24px; font-size: 14px; }
        .btn-xl  { padding: 13px 28px; font-size: 15px; font-weight: 700; }

        /* ─── FORMS ────────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: var(--noir-mid); margin-bottom: 7px;
        }

        .form-control {
            width: 100%; padding: 10px 13px;
            background: var(--bg-white);
            border: 1.5px solid var(--noir-border);
            border-radius: 8px; color: var(--noir-text);
            font-family: var(--font-body); font-size: 14px;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--or);
            box-shadow: 0 0 0 3px rgba(200,150,12,.1);
        }
        .form-control::placeholder { color: var(--noir-border); }

        .form-error {
            font-size: 12px; color: var(--danger);
            margin-top: 5px; display: flex; align-items: center; gap: 4px;
        }

        .form-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
        @media(max-width:768px) { .form-grid,.form-grid-3 { grid-template-columns: 1fr; } }

        /* ─── PROGRESS ─────────────────────────────────────────── */
        .progress { height: 5px; background: var(--bg-surface2); border-radius: 10px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--or), var(--or-vif)); transition: width .4s; }
        .progress-bar.green  { background: var(--succes); }
        .progress-bar.orange { background: var(--warning); }
        .progress-bar.red    { background: var(--danger); }

        /* ─── PAGINATION ───────────────────────────────────────── */
        .pagination { display: flex; gap: 5px; flex-wrap: wrap; }
        .page-link {
            padding: 6px 12px;
            background: var(--bg-white); border: 1px solid var(--noir-border);
            border-radius: 6px; color: var(--noir-mid);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all .18s;
        }
        .page-link:hover { border-color: var(--or); color: var(--or); background: var(--or-pale); }
        .page-link.active { background: var(--noir); color: #fff; border-color: var(--noir); }

        /* ─── STOCK INDICATOR ──────────────────────────────────── */
        .stock-indicator { display: flex; align-items: center; gap: 6px; }
        .stock-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .stock-dot.ok       { background: var(--succes); }
        .stock-dot.bas      { background: var(--warning); }
        .stock-dot.critique { background: var(--danger); }

        /* ─── DIVIDER ──────────────────────────────────────────── */
        .divider { border: none; border-top: 1px solid var(--noir-border); margin: 20px 0; }

        /* ─── SCROLLBAR ────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-page); }
        ::-webkit-scrollbar-thumb { background: var(--noir-border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--or); }

        /* ─── MOBILE ───────────────────────────────────────────── */
        @media(max-width:768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
            .content { padding: 18px; }
        }

        /* ─── ANIMATION ────────────────────────────────────────── */
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeIn .3s ease forwards; }

        /* ─── LIGNE INGRÉDIENT ─────────────────────────────────── */
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
            border: 1px solid var(--danger-border);
            border-radius: 6px; color: var(--danger);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; transition: all .18s;
        }
        .btn-remove-ligne:hover { background: var(--danger); color: #fff; }

        /* ─── ALERTE BROUILLON (card flottante) ────────────────── */
        .brouillon-banner {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            background: #FFF3CD;
            border: 1px solid #FFD452;
            border-radius: 8px;
            font-size: 12px; font-weight: 600;
            color: #856404;
            animation: pulse-border 2s infinite;
        }

        /* ─── STAT BLOCK (pour fiches) ─────────────────────────── */
        .stat-block {
            text-align: center;
            padding: 18px 14px;
            background: var(--bg-surface);
            border-radius: 10px;
            border: 1px solid var(--noir-border);
        }

        .stat-block-value {
            font-family: var(--font-mono);
            font-size: 24px; font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--noir-text);
            line-height: 1;
        }

        .stat-block-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--noir-light); margin-top: 6px;
        }

        /* ─── OR LINE ──────────────────────────────────────────── */
        .or-line {
            width: 36px; height: 2px;
            background: linear-gradient(90deg, var(--or), var(--or-vif));
            border-radius: 2px; margin-top: 7px;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ─── SIDEBAR ─── --}}
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

        <div class="nav-section">Gestion</div>
        <a href="{{ route('fournisseurs.index') }}" class="nav-item {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
            <i class="ri-truck-line"></i> Fournisseurs
            @php $achatsEnAttente = \App\Models\Achat::where('statut','brouillon')->count(); @endphp
            @if($achatsEnAttente > 0)
                <span style="margin-left:auto;background:var(--or);color:var(--noir);font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;">{{ $achatsEnAttente }}</span>
            @endif
        </a>
        <a href="{{ route('depenses.index') }}" class="nav-item {{ request()->routeIs('depenses.*') ? 'active' : '' }}">
            <i class="ri-money-dollar-circle-line"></i> Dépenses
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

{{-- ─── MAIN ─── --}}
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
