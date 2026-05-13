<?php

namespace App\Filament\Resources\BankProfiles\Pages;

use App\Filament\Resources\BankProfiles\BankProfileResource;
use App\Models\BankProfile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankProfiles extends ListRecords
{
    protected static string $resource = BankProfileResource::class;

    protected string $view = 'filament.resources.bank-profiles.pages.list-bank-profiles-boxes';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('bank_profiles', 'create')),
        ];
    }

    public function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record): string => BankProfileResource::getUrl('edit', ['record' => $record]);
    }

    public function getViewData(): array
    {
        $profiles = BankProfile::query()
            ->with(['accounts'])
            ->orderByDesc('is_default_for_invoices')
            ->orderByDesc('is_active')
            ->orderBy('bank_name')
            ->orderBy('profile_name')
            ->get();

        return [
            'profiles' => $profiles,
            'profilesCount' => $profiles->count(),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'view') ?? false);
    }

}
