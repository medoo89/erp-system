<style>
    :root {
        --sf-primary: #2c5377;
        --sf-primary-soft: #3f6c96;
        --sf-accent: #26b6b7;
        --sf-accent-dark: #16999a;
        --sf-text: #18212b;
        --sf-muted: #6b7f90;
        --sf-border: rgba(44, 83, 119, 0.12);
        --sf-border-soft: rgba(44, 83, 119, 0.08);
        --sf-white: #ffffff;
        --sf-bg-1: #f4f8fa;
        --sf-bg-2: #eef4f7;
        --sf-success: #1d8f79;
        --sf-success-bg: #ebfbf7;
        --sf-danger: #c93434;
        --sf-danger-bg: #fff1f1;
        --sf-danger-border: #f2c1c1;
        --sf-shadow: 0 24px 70px rgba(25, 41, 61, 0.08);
        --sf-shadow-soft: 0 14px 35px rgba(25, 41, 61, 0.06);
    }

    * {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        margin: 0;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:
            radial-gradient(circle at top left, rgba(38, 182, 183, 0.10), transparent 28%),
            radial-gradient(circle at top right, rgba(44, 83, 119, 0.09), transparent 32%),
            linear-gradient(180deg, var(--sf-bg-1) 0%, var(--sf-bg-2) 100%);
        color: var(--sf-text);
    }

    .page {
        min-height: 100vh;
        padding: 40px 16px 64px;
    }

    .container {
        max-width: 1120px;
        margin: 0 auto;
    }

    .apply-shell {
        position: relative;
        overflow: hidden;
        border-radius: 34px;
        border: 1px solid rgba(255,255,255,0.60);
        background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(247,250,251,0.90) 100%);
        box-shadow: var(--sf-shadow);
        backdrop-filter: blur(10px);
        animation: fadeUp .55s ease;
    }

    .apply-shell::before {
        content: "";
        position: absolute;
        right: -80px;
        top: -90px;
        width: 260px;
        height: 260px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(38,182,183,0.14) 0%, rgba(38,182,183,0.03) 60%, transparent 74%);
        pointer-events: none;
    }

    .apply-shell::after {
        content: "";
        position: absolute;
        left: -70px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(44,83,119,0.10) 0%, rgba(44,83,119,0.02) 62%, transparent 74%);
        pointer-events: none;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .apply-topbar {
        position: relative;
        z-index: 1;
        padding: 34px 34px 26px;
        border-bottom: 1px solid var(--sf-border-soft);
    }

    .brand-box {
        display: flex;
        justify-content: center;
        margin-bottom: 18px;
    }

    .brand-box img {
        height: 62px;
        object-fit: contain;
    }

    .apply-hero {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .apply-hero-left {
        flex: 1 1 560px;
        max-width: 700px;
    }

    .apply-hero-right {
        flex: 0 0 300px;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid rgba(38,182,183,0.20);
        background: rgba(38,182,183,0.10);
        color: var(--sf-accent-dark);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .title {
        margin: 0;
        color: var(--sf-primary);
        font-size: clamp(34px, 4.5vw, 56px);
        line-height: 1.02;
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .subtitle {
        margin: 14px 0 0;
        color: var(--sf-muted);
        font-size: 16px;
        line-height: 1.8;
        max-width: 760px;
    }

    .summary-card {
        padding: 20px 18px;
        border-radius: 24px;
        border: 1px solid rgba(44,83,119,0.08);
        background: rgba(255,255,255,0.70);
        box-shadow: var(--sf-shadow-soft);
    }

    .summary-card h4 {
        margin: 0 0 14px;
        font-size: 13px;
        color: var(--sf-muted);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .10em;
    }

    .summary-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(44,83,119,0.08);
    }

    .summary-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .summary-label {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .10em;
        text-transform: uppercase;
        color: var(--sf-muted);
    }

    .summary-value {
        display: block;
        color: var(--sf-primary);
        font-size: 15px;
        font-weight: 800;
        line-height: 1.6;
        word-break: break-word;
    }

    .apply-content {
        position: relative;
        z-index: 1;
        padding: 28px 34px 30px;
    }

    .progress-wrap {
        margin: 0 0 24px;
        padding: 20px;
        border: 1px solid rgba(44,83,119,0.08);
        border-radius: 24px;
        background: rgba(255,255,255,0.72);
        box-shadow: var(--sf-shadow-soft);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 13px;
        color: var(--sf-muted);
        font-weight: 800;
    }

    .progress-bar {
        width: 100%;
        height: 11px;
        background: #e6eef2;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .progress-fill {
        width: 33.33%;
        height: 100%;
        background: linear-gradient(90deg, var(--sf-accent) 0%, #82d4d7 100%);
        border-radius: 999px;
        transition: width .28s ease;
    }

    .progress-steps {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .progress-step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(44,83,119,0.08);
        background: rgba(255,255,255,0.70);
        color: var(--sf-muted);
        transition: all .2s ease;
    }

    .progress-step.is-active {
        border-color: rgba(38,182,183,0.18);
        background: rgba(38,182,183,0.08);
        color: var(--sf-primary);
    }

    .progress-step.is-complete {
        border-color: rgba(38,182,183,0.16);
        background: rgba(235,251,247,0.95);
        color: var(--sf-success);
    }

    .progress-step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: rgba(44,83,119,0.08);
        color: var(--sf-primary);
        font-size: 12px;
        font-weight: 900;
        flex-shrink: 0;
    }

    .progress-step.is-active .progress-step-number {
        background: var(--sf-accent);
        color: #fff;
    }

    .progress-step.is-complete .progress-step-number {
        background: var(--sf-success);
        color: #fff;
    }

    .progress-step-text {
        font-size: 13px;
        font-weight: 800;
        line-height: 1.4;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 16px;
        margin-bottom: 18px;
        font-size: 14px;
        line-height: 1.7;
    }

    .alert-success {
        background: var(--sf-success-bg);
        color: var(--sf-success);
        border: 1px solid #bfe8de;
    }

    .alert-danger {
        background: var(--sf-danger-bg);
        color: #991b1b;
        border: 1px solid #fecaca;
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
        border: 1px solid rgba(44,83,119,0.08);
        border-radius: 28px;
        background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(252,254,254,0.90) 100%);
        padding: 24px;
        box-shadow: var(--sf-shadow-soft);
    }

    .step-title {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 900;
        color: var(--sf-primary);
        letter-spacing: -0.02em;
    }

    .step-caption {
        margin: 0 0 18px;
        color: var(--sf-muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .fields-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .field {
        width: 100%;
        padding: 16px;
        border: 1px solid rgba(44,83,119,0.08);
        border-radius: 20px;
        background: rgba(255,255,255,0.85);
        transition: border-color .2s ease, box-shadow .2s ease, transform .16s ease;
    }

    .field.full-width {
        grid-column: 1 / -1;
    }

    .field:hover {
        border-color: rgba(38,182,183,0.18);
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.04);
        transform: translateY(-1px);
    }

    .field label {
        display: block;
        font-size: 13px;
        font-weight: 850;
        color: var(--sf-text);
        margin-bottom: 8px;
    }

    .required-mark {
        color: var(--sf-danger);
        margin-left: 4px;
    }

    .input,
    .select,
    .textarea,
    .file-input {
        width: 100%;
        border: 1px solid var(--sf-border);
        border-radius: 16px;
        background: #fff;
        padding: 13px 14px;
        font-size: 14px;
        color: var(--sf-text);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .textarea {
        min-height: 136px;
        resize: vertical;
    }

    .file-input {
        padding: 12px;
        background: linear-gradient(180deg, #fbfdfd 0%, #f7fbfc 100%);
    }

    .input:focus,
    .select:focus,
    .textarea:focus,
    .file-input:focus {
        outline: none;
        border-color: var(--sf-accent);
        box-shadow: 0 0 0 4px rgba(38, 182, 183, 0.10);
    }

    .input.invalid,
    .select.invalid,
    .textarea.invalid,
    .file-input.invalid {
        border-color: var(--sf-danger);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.08);
    }

    .help {
        margin-top: 8px;
        font-size: 12px;
        color: var(--sf-muted);
        line-height: 1.6;
    }

    .error-text {
        display: none;
        margin-top: 7px;
        font-size: 12px;
        color: var(--sf-danger);
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
        color: var(--sf-text);
        padding: 12px 14px;
        border: 1px solid rgba(44,83,119,0.08);
        border-radius: 14px;
        background: linear-gradient(180deg, #fafcfd 0%, #f6fbfc 100%);
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .checkbox-item:hover {
        border-color: rgba(38,182,183,0.18);
        background: #f3fbfb;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.03);
    }

    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--sf-accent);
        flex-shrink: 0;
    }

    .actions-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 28px;
        flex-wrap: wrap;
    }

    .btn {
        border: none;
        border-radius: 16px;
        min-height: 50px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 850;
        cursor: pointer;
        transition: transform .15s ease, background .2s ease, box-shadow .2s ease, filter .2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--sf-accent) 0%, #39c7c8 100%);
        color: #fff;
        box-shadow: 0 12px 22px rgba(38, 182, 183, 0.18);
    }

    .btn-primary:hover {
        filter: saturate(1.04);
    }

    .btn-secondary {
        background: rgba(255,255,255,0.80);
        color: var(--sf-primary);
        border: 1px solid rgba(44,83,119,0.12);
    }

    .btn-secondary:hover {
        background: #eef4f6;
    }

    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, var(--sf-accent) 0%, #39c7c8 100%);
        color: #fff;
        margin-top: 10px;
        box-shadow: 0 14px 24px rgba(38, 182, 183, 0.22);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 22px;
        text-decoration: none;
        color: var(--sf-primary);
        font-size: 14px;
        font-weight: 850;
    }

    @media (max-width: 900px) {
        .apply-topbar,
        .apply-content {
            padding-left: 20px;
            padding-right: 20px;
        }

        .fields-grid {
            grid-template-columns: 1fr;
        }

        .field.full-width {
            grid-column: auto;
        }
    }

    @media (max-width: 768px) {
        .page {
            padding-top: 22px;
        }

        .apply-shell {
            border-radius: 24px;
        }

        .title {
            font-size: 34px;
        }

        .progress-steps {
            grid-template-columns: 1fr;
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
            border-radius: 20px;
        }
    }
</style>