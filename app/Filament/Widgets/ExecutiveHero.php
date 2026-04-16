<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ExecutiveHero extends Widget
{
    protected string $view = 'filament.widgets.executive-hero';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        return [
            'todayLabel' => now()->format('l, d M Y'),
        ];
    }
}