<?php

namespace App\Filament\Resources\SalarySlips\Pages;

use App\Filament\Resources\SalarySlips\SalarySlipResource;
use App\Models\Employment;
use App\Models\Project;
use App\Models\SalarySlip;
use App\Services\SalarySlipGenerationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListSalarySlips extends ListRecords
{
    protected static string $resource = SalarySlipResource::class;

    protected string $view = 'filament.resources.salary-slips.pages.list-salary-slips-boxes';

    public ?string $search = '';
    public ?string $clientFilter = '';
    public ?string $projectFilter = '';
    public ?string $monthFilter = '';
    public ?string $statusFilter = '';
    public ?string $person = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'clientFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'monthFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'person' => ['except' => ''],
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSalarySlip')
                ->visible(fn () => (bool) (auth()->user()?->canErp('salary_slips', 'create') || auth()->user()?->canErp('employments', 'generate_salary_slip')))
                ->label('Generate Salary Slips')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('success')
                ->form([
                    Select::make('project_id')
                        ->label('Project')
                        ->options(fn () => Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),

                    Select::make('employment_ids')
                        ->label('Employees')
                        ->options(function ($get) {
                            $projectId = $get('project_id');

                            if (! $projectId) {
                                return [];
                            }

                            $project = Project::find($projectId);

                            if (! $project) {
                                return [];
                            }

                            $jobIds = $project->jobs()->pluck('id')->filter()->values()->all();

                            return Employment::query()
                                ->where(function ($query) use ($project, $jobIds) {
                                    if (! empty($jobIds)) {
                                        $query->whereIn('job_id', $jobIds);
                                    }

                                    $query->orWhere('project_name', $project->name);
                                })
                                ->orderBy('employee_name')
                                ->get()
                                ->mapWithKeys(fn ($item) => [
                                    $item->id => ($item->employee_name ?: 'Unknown Employee') . ' #' . $item->id,
                                ])
                                ->toArray();
                        })
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->helperText('Leave empty to generate for all employees in the selected project.'),

                    Select::make('salary_year')
                        ->label('Year')
                        ->options(function () {
                            $currentYear = (int) now()->year;
                            $years = [];

                            for ($year = $currentYear - 3; $year <= $currentYear + 2; $year++) {
                                $years[$year] = (string) $year;
                            }

                            return $years;
                        })
                        ->default((int) now()->year)
                        ->native(false)
                        ->required(),

                    Select::make('salary_month')
                        ->label('Month')
                        ->options([
                            1 => '01 - January',
                            2 => '02 - February',
                            3 => '03 - March',
                            4 => '04 - April',
                            5 => '05 - May',
                            6 => '06 - June',
                            7 => '07 - July',
                            8 => '08 - August',
                            9 => '09 - September',
                            10 => '10 - October',
                            11 => '11 - November',
                            12 => '12 - December',
                        ])
                        ->default((int) now()->month)
                        ->native(false)
                        ->required(),

                    Select::make('replace_existing')
                        ->label('If already exists')
                        ->options([
                            1 => 'Replace existing slip',
                            0 => 'Keep existing slip',
                        ])
                        ->default(1)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $project = Project::find($data['project_id']);

                    if (! $project) {
                        Notification::make()
                            ->title('Project not found')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $generated = app(SalarySlipGenerationService::class)
                            ->generateForProjectMonth(
                                $project,
                                (int) $data['salary_year'],
                                (int) $data['salary_month'],
                                $data['employment_ids'] ?? [],
                                (bool) $data['replace_existing'],
                                auth()->id()
                            );

                        Notification::make()
                            ->title('Salary slip generation completed')
                            ->body(count($generated) . ' slip(s) processed.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Could not generate salary slips')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('autoGenerateByProject')
                ->visible(fn () => (bool) (auth()->user()?->canErp('salary_slips', 'create') || auth()->user()?->canErp('employments', 'generate_salary_slip')))
                ->label('Auto Generate by Project')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->form([
                    Select::make('project_id')
                        ->label('Project')
                        ->options(fn () => Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),

                    Select::make('salary_year')
                        ->label('Year')
                        ->options(function () {
                            $currentYear = (int) now()->year;
                            $years = [];

                            for ($year = $currentYear - 3; $year <= $currentYear + 2; $year++) {
                                $years[$year] = (string) $year;
                            }

                            return $years;
                        })
                        ->default((int) now()->year)
                        ->native(false)
                        ->required(),

                    Select::make('salary_month')
                        ->label('Month')
                        ->options([
                            1 => '01 - January',
                            2 => '02 - February',
                            3 => '03 - March',
                            4 => '04 - April',
                            5 => '05 - May',
                            6 => '06 - June',
                            7 => '07 - July',
                            8 => '08 - August',
                            9 => '09 - September',
                            10 => '10 - October',
                            11 => '11 - November',
                            12 => '12 - December',
                        ])
                        ->default((int) now()->month)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $project = Project::find($data['project_id']);

                    if (! $project) {
                        Notification::make()
                            ->title('Project not found')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $generated = app(SalarySlipGenerationService::class)
                            ->generateForProjectMonth(
                                $project,
                                (int) $data['salary_year'],
                                (int) $data['salary_month'],
                                [],
                                true,
                                auth()->id()
                            );

                        Notification::make()
                            ->title('Auto generation completed')
                            ->body(count($generated) . ' slip(s) processed automatically.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Auto generation failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'create')),
        ];
    }

    protected function resolveSelectedPersonKey(): ?string
    {
        return filled($this->person) ? (string) $this->person : null;
    }

    protected function resolvePersonKeyForSlip(SalarySlip $salarySlip): string
    {
        if ($salarySlip->job_application_id) {
            return 'job_application:' . $salarySlip->job_application_id;
        }

        if ($salarySlip->employment_id) {
            return 'employment:' . $salarySlip->employment_id;
        }

        return 'salary_slip:' . $salarySlip->id;
    }

    protected function resolvePersonNameForSlip(SalarySlip $salarySlip): string
    {
        return (string) (
            $salarySlip->jobApplication?->full_name
            ?: $salarySlip->employment?->employee_name
            ?: $salarySlip->employment?->preEmployment?->candidate_name
            ?: $salarySlip->employment?->preEmployment?->jobApplication?->full_name
            ?: ('Employee #' . ($salarySlip->employment_id ?: $salarySlip->id))
        );
    }

    protected function buildStatusBreakdown(Collection $slips): array
    {
        $allStatuses = [
            SalarySlip::STATUS_DRAFT,
            SalarySlip::STATUS_APPROVED,
            SalarySlip::STATUS_SENT_TO_BANK,
            SalarySlip::STATUS_PAID,
            SalarySlip::STATUS_BANK_REJECTED,
        ];

        $counts = [];

        foreach ($allStatuses as $status) {
            $counts[$status] = (int) $slips->where('status', $status)->count();
        }

        return $counts;
    }

    protected function buildGroupedPeople(Collection $salarySlips): Collection
    {
        return $salarySlips
            ->groupBy(fn (SalarySlip $salarySlip) => $this->resolvePersonKeyForSlip($salarySlip))
            ->map(function (Collection $slips, string $personKey) {
                /** @var SalarySlip|null $latestSlip */
                $latestSlip = $slips
                    ->sortByDesc(fn (SalarySlip $salarySlip) => sprintf(
                        '%04d-%02d-%010d',
                        (int) ($salarySlip->salary_year ?? 0),
                        (int) ($salarySlip->salary_month ?? 0),
                        (int) $salarySlip->id
                    ))
                    ->first();

                $firstSlip = $slips->first();
                $statusBreakdown = $this->buildStatusBreakdown($slips);

                return [
                    'person_key' => $personKey,
                    'person_name' => $latestSlip ? $this->resolvePersonNameForSlip($latestSlip) : 'Unknown Employee',
                    'client_name' => $firstSlip?->client_name ?: ($firstSlip?->employment?->client_name ?: 'No Client'),
                    'project_name' => $firstSlip?->project_name ?: ($firstSlip?->employment?->project_name ?: 'No Project'),
                    'salary_slips_count' => $slips->count(),
                    'worked_days_total' => (float) $slips->sum(fn (SalarySlip $salarySlip) => (float) ($salarySlip->days_worked ?? 0)),
                    'paid_days_total' => (float) $slips->sum(fn (SalarySlip $salarySlip) => (float) ($salarySlip->paid_days ?? 0)),
                    'total_days_total' => (float) $slips->sum(fn (SalarySlip $salarySlip) => (float) ($salarySlip->total_days ?? 0)),
                    'net_amount_total' => (float) $slips->sum(fn (SalarySlip $salarySlip) => (float) ($salarySlip->net_amount ?? $salarySlip->net_salary ?? 0)),
                    'last_status' => $latestSlip?->status ?: SalarySlip::STATUS_DRAFT,
                    'last_period_label' => $latestSlip && $latestSlip->salary_year && $latestSlip->salary_month
                        ? sprintf('%02d / %04d', (int) $latestSlip->salary_month, (int) $latestSlip->salary_year)
                        : '-',
                    'currency' => $latestSlip?->currency ?: '-',
                    'status_breakdown' => $statusBreakdown,
                    'has_status' => collect($statusBreakdown)
                        ->filter(fn ($count) => (int) $count > 0)
                        ->keys()
                        ->values()
                        ->all(),
                    'slips' => $slips
                        ->sortByDesc(fn (SalarySlip $salarySlip) => sprintf(
                            '%04d-%02d-%010d',
                            (int) ($salarySlip->salary_year ?? 0),
                            (int) ($salarySlip->salary_month ?? 0),
                            (int) $salarySlip->id
                        ))
                        ->values(),
                ];
            })
            ->values();
    }

    protected function applyFilters(Collection $groupedPeople): Collection
    {
        return $groupedPeople
            ->filter(function (array $person) {
                if (filled($this->search)) {
                    $search = mb_strtolower(trim($this->search));
                    if (! str_contains(mb_strtolower($person['person_name']), $search)) {
                        return false;
                    }
                }

                if (filled($this->clientFilter) && ($person['client_name'] ?? '') !== $this->clientFilter) {
                    return false;
                }

                if (filled($this->projectFilter) && ($person['project_name'] ?? '') !== $this->projectFilter) {
                    return false;
                }

                if (filled($this->statusFilter)) {
                    $hasStatuses = collect($person['has_status'] ?? []);
                    if (! $hasStatuses->contains($this->statusFilter)) {
                        return false;
                    }
                }

                if (filled($this->monthFilter) && ($person['last_period_label'] ?? '') !== $this->monthFilter) {
                    return false;
                }

                return true;
            })
            ->groupBy('client_name')
            ->map(fn (Collection $clientGroup) => $clientGroup->groupBy('project_name'));
    }

    public function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record): string => SalarySlipResource::getUrl('view', ['record' => $record]);
    }

    public function getViewData(): array
    {
        $salarySlips = SalarySlip::query()
            ->with([
                'jobApplication',
                'employment',
                'employment.preEmployment',
                'employment.preEmployment.jobApplication',
                'days',
            ])
            ->latest('created_at')
            ->get();

        $groupedPeople = $this->buildGroupedPeople($salarySlips);
        $selectedPersonKey = $this->resolveSelectedPersonKey();
        $selectedPerson = $groupedPeople->firstWhere('person_key', $selectedPersonKey);

        $clientOptions = $groupedPeople->pluck('client_name')->filter()->unique()->sort()->values();
        $projectOptions = $groupedPeople->pluck('project_name')->filter()->unique()->sort()->values();
        $monthOptions = $groupedPeople->pluck('last_period_label')->filter()->unique()->sort()->values();

        $statusOptions = collect(SalarySlip::statusLabels())
            ->keys()
            ->values();

        return [
            'groupedPeople' => $groupedPeople,
            'selectedPerson' => $selectedPerson,
            'selectedPersonKey' => $selectedPersonKey,
            'peopleCount' => $groupedPeople->count(),
            'salarySlipsCount' => $salarySlips->count(),
            'clientOptions' => $clientOptions,
            'projectOptions' => $projectOptions,
            'monthOptions' => $monthOptions,
            'statusOptions' => $statusOptions,
            'statusLabels' => SalarySlip::statusLabels(),
            'filteredGroupedTree' => $this->applyFilters($groupedPeople),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }

}
