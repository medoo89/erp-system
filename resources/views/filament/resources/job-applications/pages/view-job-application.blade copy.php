<x-filament-panels::page>
    <style>
        .sf-candidate-requests-wrap {
            margin-top: 2rem;
            background: #ffffff;
            border: 1px solid #dbe4ea;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .sf-candidate-requests-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #dbe4ea;
        }

        .sf-candidate-requests-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
        }

        .sf-candidate-requests-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .sf-request-card {
            background: #ffffff;
            border: 1px solid #dbe4ea;
            border-radius: 22px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        }

        .sf-request-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .sf-request-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
        }

        .sf-request-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .sf-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.8rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1px solid transparent;
            line-height: 1;
        }

        .sf-badge-type {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .sf-badge-pending {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
        }

        .sf-badge-submitted {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .sf-badge-accepted {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .sf-badge-declined {
            background: #fff1f2;
            color: #be123c;
            border-color: #fecdd3;
        }

        .sf-badge-neutral {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }

        .sf-request-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .sf-request-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 1rem 1.1rem;
        }

        .sf-request-panel-full {
            grid-column: 1 / -1;
        }

        .sf-request-label {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            font-weight: 800;
            color: #0f172a;
        }

        .sf-request-value {
            margin: 0;
            font-size: 1rem;
            line-height: 1.8;
            color: #334155;
            white-space: pre-line;
        }

        .sf-request-items {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .sf-request-item {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #dbe4ea;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .sf-empty-state {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 18px;
            padding: 2rem 1rem;
            text-align: center;
            color: #64748b;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .sf-request-top {
                flex-direction: column;
                align-items: stretch;
            }

            .sf-request-grid {
                grid-template-columns: 1fr;
            }

            .sf-request-panel-full {
                grid-column: auto;
            }
        }
    </style>

    {{ $this->infolist }}

    

        <div class="sf-candidate-requests-body">
            @forelse ($this->candidateRequests as $request)
                @php
                    $statusClass = match ($request->request_status) {
                        'pending' => 'sf-badge-pending',
                        'submitted' => 'sf-badge-submitted',
                        'accepted' => 'sf-badge-accepted',
                        'declined' => 'sf-badge-declined',
                        'reconsidered', 'closed' => 'sf-badge-neutral',
                        default => 'sf-badge-neutral',
                    };

                    $typeLabel = ucfirst(str_replace('_', ' ', (string) $request->type));
                    $statusLabel = ucfirst(str_replace('_', ' ', (string) $request->request_status));
                @endphp

                <div class="sf-request-card">
                    <div class="sf-request-top">
                        <div>
                            <h3 class="sf-request-title">{{ $request->title ?: '-' }}</h3>

                            <div class="sf-request-badges">
                                <span class="sf-badge sf-badge-type">{{ $typeLabel }}</span>
                                <span class="sf-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                        </div>

                        <div
                            x-data
                            x-on:click="
                                if (confirm('Are you sure you want to delete this request?')) {
                                    $wire.deleteCandidateRequest({{ $request->id }})
                                }
                            "
                            style="display: inline-block;"
                        >
                            <x-filament::button
                                color="danger"
                                icon="heroicon-o-trash"
                                size="sm"
                                type="button"
                            >
                                Delete Request
                            </x-filament::button>
                        </div>
                    </div>

                    <div class="sf-request-grid">
                        <div class="sf-request-panel">
                            <p class="sf-request-label">Notes</p>
                            <p class="sf-request-value">{{ $request->notes ?: '-' }}</p>
                        </div>

                        <div class="sf-request-panel">
                            <p class="sf-request-label">Due Date</p>
                            <p class="sf-request-value">{{ optional($request->due_date)?->format('M j, Y') ?: '-' }}</p>
                        </div>

                        <div class="sf-request-panel sf-request-panel-full">
                            <p class="sf-request-label">Requested Items</p>

                            <div class="sf-request-items">
                                @forelse ($request->items as $item)
                                    <span class="sf-request-item">
                                        {{ $item->label ?: '-' }}
                                        @if($item->file_format)
                                            ({{ ucfirst(str_replace('_', ' ', $item->file_format)) }})
                                        @endif
                                        {{ $item->is_required ? '[Required]' : '[Optional]' }}
                                        @if($item->allow_multiple)
                                            [Multiple]
                                        @endif
                                    </span>
                                @empty
                                    <span class="sf-request-value">-</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="sf-request-panel">
                            <p class="sf-request-label">Created At</p>
                            <p class="sf-request-value">{{ optional($request->created_at)?->format('M j, Y - H:i') ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sf-empty-state">
                    No candidate requests created yet.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>