<x-filament-panels::page>
    @php
        $projectId = request()->query('project_id');
        $project = $projectId ? \App\Models\Project::with('client')->find($projectId) : null;

        $projectName = $project?->project_name ?? $project?->name ?? 'Project Contract';
        $projectCode = $project?->project_code ?? $project?->code ?? ($project ? ('PRJ-' . str_pad((string) $project->id, 4, '0', STR_PAD_LEFT)) : 'NEW');
        $clientName = $project?->client?->name ?? 'Select Project';

        $backUrl = $project ? url('/admin/projects/' . $project->id) : url('/admin/project-contracts');
    @endphp

    <style>
        .sf-contract-create {
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 0 56px;
        }

        .sf-contract-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            align-items: start;
            border-radius: 30px;
            padding: 34px;
            color: #ffffff;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .20), transparent 35%),
                linear-gradient(135deg, #0f2743 0%, #123b5f 48%, #145f65 100%);
            box-shadow: 0 24px 60px rgba(15, 39, 67, .20);
            margin-bottom: 22px;
        }

        .sf-contract-hero:after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            background: linear-gradient(90deg, #22d3ee, #2563eb, #facc15);
        }

        .sf-contract-kicker {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .22em;
            text-transform: uppercase;
            opacity: .78;
        }

        .sf-contract-title {
            margin-top: 8px;
            max-width: 650px;
            font-size: clamp(36px, 4.8vw, 62px);
            line-height: .92;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-contract-subtitle {
            margin-top: 14px;
            font-size: 13px;
            font-weight: 850;
            opacity: .86;
        }

        .sf-contract-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .sf-contract-btn {
            min-height: 42px;
            padding: 0 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 950;
            line-height: 1;
            text-decoration: none !important;
            border: 1px solid rgba(255,255,255,.18);
            box-shadow: 0 14px 30px rgba(15, 39, 67, .18);
        }

        .sf-contract-btn-gray {
            background: rgba(255,255,255,.18);
            color: #ffffff !important;
        }

        .sf-contract-form-shell {
            border-radius: 30px;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .09), transparent 35%),
                rgba(255, 255, 255, .94);
            border: 1px solid rgba(148, 163, 184, .20);
            box-shadow: 0 24px 70px rgba(15, 39, 67, .12);
            overflow: hidden;
        }

        .sf-contract-form-head {
            padding: 24px 28px 18px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
        }

        .sf-contract-pill {
            width: fit-content;
            padding: 7px 13px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-contract-form-title {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-contract-form-subtitle {
            margin-top: 6px;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .sf-contract-form-body {
            padding: 24px 28px 28px;
        }

        .sf-contract-create .fi-section {
            border-radius: 24px !important;
            border: 1px solid rgba(148, 163, 184, .18) !important;
            box-shadow: 0 16px 44px rgba(15, 39, 67, .08) !important;
            overflow: hidden !important;
        }

        .sf-contract-create .fi-section-header {
            padding: 18px 22px !important;
            background: rgba(248, 250, 252, .80) !important;
            border-bottom: 1px solid rgba(148, 163, 184, .13) !important;
        }

        .sf-contract-create input,
        .sf-contract-create textarea,
        .sf-contract-create select {
            border-radius: 999px !important;
        }

        .sf-contract-create textarea {
            border-radius: 22px !important;
        }

        .sf-contract-footer-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding-top: 22px;
        }

        .sf-submit-btn,
        .sf-cancel-btn {
            min-height: 44px;
            border-radius: 999px;
            padding: 0 24px;
            border: 0;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sf-submit-btn {
            color: #ffffff;
            background: linear-gradient(135deg, #10b981, #14b8a6);
            box-shadow: 0 14px 30px rgba(16, 185, 129, .20);
        }

        .sf-cancel-btn {
            color: #0f172a;
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, .24);
            box-shadow: 0 10px 24px rgba(15, 39, 67, .08);
        }

        @media (max-width: 900px) {
            .sf-contract-hero {
                grid-template-columns: 1fr;
            }

            .sf-contract-actions,
            .sf-contract-footer-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="sf-contract-create">
        <section class="sf-contract-hero">
            <div>
                <div class="sf-contract-kicker">Projects • Contracts</div>
                <div class="sf-contract-title">Add Contract</div>
                <div class="sf-contract-subtitle">
                    Project: {{ $projectName }} • Client: {{ $clientName }} • Code: {{ $projectCode }}
                </div>
            </div>

            <div class="sf-contract-actions">
                <a href="{{ $backUrl }}" class="sf-contract-btn sf-contract-btn-gray">Back to Project</a>
            </div>
        </section>

        <section class="sf-contract-form-shell">
            <div class="sf-contract-form-head">
                <div class="sf-contract-pill">Contract Information</div>
                <h2 class="sf-contract-form-title">Create Project Contract / Amendment</h2>
                <div class="sf-contract-form-subtitle">
                    Add base contract value, currency, tax record, dates, contract file and notes.
                </div>
            </div>

            <div class="sf-contract-form-body">
                <form wire:submit="create">
                    {{ $this->form }}

                    <div class="sf-contract-footer-actions">
                        <button type="submit" class="sf-submit-btn">
                            Create Contract
                        </button>

                        <a href="{{ $backUrl }}" class="sf-cancel-btn">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
