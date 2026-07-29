<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteReportPage extends Model
{
    protected $fillable = [
        'website_report_id',
        'url',
        'title',
        'http_status',
        'meta',
        'scores',
        'metrics',
        'audit_data',
        'mobile_screenshot_path',
        'desktop_screenshot_path',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'scores' => 'array',
            'metrics' => 'array',
            'audit_data' => 'array',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(WebsiteReport::class, 'website_report_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(WebsiteReportFinding::class);
    }
}
