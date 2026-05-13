<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalIdentity extends Model
{
    use HasFactory;

    public const STAGE_JOB_APPLICATION = 'job_application';
    public const STAGE_PRE_EMPLOYMENT = 'pre_employment';
    public const STAGE_EMPLOYMENT = 'employment';
    public const STAGE_ARCHIVED = 'archived';

    protected $fillable = [
        'portal_account_id',
        'job_application_id',
        'pre_employment_id',
        'employment_id',
        'current_stage',
        'is_current',
        'linked_at',
        'unlinked_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'linked_at' => 'datetime',
        'unlinked_at' => 'datetime',
    ];

    public function portalAccount()
    {
        return $this->belongsTo(PortalAccount::class);
    }

    public static function stageOptions(): array
    {
        return [
            self::STAGE_JOB_APPLICATION => 'Job Application',
            self::STAGE_PRE_EMPLOYMENT => 'Pre-Employment',
            self::STAGE_EMPLOYMENT => 'Employment',
            self::STAGE_ARCHIVED => 'Archived',
        ];
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }


}
