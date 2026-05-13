<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;

class ArchiveOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Archive';

    protected static ?string $title = 'Archive';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.archive-overview';

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

}
