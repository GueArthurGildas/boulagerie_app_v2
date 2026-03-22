<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — BoulangeriePro CI</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --noir: #111111; --noir-card: #1C1C1C; --noir-border: #2A2A2A;
            --or: #D4A017; --or-vif: #F5C842; --or-sombre: #A87C10; --or-pale: #3A2E0A;
            --creme: #F9F6EE; --creme-off: #EDE9DC;
            --gris-mid: #666666; --gris-light: #999999;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--noir);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Subtle grid pattern */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 80px, rgba(212,160,23,.03) 80px, rgba(212,160,23,.03) 81px),
                repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(212,160,23,.03) 80px, rgba(212,160,23,.03) 81px);
            pointer-events: none;
        }

        /* Gold glow */
        body::after {
            content: '';
            position: fixed;
            bottom: -150px; right: -150px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(212,160,23,.07) 0%, transparent 65%);
            pointer-events: none;
        }

        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 10;
            animation: fadeUp .5s ease forwards;
        }

        .logo {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--or-sombre), var(--or-vif));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 32px rgba(212,160,23,.25);
        }

        .logo-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            font-weight: 600;
            color: var(--creme);
            letter-spacing: .5px;
        }

        .logo-sub {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gris-mid);
            margin-top: 5px;
        }

        .login-card {
            background: var(--noir-card);
            border: 1px solid var(--noir-border);
            border-radius: 16px;
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        /* Gold top line */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--or), var(--or-vif), var(--or), transparent);
        }

        .login-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            font-weight: 600;
            color: var(--creme);
            margin-bottom: 4px;
        }
        .login-subtitle { font-size: 13px; color: var(--gris-mid); margin-bottom: 28px; }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gris-mid);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--gris-mid);
            font-size: 17px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 42px;
            background: #202020;
            border: 1px solid var(--noir-border);
            border-radius: 9px;
            color: var(--creme);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--or-sombre);
            box-shadow: 0 0 0 3px rgba(212,160,23,.1);
        }

        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; }
        .remember-row input { accent-color: var(--or); width: 15px; height: 15px; cursor: pointer; }
        .remember-row label { font-size: 13px; color: var(--gris-light); cursor: pointer; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--or-sombre), var(--or-vif));
            color: var(--noir);
            border: none;
            border-radius: 9px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .5px;
            cursor: pointer;
            transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-login:hover {
            filter: brightness(1.1);
            box-shadow: 0 4px 20px rgba(212,160,23,.3);
            transform: translateY(-1px);
        }

        .error-box {
            background: rgba(192,57,43,.1);
            border: 1px solid rgba(192,57,43,.25);
            border-radius: 8px;
            padding: 11px 14px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #e05a4a;
            display: flex; gap: 8px; align-items: center;
        }

        .footer-note {
            text-align: center;
            font-size: 11px;
            color: var(--gris-mid);
            margin-top: 22px;
            letter-spacing: .5px;
        }

        .footer-note span { color: var(--or-sombre); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: none; }
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="logo">
        <div class="logo-icon">🍞</div>
        <div class="logo-name">BoulangeriePro</div>
        <div class="logo-sub">Côte d'Ivoire · Gestion complète</div>
    </div>

    <div class="login-card">
        <div class="login-title">Connexion</div>
        <div class="login-subtitle">Accédez à votre espace de gestion</div>

        @if ($errors->any())
            <div class="error-box">
                <i class="ri-error-warning-line"></i>
                Identifiants incorrects. Veuillez réessayer.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <div class="input-wrap">
                    <i class="ri-mail-line input-icon"></i>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}"
                           placeholder="admin@boulangerie.ci"
                           autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div class="input-wrap">
                    <i class="ri-lock-line input-icon"></i>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••"
                           autocomplete="current-password" required>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Rester connecté</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="ri-login-box-line"></i> Se connecter
            </button>
        </form>
    </div>

    <div class="footer-note">
        BoulangeriePro CI v1.0 · <span>SYSCOHADA · FCFA</span>
    </div>
</div>
</body>
</html>
