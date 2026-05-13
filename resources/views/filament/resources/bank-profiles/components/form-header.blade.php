@php
    $record = $getRecord();
    $isEdit = filled($record?->id);

    $title = $isEdit ? 'Edit Bank Profile' : 'Create Bank Profile';

    $subtitle = $isEdit
        ? 'Update bank profile details, linked currency accounts, and automatic treasury account synchronization.'
        : 'Create an institutional bank profile and define its currency accounts. Treasury accounts will be generated automatically.';

    $badge = $isEdit
        ? (($record?->is_active === false) ? 'Inactive Bank Profile' : 'Active Bank Profile')
        : 'New Bank Profile';

    $canDelete = $isEdit && (auth()->user()?->canErp('bank_profiles', 'delete') ?? false);
@endphp

<x-filament.sf-finance-hero
    kicker="Bank Profiles › {{ $isEdit ? 'Edit' : 'Create' }}"
    :title="$title"
    :subtitle="$subtitle"
    :badge="$badge"
    :action-label="$canDelete ? 'Delete' : null"
    action-type="button"
    action-wire-click="mountAction('delete')"
    action-color="danger"
/>
