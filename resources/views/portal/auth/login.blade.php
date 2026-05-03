<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login</title>

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 20%),
                radial-gradient(circle at bottom left, rgba(179,139,47,.10), transparent 18%),
                #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            padding: 24px;
            box-sizing: border-box;
        }

        .auth-card {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid #dbe5ee;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .auth-hero {
            padding: 28px 28px 26px;
            background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%);
            color: #fff;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(255,255,255,.45);
            display: grid;
            place-items: center;
            box-shadow: 0 14px 34px rgba(15,23,42,.18);
            overflow: hidden;
            flex-shrink: 0;
        }

        .auth-logo img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            display: block;
        }

        .auth-brand-text {
            display: grid;
            gap: 5px;
        }

        .auth-brand-name {
            color: rgba(255,255,255,.96);
            font-size: 14px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .auth-brand-subtitle {
            color: rgba(255,255,255,.76);
            font-size: 12px;
            line-height: 1.35;
            font-weight: 750;
        }

        .auth-title {
            font-size: 36px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -.04em;
        }

        .auth-sub {
            margin-top: 14px;
            font-size: 14px;
            line-height: 1.7;
            opacity: .9;
        }

        .auth-body {
            padding: 28px;
        }

        .auth-field {
            margin-bottom: 16px;
        }

        .auth-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
        }

        .auth-input {
            width: 100%;
            min-height: 50px;
            border-radius: 14px;
            border: 1px solid #d7e2e5;
            background: #fff;
            padding: 0 14px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
        }

        .auth-input:focus {
            border-color: #2f6f73;
            box-shadow: 0 0 0 4px rgba(47,111,115,.10);
        }

        .auth-btn {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb, #4f8cff);
            color: #fff;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
        }

        .auth-error {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 14px;
            line-height: 1.6;
        }

        .auth-link-wrap {
            margin-top: 16px;
            text-align: center;
        }

        .auth-link {
            color: #0f766e;
            text-decoration: none;
            font-size: 13px;
            font-weight: 850;
        }

        @media (max-width: 560px) {
            .auth-card {
                max-width: 100%;
            }

            .auth-brand {
                align-items: flex-start;
            }

            .auth-title {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="auth-hero">
            <div class="auth-brand">
                <div class="auth-logo">
                    <img src="{{ asset('images/sada-fezzan-logo.png') }}" alt="Sada Fezzan" onerror="this.parentElement.style.display='none'">
                </div>

                <div class="auth-brand-text">
                    <div class="auth-brand-name">Sada Fezzan</div>
                    <div class="auth-brand-subtitle">Employee & Candidate Self Portal</div>
                </div>
            </div>

            <div class="auth-title">Portal Login</div>

            <div class="auth-sub">
                Sign in to view your updates, salary slips, notifications, and future portal items.
            </div>
        </div>

        <div class="auth-body">
            @if($errors->any())
                <div class="auth-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.login.store') }}">
                @csrf

                <div class="auth-field">
                    <label class="auth-label">Email</label>
                    <input class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>

                <div class="auth-field">
                    <label class="auth-label">Password</label>
                    <input class="auth-input" type="password" name="password" required autocomplete="current-password">
                </div>

                <button type="submit" class="auth-btn">Sign In</button>
            </form>

            <div class="auth-link-wrap">
                <a href="{{ route('portal.password.request') }}" class="auth-link">
                    Forgot password or set password for the first time?
                </a>
            </div>
        </div>
    </div>
</body>
</html>
