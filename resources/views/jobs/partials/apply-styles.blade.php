<style>
    :root {
        --primary: #3C9FA3;
        --primary-dark: #2E8B8F;
        --primary-soft: #E8F7F7;
        --heading: #1F314D;
        --text: #1F2937;
        --muted: #6B7280;
        --border: #D7E0E5;
        --bg: #F4F7F9;
        --white: #FFFFFF;
        --danger: #DC2626;
        --danger-bg: #FEF2F2;
        --success: #065F46;
        --success-bg: #ECFDF5;
        --shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        --shadow-soft: 0 8px 18px rgba(15, 23, 42, 0.04);
    }

    * { box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
        margin: 0;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:
            radial-gradient(circle at top left, rgba(60,159,163,.10), transparent 20%),
            radial-gradient(circle at top right, rgba(119,200,203,.14), transparent 22%),
            linear-gradient(180deg, #F8FBFC 0%, #F3F7F9 100%);
        color: var(--text);
    }

    .page {
        min-height: 100vh;
        padding: 40px 16px 64px;
    }

    .container {
        max-width: 980px;
        margin: 0 auto;
    }

    .card {
        background: var(--white);
        border: 1px solid #E7EEF1;
        border-radius: 24px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .55s ease;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .top-strip {
        height: 9px;
        background: linear-gradient(90deg, var(--primary) 0%, #7ED0D3 100%);
    }

    .card-inner {
        padding: 36px 36px 28px;
    }

    .hero {
        display: flex;
        justify-content: space-between;
        gap: 22px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .hero-left {
        flex: 1 1 540px;
    }

    .hero-right {
        flex: 0 0 240px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-soft);
        color: var(--primary-dark);
        border: 1px solid #D7EFEF;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .logo {
        margin-bottom: 10px;
    }

    .logo img {
        height: 60px;
        object-fit: contain;
    }

    .title {
        margin: 0;
        color: var(--heading);
        font-size: 30px;
        line-height: 1.15;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .subtitle {
        margin: 12px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
        max-width: 650px;
    }

    .summary-card {
        min-width: 220px;
        background: linear-gradient(180deg, #FAFDFD 0%, #F3FBFB 100%);
        border: 1px solid #E1F0F1;
        border-radius: 18px;
        padding: 16px 18px;
        box-shadow: var(--shadow-soft);
    }

    .summary-card h4 {
        margin: 0 0 10px;
        font-size: 13px;
        color: var(--muted);
        font-weight: 700;
    }

    .summary-card p {
        margin: 0;
        font-size: 14px;
        color: var(--heading);
        font-weight: 800;
        line-height: 1.6;
    }

    .progress-wrap {
        margin: 0 0 24px;
        padding: 16px 18px;
        border: 1px solid #EBF0F3;
        border-radius: 18px;
        background: #FCFEFE;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
        color: var(--muted);
        font-weight: 700;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #E8EFF2;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        width: 33.33%;
        height: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, #82D4D7 100%);
        border-radius: 999px;
        transition: width .28s ease;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 18px;
        font-size: 14px;
        line-height: 1.6;
    }

    .alert-success {
        background: var(--success-bg);
        color: var(--success);
        border: 1px solid #A7F3D0;
    }

    .alert-danger {
        background: var(--danger-bg);
        color: #991B1B;
        border: 1px solid #FECACA;
    }

    .alert ul {
        margin: 10px 0 0 18px;
        padding: 0;
    }

    .step {
        display: none;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .25s ease, transform .25s ease;
    }

    .step.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .step-panel {
        border: 1px solid #EAF0F3;
        border-radius: 20px;
        background: linear-gradient(180deg, #FFFFFF 0%, #FCFEFE 100%);
        padding: 22px 20px;
        box-shadow: var(--shadow-soft);
    }

    .step-title {
        margin: 0 0 6px;
        font-size: 19px;
        font-weight: 900;
        color: var(--heading);
        letter-spacing: -0.02em;
    }

    .step-caption {
        margin: 0 0 18px;
        color: var(--muted);
        font-size: 13px;
    }

    .fields-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .field {
        width: 100%;
        padding: 14px;
        border: 1px solid #ECF1F3;
        border-radius: 16px;
        background: #FFFFFF;
        transition: border-color .2s ease, box-shadow .2s ease, transform .16s ease;
    }

    .field:hover {
        border-color: #DDE8EC;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.03);
        transform: translateY(-1px);
    }

    .field label {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 8px;
    }

    .required-mark {
        color: var(--danger);
        margin-left: 4px;
    }

    .input,
    .select,
    .textarea,
    .file-input {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
        padding: 12px 14px;
        font-size: 14px;
        color: var(--text);
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .textarea {
        min-height: 120px;
        resize: vertical;
    }

    .input:focus,
    .select:focus,
    .textarea:focus,
    .file-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(60, 159, 163, 0.10);
    }

    .input.invalid,
    .select.invalid,
    .textarea.invalid,
    .file-input.invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.08);
    }

    .help {
        margin-top: 7px;
        font-size: 12px;
        color: var(--muted);
    }

    .error-text {
        display: none;
        margin-top: 7px;
        font-size: 12px;
        color: var(--danger);
        font-weight: 700;
    }

    .error-text.show {
        display: block;
    }

    .inline-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .code-col {
        width: 35%;
    }

    .number-col {
        width: 65%;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 6px 0;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: var(--text);
        padding: 10px 12px;
        border: 1px solid #E8EEF2;
        border-radius: 12px;
        background: #FAFCFD;
        transition: border-color .2s ease, background .2s ease;
    }

    .checkbox-item:hover {
        border-color: #D5E6E7;
        background: #F5FBFB;
    }

    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
    }

    .actions-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 26px;
        flex-wrap: wrap;
    }

    .btn {
        border: none;
        border-radius: 14px;
        padding: 12px 18px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .15s ease, background .2s ease, box-shadow .2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 18px rgba(60, 159, 163, 0.18);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-secondary {
        background: #EEF4F6;
        color: var(--heading);
    }

    .btn-secondary:hover {
        background: #E3ECEF;
    }

    .btn-submit {
        width: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, #55B5BA 100%);
        color: #fff;
        margin-top: 10px;
        box-shadow: 0 12px 22px rgba(60, 159, 163, 0.18);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 20px;
        text-decoration: none;
        color: #35577C;
        font-size: 14px;
        font-weight: 800;
    }

    @media (max-width: 768px) {
        .page {
            padding-top: 22px;
        }

        .card-inner {
            padding: 22px 16px 22px;
        }

        .hero {
            gap: 14px;
        }

        .title {
            font-size: 24px;
        }

        .inline-row {
            flex-direction: column;
        }

        .code-col,
        .number-col {
            width: 100%;
        }

        .actions-row {
            flex-direction: column;
        }

        .actions-row .btn {
            width: 100%;
        }

        .summary-card {
            width: 100%;
        }

        .step-panel {
            padding: 18px 14px;
        }
    }
</style>