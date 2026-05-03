<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Portal Password Setup</title>
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

        .message {
            margin-top: 12px;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 750;
        }

        .message.success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #86efac;
        }

        .message.error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .portal-auth-link {
            display: block;
            margin-top: 16px;
            text-align: center;
            color: #0f766e;
            text-decoration: none;
            font-size: 13px;
            font-weight: 850;
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
        <h1>Set or reset password</h1>
        <p>
            Enter your portal email and we will send you a secure password setup link.
        </p>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="message error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('portal.password.request.send') }}">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">

            <button type="submit">Send Password Setup Link</button>
        </form>

        <a class="portal-auth-link" href="{{ route('portal.login') }}">Back to Login</a>
    </main>
</body>
</html>
