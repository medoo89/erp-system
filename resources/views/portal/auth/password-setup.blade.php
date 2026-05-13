<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Portal Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.18), transparent 34%),
                linear-gradient(135deg, #f8fafc, #eef6f8);
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
        }

        .portal-auth-card {
            width: min(100% - 32px, 480px);
            border-radius: 30px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 24px 70px rgba(15,23,42,.12);
            padding: 28px;
        }

        .portal-auth-kicker {
            color: #0f766e;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.08;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        p {
            margin: 10px 0 22px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.65;
            font-weight: 650;
        }

        label {
            display: block;
            margin: 14px 0 7px;
            color: #334155;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            min-height: 48px;
            border-radius: 16px;
            border: 1px solid #d7e2e5;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 750;
            outline: none;
            background: #ffffff;
        }

        input:focus {
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.10);
        }

        button {
            width: 100%;
            margin-top: 20px;
            min-height: 50px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, #0f766e, #0f4c81);
            color: #ffffff;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 14px 34px rgba(15,118,110,.22);
        }

        .error {
            margin-top: 12px;
            border-radius: 16px;
            padding: 12px 14px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 750;
        }

        .hint {
            margin-top: 14px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.55;
            font-weight: 650;
            text-align: center;
        }
        .portal-brand-logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }

        .portal-brand-logo {
            max-width: 178px;
            max-height: 86px;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 12px 24px rgba(15,23,42,.10));
        }

    </style>
</head>
<body>
    <main class="portal-auth-card">
        <div class="portal-brand-logo-wrap">
            <img
                src="{{ asset('images/sada-fezzan-logo.png') }}"
                alt="Sada Fezzan"
                class="portal-brand-logo"
                onerror="this.style.display='none'"
            >
        </div>

        <div class="portal-auth-kicker">Sada Fezzan Employee Portal</div>
        <h1>Set your password</h1>
        <p>
            Create your own secure password to access your employee portal.
        </p>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('portal.password.setup.store', ['token' => $token, 'email' => $email]) }}">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" readonly>

            <label>New Password</label>
            <input type="password" name="password" required autocomplete="new-password">

            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit">Set Password & Open Portal</button>
        </form>

        <div class="hint">
            Password must be at least 8 characters. This setup link is valid for 24 hours.
        </div>
    </main>
</body>
</html>
