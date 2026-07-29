<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'requested_url',
        'final_url',
        'domain',
        'website_title',
        'status',
        'current_stage',
        'progress',
        'page_limit',
        'scores',
        'summary',
        'top_recommendations',
        'tool_versions',
        'pdf_path',
        'data_path',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'summary' => 'array',
            'top_recommendations' => 'array',
            'tool_versions' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(WebsiteReportPage::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(WebsiteReportFinding::class);
    }

    public function apiRuns(): HasMany
    {
        return $this->hasMany(WebsiteAuditApiRun::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['completed', 'failed'], true);
    }
}
