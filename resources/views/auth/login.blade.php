<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Fiesta Tours</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* ── FONDO DIAGONAL ── */
        .bg-image {
            position: fixed;
            inset: 0;
            z-index: 0;
        }
        .bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bg-diagonal {
            position: fixed;
            inset: 0;
            z-index: 1;
        }
        .bg-diagonal svg {
            width: 100%;
            height: 100%;
        }

        /* ── TARJETA ── */
        .wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            width: 820px;
            min-height: 520px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,.2);
        }

        /* ── PANEL IZQUIERDO ── */
        .left {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.8rem;
        }
        .left-img {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        .left-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,.4) 0%,
                rgba(0,0,0,.05) 45%,
                rgba(0,0,0,.65) 100%
            );
            z-index: 1;
        }
        .left-top, .left-bottom { position: relative; z-index: 2; }
        .left-top { display: flex; justify-content: space-between; align-items: center; }

        .badge-works {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,.92);
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            padding: 5px 16px;
            border-radius: 999px;
        }

        .left-bottom {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .logo-large {
            width: 180px;
            height: auto;
            display: block;
        }
        .logo-large img {
            width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: contain;
            display: block;
            filter: brightness(0) invert(1);
        }
        .nav-btns { display: flex; gap: 6px; margin-left: auto; }
        .nav-btn {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.3);
            background: rgba(255,255,255,.1);
            color: rgba(255,255,255,.85);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 14px;
        }

        /* ── PANEL DERECHO ── */
        .right {
            width: 340px;
            background: #fff;
            padding: 2.2rem 2.4rem 2rem;
            display: flex;
            flex-direction: column;
        }
        .right-top {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2.4rem;
        }
        .brand-logo {
            width: 200px;
            height: auto;
            display: block;
        }
        .brand-logo img {
            width: 100%;
            height: auto;
            max-height: 70px;
            object-fit: contain;
        }

        h1 { font-size: 28px; font-weight: 700; color: #0f172a; margin-bottom: 6px; letter-spacing: -.5px; }
        .subtitle { font-size: 13px; color: #64748b; margin-bottom: 2rem; }

        .error-box {
            background: #fee2e2; border: 1px solid #fca5a5;
            color: #991b1b; padding: .7rem 1rem;
            border-radius: 8px; font-size: .82rem; margin-bottom: 1rem;
        }

        label {
            display: block; font-size: 11px; font-weight: 600;
            color: #64748b; text-transform: uppercase;
            letter-spacing: .6px; margin-bottom: 5px;
        }

        /* ── CONTENEDOR DEL INPUT CON OJO ── */
        .password-wrapper {
            position: relative;
            margin-bottom: 1.1rem;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 13px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 13px;
            outline: none;
            transition: border-color .15s, background .15s;
        }

        .password-wrapper input {
            padding-right: 42px;
            margin-bottom: 0;
        }

        input:focus {
            border-color: #1c1d30;
            background: #fff;
        }

        /* ── BOTÓN OJO ── */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 18px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .2s;
            line-height: 1;
        }

        .toggle-password:hover {
            color: #1c1d30;
        }

        .toggle-password:focus {
            outline: none;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .forgot {
            font-size: 12px;
            color: #1c1d30;
            text-align: right;
            margin-top: -8px;
            margin-bottom: 2rem;
            text-decoration: none;
            display: block;
        }
        .forgot:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 12px;
            background: #e63232; border: none;
            border-radius: 8px; color: #fff;
            font-size: 15px; font-weight: 600;
            cursor: pointer; letter-spacing: .2px;
            transition: background .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover  { background: #c42a2a; }
        .btn-login:active { transform: scale(.98); }

        .btn-login.btn-loading {
            opacity: 0.85;
            cursor: not-allowed;
            background: #c42a2a;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
                width: 95%;
                max-width: 400px;
                min-height: auto;
                border-radius: 16px;
            }
            .left { display: none; }
            .right {
                width: 100%;
                padding: 2rem 1.5rem;
            }
            .brand-logo {
                width: 160px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-image">
        <img src="https://www.machupicchuexploringperu.com/wp-content/uploads/2023/07/header-slider-machu-picchu-one-day-tour-1920x1080-1-2.jpg"
             alt="">
    </div>

    <div class="bg-diagonal">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="62,0 100,0 100,100 52,100" fill="white"/>
        </svg>
    </div>

    {{-- TARJETA --}}
    <div class="wrapper">

        <div class="left">
            <img class="left-img"
                 src="https://www.machupicchuexploringperu.com/wp-content/uploads/2023/07/banner-camino-inca.jpg"
                 alt="Paisaje Fiesta Tours">
            <div class="left-overlay"></div>

            <div class="left-top">
                <span class="badge-works">Destinos Seleccionados</span>
            </div>

            <div class="left-bottom">
                <div class="logo-large">
                    <img src="https://res.cloudinary.com/dlgeap8h0/image/upload/v1782924689/log-png_r4ezpy.png" alt="Fiesta Tours">
                </div>
                <div class="nav-btns">
                    <button class="nav-btn">&#8592;</button>
                    <button class="nav-btn">&#8594;</button>
                </div>
            </div>
        </div>

        <div class="right">
            <div class="right-top">
                <div class="brand-logo">
                    <img src="https://res.cloudinary.com/dlgeap8h0/image/upload/v1782924689/log-png_r4ezpy.png" alt="Fiesta Tours">
                </div>
            </div>

            <h1>Bienvenido</h1>
            <p class="subtitle">Ingresa a tu cuenta para continuar</p>

            @if($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.post') }}" id='login-form' method="POST">
                @csrf

                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="correo@ejemplo.com"
                       required autofocus>

                <label for="password">Contraseña</label>

                <div class="password-wrapper">
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                        <svg viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>

                <button type="submit" class="btn-login" id='btn-submit'>
                    <span id="btn-text">Iniciar Sesión</span>
                </button>
            </form>
        </div>

    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('btn-submit');

            if (btn.classList.contains('btn-loading')) {
                e.preventDefault();
                return;
            }

            btn.innerHTML = `<span class="spinner"></span><span>Iniciando...</span>`;
            btn.disabled = true;
            btn.classList.add('btn-loading');
        });

        (function() {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    const svg = this.querySelector('svg');
                    if (svg) {
                        if (type === 'text') {
                            svg.innerHTML = `
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            `;
                        } else {
                            svg.innerHTML = `
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            `;
                        }
                    }
                });
            }
        })();
    </script>

</body>
</html>
