<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentApplicationsOverview extends BaseWidget
{
    protected static ?string $heading = 'Recent Applications';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobApplication::query()
                    ->with('job')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime('M j, Y - H:i'),
            ])
            ->paginated(false);
    }
}