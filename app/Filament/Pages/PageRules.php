<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;

class PageRules extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Admin Settings';

    protected static ?string $navigationLabel = 'Page Rules';

    protected static ?string $title = 'Page Rules';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'page-rules';

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.page-rules';

    public $users;

    public array $roles = [];

    public array $registry = [];

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->isSuperAdmin() || auth()->user()?->canErp('access_control', 'view'),
            403
        );

        $this->users = User::query()
            ->orderByDesc('is_admin')
            ->orderBy('erp_department')
            ->orderBy('erp_role')
            ->orderBy('name')
            ->get();

        $this->roles = User::erpRoleOptions();
        $this->registry = User::erpPermissionRegistry();
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getHeading(): string
    {
        return '';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (
            auth()->user()?->isSuperAdmin()
            || auth()->user()?->canErp('access_control', 'view')
        );
    }
}
