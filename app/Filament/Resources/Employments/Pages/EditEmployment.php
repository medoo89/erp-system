<?php

namespace App\Filament\Resources\Employments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditEmployment extends EditRecord
{
    protected array $erpLoginSetupData = [];
    
    protected string $view = 'filament.resources.employments.pages.edit-employment-premium';
protected static string $resource = EmploymentResource::class;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->erpLoginSetupData = [
            'create' => (bool) ($data['create_erp_user_after_save'] ?? false),
            'role' => $data['erp_login_role'] ?? 'viewer',
            'department' => $data['erp_login_department'] ?? ($data['office_department'] ?? 'admin'),
            'password' => $data['erp_login_temp_password'] ?? null,
        ];

        unset(
            $data['create_erp_user_after_save'],
            $data['erp_login_role'],
            $data['erp_login_department'],
            $data['erp_login_temp_password']
        );

        if (($data['employee_category'] ?? $this->record?->employee_category ?? 'operational') === 'office') {
            $data['client_name'] = $data['client_name'] ?? 'Sada Fezzan';
            $data['project_name'] = $data['project_name'] ?? 'Internal Office';

            if (($data['contract_type'] ?? null) === 'open_ended' || ($data['is_open_ended_contract'] ?? false)) {
                $data['is_open_ended_contract'] = true;
                $data['contract_end_date'] = null;
            }
        }

        return $data;
    }

    public function getTitle(): string
    {
        $name = $this->record?->employee_name ?: 'Employee';

        return "Edit Employment Profile — {$name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToProfile')
                ->label('Back to Profile')
                ->color('gray')
                ->url(fn () => EmploymentResource::getUrl('view', ['record' => $this->record])),

            Action::make('saveChanges')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'edit'))
                ->label('Save Changes')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Save changes')
                ->modalDescription('Are you sure you want to save these changes?')
                ->modalSubmitActionLabel('Yes, Save')
                ->action(function () {
                    $this->save();

                    Notification::make()
                        ->title('Changes saved successfully')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'delete'))
                ->label('Delete')
                ->requiresConfirmation()
                ->modalHeading('Permanent delete')
                ->modalDescription('This record will be permanently deleted and cannot be recovered. Are you sure?')
                ->modalSubmitActionLabel('Yes, Delete Permanently'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function getView(): string
    {
        return 'filament.resources.employments.pages.edit-employment-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'edit') ?? false);
    }


    protected function afterSave(): void
    {
        $this->syncErpLoginFromSetup();
    }

    protected function syncErpLoginFromSetup(): void
    {
        $employment = $this->record;

        if (! $employment) {
            return;
        }

        if ((string) ($employment->employee_category ?? 'operational') !== 'office') {
            return;
        }

        if (! (bool) ($this->erpLoginSetupData['create'] ?? false)) {
            return;
        }

        if (blank($employment->employee_email)) {
            Notification::make()
                ->title('ERP user was not created')
                ->body('Employee email is required to create ERP login.')
                ->warning()
                ->send();

            return;
        }

        $role = $this->erpLoginSetupData['role'] ?? 'viewer';
        $department = $this->erpLoginSetupData['department'] ?? ($employment->office_department ?: 'admin');
        $password = $this->erpLoginSetupData['password'] ?? null;

        $user = User::query()
            ->where('email', strtolower(trim($employment->employee_email)))
            ->first();

        $permissions = User::defaultErpPermissionsForRole($role);

        if ($user) {
            $payload = [
                'employment_id' => $user->employment_id ?: $employment->id,
                'name' => $employment->employee_name ?: $user->name,
                'is_admin' => true,
                'user_type' => User::TYPE_ADMIN,
                'erp_role' => $role,
                'erp_department' => $department,
                'erp_permissions' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
            ];

            if (filled($password)) {
                $payload['password'] = Hash::make($password);
            }

            $user->forceFill($payload)->save();

            Notification::make()
                ->title('ERP user updated')
                ->body('Default page rules were applied based on the selected role.')
                ->success()
                ->send();

            return;
        }

        User::query()->create([
            'employment_id' => $employment->id,
            'name' => $employment->employee_name,
            'email' => strtolower(trim($employment->employee_email)),
            'password' => Hash::make($password ?: 'password123'),
            'is_admin' => true,
            'user_type' => User::TYPE_ADMIN,
            'erp_role' => $role,
            'erp_department' => $department,
            'erp_permissions' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
        ]);

        Notification::make()
            ->title('ERP user created')
            ->body('Default page rules were applied based on the selected role.')
            ->success()
            ->send();
    }


}
