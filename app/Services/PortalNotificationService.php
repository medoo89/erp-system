<?php

namespace App\Services;

use App\Mail\PortalNotificationMail;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\PortalAccount;
use App\Models\PortalIdentity;
use App\Models\PortalNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class PortalNotificationService
{
    public function notifyEmployment(
        ?Employment $employment,
        string $category,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?Model $related = null,
        bool $sendEmail = true,
    ): ?PortalNotification {
        if (! $employment) {
            return null;
        }

        $portalAccount = $this->resolvePortalAccountForEmployment($employment);

        if (! $portalAccount) {
            return null;
        }

        $notification = PortalNotification::query()->create([
            'portal_account_id' => $portalAccount->id,
            'category' => $category,
            'title' => $title,
            'message' => $message ?: $title,
            'action_type' => $category,
            'action_url' => $actionUrl ?: url('/portal/notifications'),
            'action_label' => $actionLabel ?: 'Open Portal',
            'related_type' => $related ? get_class($related) : Employment::class,
            'related_id' => $related?->getKey() ?: $employment->getKey(),
            'is_read' => false,
        ]);

        if ($sendEmail && filled($portalAccount->email)) {
            try {
                Mail::to($portalAccount->email)->send(
                    new PortalNotificationMail($portalAccount, $notification)
                );

                if (Schema::hasColumn('portal_notifications', 'emailed_at')) {
                    $notification->forceFill([
                        'emailed_at' => now(),
                    ])->save();
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $notification;
    }

    public function resolvePortalAccountForEmployment(Employment $employment): ?PortalAccount
    {
        $employmentId = $employment->getKey();

        if ($employmentId) {
            $identity = PortalIdentity::query()
                ->where('employment_id', $employmentId)
                ->where('is_current', true)
                ->latest('id')
                ->first();

            if ($identity?->portalAccount) {
                return $identity->portalAccount;
            }

            $identity = PortalIdentity::query()
                ->where('employment_id', $employmentId)
                ->latest('id')
                ->first();

            if ($identity?->portalAccount) {
                return $identity->portalAccount;
            }
        }

        $email = strtolower(trim((string) ($employment->employee_email ?? '')));

        if ($email !== '') {
            return PortalAccount::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();
        }

        return null;
    }

    public function notifySalarySlip(
        $salarySlip,
        string $event,
        ?string $customTitle = null,
        ?string $customMessage = null,
        bool $sendEmail = true,
    ): ?PortalNotification {
        $salarySlip->loadMissing(['employment']);

        $employment = $salarySlip->employment ?? null;

        $period = trim(sprintf(
            '%02d/%04d',
            (int) ($salarySlip->salary_month ?? 0),
            (int) ($salarySlip->salary_year ?? 0)
        ));

        $title = $customTitle ?: match ($event) {
            'approved' => 'Salary Slip Approved',
            'payment_sent' => 'Salary Payment Sent',
            'paid' => 'Salary Payment Confirmed',
            'bank_rejected' => 'Salary Payment Rejected',
            default => 'Salary Slip Updated',
        };

        $message = $customMessage ?: trim(implode(' ', array_filter([
            'Salary slip',
            $period !== '00/0000' ? $period : null,
            'has been updated.',
            filled($salarySlip->net_amount ?? null)
                ? 'Net amount: ' . number_format((float) $salarySlip->net_amount, 2) . ' ' . ($salarySlip->currency ?: '')
                : null,
        ])));

        $actionUrl = route('portal.salary-slips.show', $salarySlip);

        return $this->notifyEmployment(
            employment: $employment,
            category: 'salary_slip',
            title: $title,
            message: $message,
            actionUrl: $actionUrl,
            actionLabel: 'Open Salary Slip',
            related: $salarySlip,
            sendEmail: $sendEmail,
        );
    }


    public function notifyReimbursement(
        FinanceExpense $expense,
        string $event,
        ?string $customTitle = null,
        ?string $customMessage = null,
        bool $sendEmail = true,
    ): ?PortalNotification {
        $expense->loadMissing(['employment', 'preEmployment']);

        $employment = $expense->employment ?? null;

        if (! $employment && $expense->preEmployment?->employment) {
            $employment = $expense->preEmployment->employment;
        }

        if (! $employment) {
            return null;
        }

        $amount = number_format((float) ($expense->reimbursement_amount ?: $expense->amount ?: 0), 2)
            . ' '
            . ($expense->reimbursement_currency ?: $expense->currency ?: '');

        $title = $customTitle ?: match ($event) {
            'approved' => 'Reimbursement Approved',
            'rejected' => 'Reimbursement Rejected',
            'linked_salary_slip' => 'Reimbursement Added to Salary Slip',
            'paid' => 'Reimbursement Paid',
            default => 'Reimbursement Updated',
        };

        $message = $customMessage ?: trim(implode(' ', array_filter([
            'Your reimbursement claim',
            $expense->title ? '"' . $expense->title . '"' : null,
            'has been updated.',
            'Amount: ' . $amount . '.',
        ])));

        return $this->notifyEmployment(
            employment: $employment,
            category: 'reimbursement',
            title: $title,
            message: $message,
            actionUrl: route('portal.reimbursements.index'),
            actionLabel: 'Open Reimbursements',
            related: $expense,
            sendEmail: $sendEmail,
        );
    }

    public function notifyGenericEmploymentUpdate(
        ?Employment $employment,
        string $category,
        string $title,
        ?string $message = null,
        ?string $portalPath = null,
        ?Model $related = null,
        bool $sendEmail = true,
    ): ?PortalNotification {
        $url = $portalPath ? url($portalPath) : url('/portal');

        return $this->notifyEmployment(
            employment: $employment,
            category: $category,
            title: $title,
            message: $message,
            actionUrl: $url,
            actionLabel: 'Open Portal',
            related: $related,
            sendEmail: $sendEmail,
        );
    }
}
