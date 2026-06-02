<x-filament-panels::page>
    @php
        $project = $record;
        $client = $project->client ?? null;

        $projectName = $project->project_name ?? $project->name ?? 'Project';
        $projectCode = $project->project_code ?? $project->code ?? ('PRJ-' . str_pad((string) $project->id, 4, '0', STR_PAD_LEFT));
        $clientName = $client?->name ?? 'No Client';

        $viewUrl = \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $project]);
        $clientUrl = $project->client_id ? url('/admin/clients/' . $project->client_id . '/view') : url('/admin/clients');
    @endphp

    <style>
        .sf-edit-project-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 0 56px;
        }

        .sf-edit-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .22), transparent 35%),
                linear-gradient(135deg, #0f2743 0%, #123b5f 48%, #145f65 100%);
            box-shadow: 0 24px 60px rgba(15, 39, 67, .20);
        }

        .sf-edit-hero:after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            background: linear-gradient(90deg, #22d3ee, #2563eb, #facc15);
        }

        .sf-edit-hero-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
        }

        .sf-edit-kicker {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .22em;
            text-transform: uppercase;
            opacity: .78;
        }

        .sf-edit-title {
            margin-top: 8px;
            max-width: 660px;
            font-size: clamp(38px, 5vw, 64px);
            line-height: .92;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-edit-subtitle {
            margin-top: 14px;
            font-size: 13px;
            font-weight: 800;
            opacity: .86;
        }

        .sf-edit-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .sf-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            box-shadow: 0 14px 26px rgba(15, 39, 67, .16);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .sf-edit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(15, 39, 67, .22);
        }

        .sf-edit-btn-blue {
            color: #fff;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
        }

        .sf-edit-btn-gray {
            color: #fff;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.22);
        }

        .sf-edit-shell {
            margin-top: 22px;
            border-radius: 30px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
                rgba(255,255,255,.96);
            border: 1px solid rgba(15, 39, 67, .08);
            box-shadow: 0 20px 50px rgba(15, 39, 67, .08);
            overflow: hidden;
        }

        .sf-edit-shell-header {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(15, 39, 67, .08);
        }

        .sf-edit-pill {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 10px;
            letter-spacing: .22em;
            text-transform: uppercase;
            font-weight: 950;
        }

        .sf-edit-shell-title {
            margin-top: 12px;
            font-size: 22px;
            line-height: 1.1;
            color: #0f172a;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-edit-shell-subtitle {
            margin-top: 6px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .sf-edit-body {
            padding: 24px;
        }

        .sf-edit-body .fi-fo-component-ctn,
        .sf-edit-body .fi-section,
        .sf-edit-body .fi-tabs,
        .sf-edit-body .fi-ta-ctn {
            border-radius: 24px !important;
        }

        .sf-edit-body .fi-section {
            overflow: hidden;
            border: 1px solid rgba(15, 39, 67, .08) !important;
            box-shadow: 0 14px 36px rgba(15, 39, 67, .06) !important;
        }

        .sf-edit-body .fi-section-header {
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.08), transparent 35%),
                rgba(248,250,252,.92) !important;
        }

        .sf-edit-body input,
        .sf-edit-body select,
        .sf-edit-body textarea,
        .sf-edit-body .fi-input-wrp {
            border-radius: 14px !important;
        }

        .sf-edit-body .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        .sf-edit-body .fi-ac-btn-action,
        .sf-edit-body .fi-ta-actions .fi-btn {
            min-height: 38px !important;
            border-radius: 999px !important;
        }

        .sf-edit-savebar {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        @media (max-width: 1100px) {
            .sf-edit-hero-row {
                flex-direction: column;
            }

            .sf-edit-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="sf-edit-project-page">
        <section class="sf-edit-hero">
            <div class="sf-edit-hero-row">
                <div>
                    <div class="sf-edit-kicker">Projects › Edit</div>
                    <div class="sf-edit-title">{{ $projectName }}</div>
                    <div class="sf-edit-subtitle">
                        Client: {{ $clientName }} · Code: {{ $projectCode }}
                    </div>
                </div>

                <div class="sf-edit-actions">
                    <a href="{{ $clientUrl }}" class="sf-edit-btn sf-edit-btn-gray">Back Client</a>
                    <a href="{{ $viewUrl }}" class="sf-edit-btn sf-edit-btn-blue">View Project</a>
                </div>
            </div>
        </section>

        <section class="sf-edit-shell">
            <div class="sf-edit-shell-header">
                <div class="sf-edit-pill">Project Information</div>
                <div class="sf-edit-shell-title">Edit Project Details</div>
                <div class="sf-edit-shell-subtitle">
                    Update the project profile, commercial fields, and linked contract/counter records.
                </div>
            </div>

            <div class="sf-edit-body">
                {{ $this->form }}

                    <div class="sf-edit-savebar">
                        <x-filament::button type="submit">
                            Save Changes
                        </x-filament::button>

                        <x-filament::button
                            color="gray"
                            tag="a"
                            href="{{ $viewUrl }}"
                        >
                            Cancel
                        </x-filament::button>
                    </div>
            </div>
        </section>

        <section class="sf-edit-shell">
            <div class="sf-edit-shell-header">
                <div class="sf-edit-pill">Workflow</div>
                <div class="sf-edit-shell-title">Project Contracts / Counter</div>
                <div class="sf-edit-shell-subtitle">
                    Contract and counter workflow will be managed from the project workflow section after the page opens cleanly.
                </div>
            </div>

            <div class="sf-edit-body">
                <div style="border: 1px dashed rgba(15,39,67,.18); border-radius: 24px; padding: 22px; background: rgba(248,250,252,.75);">
                    <div style="font-weight: 950; color: #0f2743; font-size: 16px;">Workflow area ready</div>
                    <div style="margin-top: 6px; color: #64748b; font-size: 13px; font-weight: 700;">
                        The old broken Filament relation-manager component was removed. Next patch will add the contract/counter workflow safely.
                    </div>
                    <div style="margin-top: 16px;">
                        <a href="{{ $viewUrl }}" class="sf-edit-btn sf-edit-btn-blue">Back to Project View</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
