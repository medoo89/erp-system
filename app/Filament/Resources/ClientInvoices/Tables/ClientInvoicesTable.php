<?php

namespace App\Filament\Resources\ClientInvoices\Tables;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\Project;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('invoice_date', 'desc')
            ->recordUrl(fn ($record): string => ClientInvoiceResource::getUrl('view', ['record' => $record]))
            ->searchPlaceholder('Search invoice no, client, or project')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->state(fn ($record) => ($record->period_start ? $record->period_start->format('Y-m-d') : '-') . ' → ' . ($record->period_end ? $record->period_end->format('Y-m-d') : '-')),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->display_currency ?: $record->foreign_currency ?: ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('received_total')
                    ->label('Received')
                    ->state(fn ($record) => number_format((float) $record->totalPaidInInvoiceCurrency(), 2))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('remaining_total')
                    ->label('Remaining')
                    ->state(fn ($record) => number_format((float) $record->remainingBalanceInInvoiceCurrency(), 2))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ClientInvoice::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        ClientInvoice::STATUS_DRAFT => 'gray',
                        ClientInvoice::STATUS_APPROVED => 'info',
                        ClientInvoice::STATUS_SENT_TO_CLIENT => 'warning',
                        ClientInvoice::STATUS_PARTIALLY_PAID => 'warning',
                        ClientInvoice::STATUS_PAID => 'success',
                        ClientInvoice::STATUS_CANCELLED => 'danger',
                        ClientInvoice::STATUS_ISSUED => 'primary',
                        ClientInvoice::STATUS_SUBMITTED => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->options(
                        Client::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(
                        Project::query()
                            ->with('client')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(function ($project) {
                                $label = $project->client?->name ?: 'Unknown Client';
                                $label .= ' — ';
                                $label .= $project->name ?: 'Unnamed Project';

                                return [$project->id => $label];
                            })
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ClientInvoice::statusOptions()),

                Filter::make('invoice_year')
                    ->form([
                        Select::make('year')
                            ->label('Year')
                            ->options(function () {
                                $years = ClientInvoice::query()
                                    ->whereNotNull('invoice_date')
                                    ->selectRaw('DISTINCT strftime("%Y", invoice_date) as year')
                                    ->orderByDesc('year')
                                    ->pluck('year', 'year')
                                    ->toArray();

                                return ! empty($years) ? $years : [now()->format('Y') => now()->format('Y')];
                            })
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['year'] ?? null)) {
                            return $query;
                        }

                        return $query->whereYear('invoice_date', (int) $data['year']);
                    })
                    ->indicateUsing(fn (array $data): ?string => filled($data['year'] ?? null) ? 'Year: ' . $data['year'] : null),

                Filter::make('invoice_month')
                    ->form([
                        Select::make('month')
                            ->label('Month')
                            ->options([
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['month'] ?? null)) {
                            return $query;
                        }

                        return $query->whereMonth('invoice_date', (int) $data['month']);
                    })
                    ->indicateUsing(fn (array $data): ?string => filled($data['month'] ?? null) ? 'Month: ' . str_pad((string) $data['month'], 2, '0', STR_PAD_LEFT) : null),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'view')),
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'edit')),
            ])
            ->bulkActions([]);
    }
}
