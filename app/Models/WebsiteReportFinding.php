<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteReportFinding extends Model
{
    protected $fillable = [
        'website_report_id',
        'website_report_page_id',
        'category',
        'rule_key',
        'severity',
        'title',
        'description',
        'evidence',
        'recommendation',
        'impact',
        'effort',
        'source',
        'details',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(WebsiteReport::class, 'website_report_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(WebsiteReportPage::class, 'website_report_page_id');
    }
}
