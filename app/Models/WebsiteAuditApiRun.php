<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteAuditApiRun extends Model
{
    protected $fillable = [
        'website_report_id',
        'provider',
        'operation',
        'status',
        'http_status',
        'duration_ms',
        'response_summary',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'response_summary' => 'array',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(WebsiteReport::class, 'website_report_id');
    }
}
