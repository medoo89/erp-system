<?php

namespace App\Filament\Resources\PreEmployments;

use App\Filament\Resources\PreEmployments\Pages;
use App\Filament\Resources\PreEmployments\RelationManagers\FilesRelationManager;
use App\Filament\Resources\PreEmployments\RelationManagers\PortalFieldsRelationManager;
use App\Filament\Resources\PreEmployments\RelationManagers\PortalValuesRelationManager;
use App\Filament\Resources\PreEmployments\Schemas\PreEmploymentForm;
use App\Filament\Resources\PreEmployments\Tables\PreEmploymentsTable;
use App\Models\PreEmployment;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PreEmploymentResource extends Resource
{
    protected static ?string $model = PreEmployment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'candidate_name';

    protected static ?string $navigationLabel = 'Pre-Employment';

    protected static ?string $modelLabel = 'Pre-Employment';

    protected static ?string $pluralModelLabel = 'Pre-Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['job.project.client', 'jobApplication', 'assignedHrUser', 'files'])
            ->where('is_archived', false)
            ->where('is_declined', false)
            ->whereNull('declined_at');
    }

    public static function form(Schema $schema): Schema
    {
        return PreEmploymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->schema([
                        TextEntry::make('candidate_name')->label('Candidate')->default('-')->weight('bold'),
                        TextEntry::make('job.title')->label('Position')->default('-')->weight('bold'),
                        TextEntry::make('job.project.name')->label('Project')->default('-')->weight('bold'),
                        TextEntry::make('job.project.client.name')->label('Client')->default('-')->weight('bold'),
                        TextEntry::make('status')
                            ->label('Current Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => self::label($state))
                            ->color(fn (?string $state) => self::statusColor($state)),
                        TextEntry::make('assignedHrUser.name')->label('Operation Officer')->default('-')->weight('bold'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Process Control')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => self::label($state))
                            ->color(fn (?string $state) => self::statusColor($state)),
                        TextEntry::make('assignedHrUser.name')->label('Operation Officer')->default('-'),
                        TextEntry::make('portal_token')
                            ->label('Public Link')
                            ->formatStateUsing(fn ($state) => filled($state) ? url('/pre-employment/portal/' . $state) : '-')
                            ->url(fn ($state) => filled($state) ? url('/pre-employment/portal/' . $state) : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('portal_last_sent_at')->label('Last Sent')->dateTime('M j, Y H:i')->placeholder('-'),
                        TextEntry::make('portal_last_submitted_at')->label('Last Submitted')->dateTime('M j, Y H:i')->placeholder('-'),
                        TextEntry::make('converted_to_employment_at')->label('Converted To Employment')->dateTime('M j, Y H:i')->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Client Tracking')
                    ->schema([
                        TextEntry::make('contract_status')->label('Contract Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('caf_status')->label('CAF Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('caf_file_path')
                            ->label('CAF File')
                            ->formatStateUsing(fn ($state) => filled($state) ? 'Open File' : '-')
                            ->url(fn ($state) => filled($state) ? asset('storage/' . ltrim($state, '/')) : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('gl_status')->label('General Letter Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('gl_file_path')
                            ->label('General Letter File')
                            ->formatStateUsing(fn ($state) => filled($state) ? 'Open File' : '-')
                            ->url(fn ($state) => filled($state) ? asset('storage/' . ltrim($state, '/')) : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('client_tracking_notes')->label('Client Tracking Notes')->default('-')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Candidate Tracking')
                    ->schema([
                        TextEntry::make('medical_status')->label('Medical Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('visa_status')->label('Visa Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('travel_status')->label('Travel Status')->formatStateUsing(fn ($state) => self::label($state))->default('-'),
                        TextEntry::make('availability_date')->label('Availability Date')->date('M j, Y')->placeholder('-'),
                        TextEntry::make('candidate_tracking_notes')->label('Candidate Tracking Notes')->default('-')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Commercial')
                    ->schema([
                        TextEntry::make('expected_rate')->label('Expected Rate / Salary')->default('-'),
                        TextEntry::make('final_rate')->label('Final Approved Rate / Salary')->default('-'),
                    ])
                    ->columns(2)
                    ->visible(fn () => self::canSeeCommercial())
                    ->columnSpanFull(),

                Section::make('Notes')
                    ->schema([
                        TextEntry::make('notes')->label('Candidate / Process Notes')->default('-')->columnSpanFull(),
                        TextEntry::make('internal_notes')->label('Internal Notes')->default('-')->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PreEmploymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PortalFieldsRelationManager::class,
            PortalValuesRelationManager::class,
            FilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreEmployments::route('/'),
            'create' => Pages\CreatePreEmployment::route('/create'),
            'view' => Pages\ViewPreEmployment::route('/{record}'),
            'edit' => Pages\EditPreEmployment::route('/{record}/edit'),
        ];
    }

    protected static function label(?string $value): string
    {
        return match ($value) {
            'initiated' => 'Initiated',
            'under_preparation' => 'Under Preparation',
            'awaiting_candidate_upload' => 'Awaiting Candidate Upload',
            'documents_under_review' => 'Documents Under Review',
            'additional_documents_required' => 'Additional Documents Required',
            'pending_medical' => 'Pending Medical',
            'pending_visa' => 'Pending Visa',
            'pending_travel' => 'Pending Travel',
            'ready_for_employment' => 'Ready for Employment',
            'converted_to_employment' => 'Converted to Employment',
            'declined' => 'Declined',
            'not_started' => 'Not Started',
            'under_discussion' => 'Under Discussion',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'signed' => 'Signed',
            'pending' => 'Pending',
            'fit' => 'Fit',
            'not_fit' => 'Not Fit',
            'approved' => 'Approved',
            'expired' => 'Expired',
            'booked' => 'Booked',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'draft' => 'Draft',
            'sent' => 'Sent',
            'received_signed' => 'Received Signed',
            default => $value ? ucfirst(str_replace('_', ' ', $value)) : '-',
        };
    }

    protected static function statusColor(?string $state): string
    {
        return match ($state) {
            'initiated' => 'gray',
            'under_preparation' => 'warning',
            'awaiting_candidate_upload' => 'warning',
            'documents_under_review' => 'info',
            'additional_documents_required' => 'warning',
            'pending_medical' => 'warning',
            'pending_visa' => 'warning',
            'pending_travel' => 'warning',
            'ready_for_employment' => 'success',
            'converted_to_employment' => 'success',
            'declined' => 'danger',
            default => 'gray',
        };
    }

    protected static function canSeeCommercial(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole(['admin', 'finance']);
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('finance');
        }

        return true;
    }
}