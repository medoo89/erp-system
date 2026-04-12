<?php

namespace App\Filament\Resources\Employments;

use App\Filament\Resources\Employments\Pages;
use App\Filament\Resources\Employments\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Employments\RelationManagers\FilesRelationManager;
use App\Filament\Resources\Employments\RelationManagers\RotationsRelationManager;
use App\Filament\Resources\Employments\Schemas\EmploymentForm;
use App\Filament\Resources\Employments\Tables\EmploymentsTable;
use App\Models\Employment;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class EmploymentResource extends Resource
{
    protected static ?string $model = Employment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $recordTitleAttribute = 'employee_name';

    protected static ?string $navigationLabel = 'Employment';

    protected static ?string $modelLabel = 'Employment';

    protected static ?string $pluralModelLabel = 'Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'job.project.client',
                'assignedHrUser',
                'files',
                'rotations',
                'currentRotation',
                'documents',
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return EmploymentForm::configure($schema);
    }

    protected static function getCurrentOrLatestRotation($record)
    {
        return $record->currentRotation
            ?: $record->rotations
                ->sortByDesc(fn ($item) => optional($item->from_date)?->timestamp ?? 0)
                ->first();
    }

    protected static function getCurrentFileByCategory($record, string $category)
    {
        return $record->files
            ->where('category', $category)
            ->sortByDesc('version_no')
            ->firstWhere('is_current', true)
            ?: $record->files
                ->where('category', $category)
                ->sortByDesc('version_no')
                ->first();
    }

    protected static function smartContractStatus($record): string
    {
        $file = static::getCurrentFileByCategory($record, 'contract');
        $status = $file?->document_status ?: $record->contract_status;
        $expiry = $file?->expiry_date ?: $record->contract_end_date;

        if ($expiry) {
            if ($expiry->isPast()) {
                return 'Expired';
            }

            if (now()->diffInDays($expiry, false) <= 30) {
                return 'Expiring Soon';
            }
        }

        return match ($status) {
            'active' => 'Active',
            'renewal_in_progress' => 'Renewal In Progress',
            'renewed' => 'Renewed',
            'completed' => 'Completed',
            'terminated' => 'Terminated',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    protected static function contractStatusColor($record): string
    {
        $value = static::smartContractStatus($record);

        return match ($value) {
            'Active', 'Renewed' => 'success',
            'Renewal In Progress', 'Expiring Soon' => 'warning',
            'Completed' => 'info',
            'Expired', 'Terminated' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartVisaStatus($record): string
    {
        $file = static::getCurrentFileByCategory($record, 'visa');
        $status = $file?->document_status ?: $record->visa_status;
        $expiry = $file?->expiry_date ?: $record->visa_expiry_date;

        if ($expiry) {
            if ($expiry->isPast()) {
                return 'Expired';
            }

            if (now()->diffInDays($expiry, false) <= 30) {
                return 'Expiring Soon';
            }
        }

        return match ($status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'renewed' => 'Renewed',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    protected static function visaStatusColor($record): string
    {
        $value = static::smartVisaStatus($record);

        return match ($value) {
            'Approved', 'Renewed' => 'success',
            'Pending', 'Expiring Soon' => 'warning',
            'Expired', 'Rejected' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartMedicalStatus($record): string
    {
        $file = static::getCurrentFileByCategory($record, 'medical');
        $status = $file?->document_status ?: $record->medical_status;
        $expiry = $file?->expiry_date ?: $record->medical_expiry_date;

        if ($expiry) {
            if ($expiry->isPast()) {
                return 'Expired';
            }

            if (now()->diffInDays($expiry, false) <= 30) {
                return 'Expiring Soon';
            }
        }

        return match ($status) {
            'pending' => 'Pending',
            'fit' => 'Fit',
            'not_fit' => 'Not Fit',
            'renewed' => 'Renewed',
            'expired' => 'Expired',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    protected static function medicalStatusColor($record): string
    {
        $value = static::smartMedicalStatus($record);

        return match ($value) {
            'Fit', 'Renewed' => 'success',
            'Pending', 'Expiring Soon' => 'warning',
            'Expired', 'Not Fit' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartCurrentWorkStatus($record): string
    {
        $rotation = static::getCurrentOrLatestRotation($record);

        if (! $rotation) {
            return $record->current_work_status
                ? ucfirst(str_replace('_', ' ', $record->current_work_status))
                : '-';
        }

        if ($rotation->status === 'active') {
            return 'Working';
        }

        if ($rotation->status === 'scheduled') {
            return 'Pending Mobilization';
        }

        if ($rotation->status === 'completed') {
            return 'Vacation';
        }

        if ($rotation->status === 'paused') {
            return 'On Leave';
        }

        if ($rotation->status === 'cancelled') {
            return 'Inactive';
        }

        return $record->current_work_status
            ? ucfirst(str_replace('_', ' ', $record->current_work_status))
            : '-';
    }

    protected static function currentWorkStatusColor($record): string
    {
        $value = static::smartCurrentWorkStatus($record);

        return match ($value) {
            'Working' => 'success',
            'Pending Mobilization' => 'warning',
            'Vacation' => 'info',
            'On Leave' => 'gray',
            'Inactive' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartRotationStatus($record): string
    {
        $rotation = static::getCurrentOrLatestRotation($record);

        if (! $rotation) {
            return $record->rotation_status
                ? ucfirst(str_replace('_', ' ', $record->rotation_status))
                : '-';
        }

        if ($rotation->status === 'active') {
            return 'Active';
        }

        if ($rotation->status === 'scheduled' && $rotation->mobilization_date && now()->diffInDays($rotation->mobilization_date, false) <= 7) {
            return 'Travel This Week';
        }

        if ($rotation->status === 'scheduled') {
            return 'Upcoming';
        }

        if ($rotation->status === 'completed') {
            return 'Completed';
        }

        if ($rotation->status === 'paused') {
            return 'Paused';
        }

        if ($rotation->status === 'cancelled') {
            return 'Cancelled';
        }

        return ucfirst(str_replace('_', ' ', $rotation->status ?: '-'));
    }

    protected static function rotationStatusColor($record): string
    {
        $value = static::smartRotationStatus($record);

        return match ($value) {
            'Active' => 'success',
            'Upcoming', 'Travel This Week' => 'warning',
            'Completed' => 'info',
            'Paused' => 'gray',
            'Cancelled' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartTravelStatus($record): string
    {
        $rotation = static::getCurrentOrLatestRotation($record);

        $status = $rotation?->travel_status ?: $record->travel_status;

        if ($rotation?->mobilization_date && now()->greaterThanOrEqualTo($rotation->mobilization_date)) {
            return 'Completed';
        }

        return match ($status) {
            'pending_request' => 'Pending Request',
            'request_received' => 'Request Received',
            'ticket_booked' => 'Ticket Booked',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    protected static function travelStatusColor($record): string
    {
        $value = static::smartTravelStatus($record);

        return match ($value) {
            'Request Received' => 'info',
            'Pending Request' => 'warning',
            'Ticket Booked', 'Completed' => 'success',
            'Cancelled' => 'danger',
            default => 'gray',
        };
    }

    protected static function smartWorkLocation($record): string
    {
        $rotation = static::getCurrentOrLatestRotation($record);

        if ($rotation?->status === 'active') {
            return 'Outside Libya / On Rotation';
        }

        if ($rotation?->status === 'scheduled') {
            return 'Libya / Awaiting Travel';
        }

        if ($rotation?->status === 'completed') {
            return 'Libya';
        }

        return $record->work_location
            ?: $record->project_name
            ?: '-';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->schema([
                        TextEntry::make('employee_name')
                            ->label('Employee')
                            ->default('-')
                            ->weight('bold'),

                        TextEntry::make('employee_code')
                            ->label('Employee Code')
                            ->default('-')
                            ->weight('bold'),

                        TextEntry::make('position_title')
                            ->label('Position')
                            ->default('-')
                            ->weight('bold'),

                        TextEntry::make('client_name')
                            ->label('Client')
                            ->default('-')
                            ->weight('bold'),

                        TextEntry::make('project_name')
                            ->label('Project')
                            ->default('-')
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'active' => 'Active',
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'terminated' => 'Terminated',
                                default => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-',
                            })
                            ->color(fn ($state) => match ($state) {
                                'active' => 'success',
                                'on_hold' => 'warning',
                                'completed' => 'info',
                                'terminated' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('operation_officer_name')
                            ->label('Operation Officer')
                            ->default('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Current / Upcoming Rotation')
                    ->schema([
                        TextEntry::make('rotation_summary_card')
                            ->hiddenLabel()
                            ->state(function ($record) {
                                $rotation = static::getCurrentOrLatestRotation($record);

                                if (! $rotation) {
                                    return new HtmlString('
                                        <div style="border:1px dashed #cbd5e1;border-radius:20px;padding:24px;background:#f8fafc;color:#64748b;text-align:center;">
                                            No rotation records available yet.
                                        </div>
                                    ');
                                }

                                $statusBadge = match ($rotation->status) {
                                    'scheduled' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;">Scheduled</span>',
                                    'active' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Active</span>',
                                    'completed' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;">Completed</span>',
                                    'paused' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#e2e8f0;color:#334155;font-size:12px;font-weight:700;">Paused</span>',
                                    'cancelled' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;">Cancelled</span>',
                                    default => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#e2e8f0;color:#334155;font-size:12px;font-weight:700;">-</span>',
                                };

                                $travelBadge = match ($rotation->travel_status) {
                                    'pending_request' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;">Pending Request</span>',
                                    'request_received' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;">Request Received</span>',
                                    'ticket_booked' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Ticket Booked</span>',
                                    'completed' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Completed</span>',
                                    'cancelled' => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;">Cancelled</span>',
                                    default => '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#e2e8f0;color:#334155;font-size:12px;font-weight:700;">-</span>',
                                };

                                $locationStatus = static::smartWorkLocation($record);

                                $travelRequestLink = '-';
                                if (filled($rotation->travel_request_file_path)) {
                                    $travelRequestUrl = Storage::disk('public')->url($rotation->travel_request_file_path);
                                    $travelRequestLink = '<a href="' . e($travelRequestUrl) . '" target="_blank" style="display:inline-block;padding:10px 14px;border-radius:12px;background:#0f172a;color:#fff;text-decoration:none;font-weight:700;font-size:13px;">Open Travel Request</a>';
                                }

                                $ticketLink = '-';
                                if (filled($rotation->ticket_file_path)) {
                                    $ticketUrl = Storage::disk('public')->url($rotation->ticket_file_path);
                                    $ticketLink = '<a href="' . e($ticketUrl) . '" target="_blank" style="display:inline-block;padding:10px 14px;border-radius:12px;background:#0f172a;color:#fff;text-decoration:none;font-weight:700;font-size:13px;">Open Ticket</a>';
                                }

                                return new HtmlString('
                                    <div style="border:1px solid #e2e8f0;border-radius:24px;padding:24px;background:#ffffff;box-shadow:0 8px 24px rgba(15,23,42,.05);">
                                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
                                            <div>
                                                <div style="font-size:24px;font-weight:800;color:#0f172a;">🔁 ' . e($rotation->rotation_label ?: 'Rotation Record') . '</div>
                                                <div style="margin-top:8px;color:#475569;font-size:14px;">Current operational rotation summary</div>
                                            </div>
                                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                                ' . $statusBadge . '
                                                ' . $travelBadge . '
                                                ' . ($rotation->is_current ? '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:#ecfccb;color:#3f6212;font-size:12px;font-weight:700;">Current Rotation</span>' : '') . '
                                            </div>
                                        </div>

                                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:20px;">
                                            <div><strong>From Date:</strong><br>' . e($rotation->from_date?->format('M j, Y') ?? '-') . '</div>
                                            <div><strong>To Date:</strong><br>' . e($rotation->to_date?->format('M j, Y') ?? '-') . '</div>
                                            <div><strong>Mobilization Date:</strong><br>' . e($rotation->mobilization_date?->format('M j, Y') ?? '-') . '</div>
                                            <div><strong>Demobilization Date:</strong><br>' . e($rotation->demobilization_date?->format('M j, Y') ?? '-') . '</div>
                                            <div><strong>Rotation Pattern:</strong><br>' . e($rotation->rotation_pattern ?: '-') . '</div>
                                            <div><strong>Location Status:</strong><br>' . e($locationStatus) . '</div>
                                        </div>

                                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;margin-bottom:18px;">
                                            <div>
                                                <div style="font-size:13px;color:#64748b;margin-bottom:8px;">Travel Request File</div>
                                                ' . $travelRequestLink . '
                                            </div>
                                            <div>
                                                <div style="font-size:13px;color:#64748b;margin-bottom:8px;">Ticket File</div>
                                                ' . $ticketLink . '
                                            </div>
                                        </div>

                                        <div style="font-size:14px;color:#475569;line-height:1.8;">
                                            <strong>Notes:</strong><br>
                                            ' . e($rotation->notes ?: '-') . '
                                        </div>
                                    </div>
                                ');
                            })
                            ->html(),
                    ])
                    ->columnSpanFull(),

                Section::make('Employment Tracking')
                    ->schema([
                        TextEntry::make('smart_current_work_status')
                            ->label('Current Work Status')
                            ->state(fn ($record) => static::smartCurrentWorkStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::currentWorkStatusColor($record)),

                        TextEntry::make('smart_rotation_status')
                            ->label('Rotation Status')
                            ->state(fn ($record) => static::smartRotationStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::rotationStatusColor($record)),

                        TextEntry::make('rotation_pattern')
                            ->label('Rotation Pattern')
                            ->state(function ($record) {
                                $rotation = static::getCurrentOrLatestRotation($record);
                                return $rotation?->rotation_pattern ?: $record->rotation_pattern ?: '-';
                            }),

                        TextEntry::make('smart_contract_status')
                            ->label('Contract Status')
                            ->state(fn ($record) => static::smartContractStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::contractStatusColor($record)),

                        TextEntry::make('smart_medical_status')
                            ->label('Medical Status')
                            ->state(fn ($record) => static::smartMedicalStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::medicalStatusColor($record)),

                        TextEntry::make('smart_visa_status')
                            ->label('Visa Status')
                            ->state(fn ($record) => static::smartVisaStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::visaStatusColor($record)),

                        TextEntry::make('smart_travel_status')
                            ->label('Travel Status')
                            ->state(fn ($record) => static::smartTravelStatus($record))
                            ->badge()
                            ->color(fn ($record) => static::travelStatusColor($record)),

                        TextEntry::make('smart_work_location')
                            ->label('Work Location')
                            ->state(fn ($record) => static::smartWorkLocation($record)),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Document Cards')
                    ->schema([
                        TextEntry::make('document_cards')
                            ->hiddenLabel()
                            ->state(function ($record) {
                                $files = $record?->files ?? collect();

                                if ($files->isEmpty()) {
                                    return new HtmlString('<div style="border:1px dashed #cbd5e1;border-radius:20px;padding:28px;text-align:center;color:#64748b;background:#f8fafc;">No documents uploaded yet.</div>');
                                }

                                $cards = $files->sortByDesc('created_at')->map(function ($file) {
                                    $icon = match ($file->category) {
                                        'passport' => '🛂',
                                        'visa' => '🛃',
                                        'medical' => '🩺',
                                        'personal_photo' => '🖼️',
                                        'certificate' => '📜',
                                        'contract' => '📄',
                                        'rotation_document' => '🔁',
                                        'travel_request' => '✈️',
                                        'ticket' => '🎫',
                                        'internal_document' => '🗂️',
                                        default => '📁',
                                    };

                                    $category = $file->category ? ucfirst(str_replace('_', ' ', $file->category)) : '-';
                                    $version = 'V' . ($file->version_no ?: 1);
                                    $current = $file->is_current
                                        ? '<span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Current</span>'
                                        : '<span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:12px;font-weight:700;">Old</span>';

                                    $submittedBy = $file->uploaded_by_type === 'candidate' ? 'Candidate' : 'Admin';

                                    $expiry = '-';
                                    if ($file->expiry_date) {
                                        $date = $file->expiry_date->format('M j, Y');
                                        $badgeStyle = 'background:#dcfce7;color:#166534;';
                                        if ($file->expiry_date->isPast()) {
                                            $badgeStyle = 'background:#fee2e2;color:#991b1b;';
                                        } elseif (now()->diffInDays($file->expiry_date, false) <= 30) {
                                            $badgeStyle = 'background:#fef3c7;color:#92400e;';
                                        }
                                        $expiry = '<span style="display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;' . $badgeStyle . '">Expiry: ' . $date . '</span>';
                                    }

                                    $url = $file->file_path ? Storage::disk('public')->url($file->file_path) : null;
                                    $open = $url ? '<a href="' . e($url) . '" target="_blank" style="display:inline-block;margin-top:14px;padding:10px 14px;border-radius:12px;background:#0f172a;color:#fff;text-decoration:none;font-weight:700;font-size:13px;">Open File</a>' : '';

                                    return '<div style="border:1px solid #e2e8f0;border-radius:22px;background:#ffffff;padding:20px;box-shadow:0 8px 24px rgba(15,23,42,.05);"><div style="font-size:36px;line-height:1;margin-bottom:14px;">' . $icon . '</div><div style="font-size:17px;font-weight:800;color:#0f172a;margin-bottom:8px;">' . e($file->title ?: '-') . '</div><div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;"><span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:700;">' . e($category) . '</span><span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#f1f5f9;color:#334155;font-size:12px;font-weight:700;">' . e($version) . '</span>' . $current . '</div><div style="font-size:13px;color:#475569;line-height:1.8;"><div><strong>Submitted By:</strong> ' . e($submittedBy) . '</div><div><strong>Document Date:</strong> ' . e($file->document_date?->format('M j, Y') ?? '-') . '</div></div><div style="margin-top:12px;">' . $expiry . '</div>' . $open . '</div>';
                                })->implode('');

                                return new HtmlString('<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;">' . $cards . '</div>');
                            })
                            ->html(),
                    ])
                    ->columnSpanFull(),

                Section::make('Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Operations Notes')
                            ->default('-')
                            ->columnSpanFull(),

                        TextEntry::make('internal_notes')
                            ->label('Internal Notes')
                            ->default('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return EmploymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FilesRelationManager::class,
            RotationsRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployments::route('/'),
            'create' => Pages\CreateEmployment::route('/create'),
            'view' => Pages\ViewEmployment::route('/{record}'),
            'edit' => Pages\EditEmployment::route('/{record}/edit'),
        ];
    }
}
