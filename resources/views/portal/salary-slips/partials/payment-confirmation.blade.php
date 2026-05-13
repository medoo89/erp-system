@php

    $salaryDisplayAmount = function ($slip) {
        return number_format((float) ($slip->payment_total_amount ?? $slip->net_amount ?? 0), 2);
    };

    $portalSlipRecord = $salarySlip ?? $slip ?? $record ?? null;

    $confirmationStatus = $portalSlipRecord?->employee_confirmation_status ?? null;
    $mainStatus = $portalSlipRecord?->status ?? null;
    $paymentMethod = $portalSlipRecord?->payment_method ?? null;

    $isCash = $paymentMethod === 'cash';
    $isBank = $paymentMethod === 'bank';

    $needsConfirmation = $portalSlipRecord
        && in_array($mainStatus, ['sent_to_bank', 'paid'], true)
        && in_array($confirmationStatus, [null, '', 'pending'], true);

    $methodLabel = $isCash ? 'Cash Payment' : 'Bank Transfer';

    $confirmedAt = $portalSlipRecord?->employee_confirmed_at
        ? \Carbon\Carbon::parse($portalSlipRecord->employee_confirmed_at)->format('d M Y H:i')
        : null;
@endphp

@if($portalSlipRecord)
    @if($needsConfirmation)
        <section class="sf-payment-confirm-shell sf-payment-confirm-shell--pending">
            <div class="sf-payment-confirm-head">
                <div>
                    <div class="sf-payment-confirm-kicker">Employee Receipt Confirmation</div>

                    <h2 class="sf-payment-confirm-title">
                        {{ $isCash ? 'Please confirm receiving this cash payment' : 'Please confirm receipt of this salary payment' }}
                    </h2>

                    <p class="sf-payment-confirm-text">
                        Payment method: <strong>{{ $methodLabel }}</strong>.
                        Confirming here will update the ERP record directly.
                    </p>
                </div>

                <span class="sf-payment-confirm-status">Action Required</span>
            </div>

            <div class="{{ $isCash ? 'sf-payment-confirm-grid sf-payment-confirm-grid--single' : 'sf-payment-confirm-grid' }}">
                <form method="POST" action="{{ route('portal.salary-slips.confirm-received', $portalSlipRecord) }}" class="sf-payment-choice sf-payment-choice-received">
                    @csrf

                    <div class="sf-payment-choice-icon">✓</div>

                    <div class="sf-payment-choice-body">
                        <h3>{{ $isCash ? 'Confirm Cash Received' : 'Confirm Payment Received' }}</h3>
                        <p>
                            {{ $isCash
                                ? 'I confirm that I received this salary amount in cash.'
                                : 'I confirm that I received this salary payment.'
                            }}
                        </p>
                    </div>

                    <textarea
                        name="employee_confirmation_notes"
                        class="sf-payment-note"
                        rows="2"
                        placeholder="Optional note..."
                    ></textarea>

                    <button type="submit" class="sf-payment-btn sf-payment-btn-received">
                        {{ $isCash ? 'Confirm Cash Received' : 'Confirm Received' }}
                    </button>
                </form>

                @if(! $isCash)
                    <form method="POST" action="{{ route('portal.salary-slips.not-received', $portalSlipRecord) }}" class="sf-payment-choice sf-payment-choice-not" onsubmit="return confirm('Are you sure you want to report this salary payment as not received?');">
                        @csrf

                        <div class="sf-payment-choice-icon">!</div>

                        <div class="sf-payment-choice-body">
                            <h3>Not Received</h3>
                            <p>
                                I did not receive this salary payment and I want to notify the company.
                            </p>
                        </div>

                        <textarea
                            name="employee_confirmation_notes"
                            class="sf-payment-note"
                            rows="2"
                            placeholder="Tell us what happened..."
                        ></textarea>

                        <button type="submit" class="sf-payment-btn sf-payment-btn-not">
                            Not Received
                        </button>
                    </form>
                @endif
            </div>
        </section>
    @elseif($confirmationStatus === 'received')
        <section class="sf-payment-confirm-shell sf-payment-confirm-shell--received">
            <div class="sf-payment-confirm-head">
                <div>
                    <div class="sf-payment-confirm-kicker">Employee Receipt Confirmation</div>
                    <h2 class="sf-payment-confirm-title">Receipt confirmed successfully</h2>
                    <p class="sf-payment-confirm-text">
                        The employee confirmed receiving this {{ $isCash ? 'cash payment' : 'salary payment' }}.
                        This confirmation is now linked to the ERP salary slip.
                    </p>
                </div>

                <span class="sf-payment-confirm-status sf-payment-confirm-status--received">Received</span>
            </div>

            <div class="sf-payment-confirm-summary">
                <div>
                    <strong>Confirmation Status</strong>
                    <span>Received</span>
                </div>

                <div>
                    <strong>Confirmed At</strong>
                    <span>{{ $confirmedAt ?: '-' }}</span>
                </div>

                @if($portalSlipRecord->employee_confirmation_notes)
                    <div>
                        <strong>Employee Note</strong>
                        <span>{{ $portalSlipRecord->employee_confirmation_notes }}</span>
                    </div>
                @endif
            </div>
        </section>
    @elseif($confirmationStatus === 'not_received')
        <section class="sf-payment-confirm-shell sf-payment-confirm-shell--not-received">
            <div class="sf-payment-confirm-head">
                <div>
                    <div class="sf-payment-confirm-kicker">Employee Receipt Confirmation</div>
                    <h2 class="sf-payment-confirm-title">Payment reported as not received</h2>
                    <p class="sf-payment-confirm-text">
                        The employee reported that this bank salary payment was not received.
                        The ERP salary slip should now be marked as Bank Rejected for finance follow-up.
                    </p>
                </div>

                <span class="sf-payment-confirm-status sf-payment-confirm-status--danger">Not Received</span>
            </div>

            <div class="sf-payment-confirm-summary">
                <div>
                    <strong>Confirmation Status</strong>
                    <span>Not Received</span>
                </div>

                <div>
                    <strong>Reported At</strong>
                    <span>{{ $confirmedAt ?: '-' }}</span>
                </div>

                @if($portalSlipRecord->employee_confirmation_notes)
                    <div>
                        <strong>Employee Note</strong>
                        <span>{{ $portalSlipRecord->employee_confirmation_notes }}</span>
                    </div>
                @endif
            </div>
        </section>
    @endif
@endif
