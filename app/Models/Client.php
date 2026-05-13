<?php

namespace App\Models;

use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'notes',
        'is_active',
        'is_archived',
        'archive_reason',
        'archived_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $client) {
            if (blank($client->code) && filled($client->name)) {
                $client->code = app(CodeGeneratorService::class)
                    ->generateClientCode($client->name);
            }
        });

        static::updating(function (self $client) {
            if (blank($client->code) && filled($client->name)) {
                $client->code = app(CodeGeneratorService::class)
                    ->generateClientCode($client->name, $client->id);
            }
        });
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function financeProfiles()
    {
        return $this->hasMany(CandidateFinanceProfile::class, 'client_id')
            ->latest('id');
    }

    public function financeExpenses()
    {
        return $this->hasMany(FinanceExpense::class, 'client_id')
            ->latest('expense_date')
            ->latest('id');
    }

    public function paymentStructures()
    {
        return $this->hasMany(ClientPaymentStructure::class, 'client_id')
            ->latest('effective_from')
            ->latest('id');
    }
}