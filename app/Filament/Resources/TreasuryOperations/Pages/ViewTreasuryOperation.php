<?php

namespace App\Filament\Resources\TreasuryOperations\Pages;

use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use App\Models\TreasuryOperation;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTreasuryOperation extends ViewRecord
{
    protected static string $resource = TreasuryOperationResource::class;

    protected string $view = 'filament.resources.treasury-operations.pages.view-treasury-operation-premium';

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Treasury Operation #' . $this->record->id;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $type = $this->record->operation_type ?: 'Operation';
        $currency = $this->record->currency ?: '-';
        $amount = number_format((float) ($this->record->amount ?? 0), 2);

        return "Type: {$type} · Amount: {$amount} {$currency}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Operations')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(TreasuryOperationResource::getUrl('index')),

            EditAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'edit')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
