<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\WebsiteReport;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'reports' => WebsiteReport::query()->count(),
                'completed' => WebsiteReport::query()->where('status', 'completed')->count(),
                'inquiries' => Inquiry::query()->count(),
            ],
            'queue' => [
                'waiting' => DB::table('jobs')->count(),
                'failed' => DB::table('failed_jobs')->count(),
                'stalled' => WebsiteReport::query()->where('status', 'queued')->count(),
            ],
            'reports' => WebsiteReport::query()->with('user')->latest()->limit(12)->get(),
            'inquiries' => Inquiry::query()->latest()->limit(8)->get(),
            'users' => User::query()->withCount('websiteReports')->latest()->limit(8)->get(),
        ]);
    }
}
