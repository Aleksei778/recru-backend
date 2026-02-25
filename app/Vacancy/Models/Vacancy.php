<?php

declare(strict_types=1);

namespace App\Vacancy\Models;

use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use App\Common\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Vacancy extends Model
{
    protected $table = 'vacancies';

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'employment_type',
        'work_mode',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'location',
        'published_at',
        'closed_at',
        'created_by_id',
    ];

    protected $casts = [
        'employment_type' => EmploymentType::class,
        'work_mode' => WorkMode::class,
        'status' => Status::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
