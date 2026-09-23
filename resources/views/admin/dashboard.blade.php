@extends('layouts.admin')

@section('title', 'Admin Overview')

@section('content')
<div class="admin-shell">
    <header class="admin-page-head">
        <div>
            <span class="admin-kicker"><i></i> Live business intelligence</span>
            <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ str(auth()->user()->name)->before(' ') }}.</h1>
            <p>Reports, opportunities and platform health—without the noise.</p>
        </div>
        <div class="admin-timestamp">
            <i class="bi bi-activity"></i>
            <span>System snapshot<small>{{ now()->format('d M Y · h:i A') }}</small></span>
        </div>
    </header>

    <section class="admin-stat-grid" aria-label="Platform totals">
        <article class="admin-stat admin-stat-lime">
            <span><i class="bi bi-people"></i> Registered users</span>
            <strong>{{ number_format($stats['users']) }}</strong>
            <small>Customer accounts</small>
        </article>
        <article class="admin-stat admin-stat-violet">
            <span><i class="bi bi-radar"></i> Total reports</span>
            <strong>{{ number_format($stats['reports']) }}</strong>
            <small>Website audits requested</small>
        </article>
        <article class="admin-stat admin-stat-aqua">
            <span><i class="bi bi-check2-circle"></i> Completed</span>
            <strong>{{ number_format($stats['completed']) }}</strong>
            <small>Reports successfully delivered</small>
        </article>
        <article class="admin-stat admin-stat-coral">
            <span><i class="bi bi-chat-heart"></i> Inquiries</span>
            <strong>{{ number_format($stats['inquiries']) }}</strong>
            <small>Potential projects received</small>
        </article>
    </section>

    <section class="admin-queue {{ $queue['waiting'] > 0 || $queue['failed'] > 0 || $queue['stalled'] > 0 ? 'needs-attention' : '' }}">
        <div class="admin-queue-icon"><i class="bi {{ $queue['waiting'] > 0 || $queue['failed'] > 0 || $queue['stalled'] > 0 ? 'bi-exclamation-triangle' : 'bi-shield-check' }}"></i></div>
        <div>
            <span class="admin-panel-label">Queue health</span>
            <h2>{{ $queue['waiting'] > 0 || $queue['stalled'] > 0 ? 'The audit worker needs attention.' : ($queue['failed'] > 0 ? 'Some jobs have failed.' : 'All background systems are clear.') }}</h2>
            <p>
                @if ($queue['waiting'] > 0)
                    {{ $queue['waiting'] }} {{ str('job')->plural($queue['waiting']) }} waiting. Start the Hostinger cron worker to process queued reports.
                @elseif ($queue['stalled'] > 0)
                    {{ $queue['stalled'] }} queued {{ str('report')->plural($queue['stalled']) }} have no waiting job. Re-dispatch them before running the worker.
                @elseif ($queue['failed'] > 0)
                    Inspect failed jobs over SSH before retrying them.
                @else
                    There are no jobs waiting or failed at this moment.
                @endif
            </p>
        </div>
        <div class="admin-queue-counts">
            <span><strong>{{ $queue['waiting'] }}</strong> waiting</span>
            <span><strong>{{ $queue['failed'] }}</strong> failed</span>
            <span><strong>{{ $queue['stalled'] }}</strong> queued reports</span>
        </div>
    </section>

    <section class="admin-mail-panel">
        <div class="admin-mail-copy">
            <div class="admin-mail-icon"><i class="bi bi-envelope-check"></i></div>
            <div>
                <span class="admin-panel-label">Email delivery</span>
                <h2>SMTP connection test</h2>
                <p>This sends immediately through the effective production configuration—without using the queue.</p>
            </div>
        </div>

        <div class="admin-mail-settings" aria-label="Effective mail configuration">
            <span><small>Mailer</small><strong>{{ $mailConfig['mailer'] }}</strong></span>
            <span><small>Server</small><strong>{{ $mailConfig['host'] }}:{{ $mailConfig['port'] }}</strong></span>
            <span><small>Scheme</small><strong>{{ $mailConfig['scheme'] }}</strong></span>
            <span><small>Credentials</small><strong class="{{ $mailConfig['username'] && $mailConfig['password_set'] ? 'is-ready' : 'is-missing' }}">{{ $mailConfig['username'] && $mailConfig['password_set'] ? 'Configured' : 'Incomplete' }}</strong></span>
            <span><small>From</small><strong>{{ $mailConfig['from'] }}</strong></span>
        </div>

        @if (session('mail_diagnostic'))
            @php($mailResult = session('mail_diagnostic'))
            <div class="admin-mail-result {{ $mailResult['success'] ? 'is-success' : 'is-error' }}" role="status">
                <i class="bi {{ $mailResult['success'] ? 'bi-check-circle' : 'bi-x-octagon' }}"></i>
                <div>
                    <strong>{{ $mailResult['success'] ? 'SMTP test accepted' : 'SMTP test failed' }}</strong>
                    <p>{{ $mailResult['message'] }}</p>
                    @if (! empty($mailResult['detail']))<code>{{ $mailResult['detail'] }}</code>@endif
                </div>
            </div>
        @endif

        <form class="admin-mail-form" method="POST" action="{{ route('admin.mail.test') }}">
            @csrf
            <div>
                <label for="mail-test-email">Deliver test message to</label>
                <input id="mail-test-email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required>
                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <button type="submit"><i class="bi bi-send"></i> Send live test</button>
        </form>
    </section>

    <section class="admin-panel" id="reports">
        <div class="admin-panel-head">
            <div><span class="admin-panel-label">Audit operations</span><h2>Recent reports</h2></div>
            <span>{{ $stats['reports'] }} total</span>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Website</th><th>Customer</th><th>Status</th><th>Progress</th><th>Requested</th><th></th></tr></thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td><strong>{{ $report->domain }}</strong><small>{{ $report->website_title ?: $report->current_stage }}</small></td>
                            <td><strong>{{ $report->user->name }}</strong><small>{{ $report->user->email }}</small></td>
                            <td><span class="admin-status status-{{ $report->status }}"><i></i>{{ ucfirst($report->status) }}</span></td>
                            <td><div class="admin-progress"><span style="width: {{ $report->progress }}%"></span></div><small>{{ $report->progress }}%</small></td>
                            <td>{{ $report->created_at->format('d M Y') }}<small>{{ $report->created_at->format('h:i A') }}</small></td>
                            <td><a class="admin-row-action" href="{{ route('reports.show', $report) }}" aria-label="Open {{ $report->domain }} report"><i class="bi bi-arrow-up-right"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="admin-empty">No website reports have been requested yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-split-grid">
        <section class="admin-panel" id="inquiries">
            <div class="admin-panel-head">
                <div><span class="admin-panel-label">Sales pipeline</span><h2>Latest inquiries</h2></div>
                <span>{{ $stats['inquiries'] }} total</span>
            </div>
            <div class="admin-card-list">
                @forelse ($inquiries as $inquiry)
                    <article class="admin-inquiry">
                        <div class="admin-avatar">{{ strtoupper(substr($inquiry->name, 0, 1)) }}</div>
                        <div>
                            <div class="admin-list-title"><strong>{{ $inquiry->name }}</strong><time>{{ $inquiry->created_at->diffForHumans() }}</time></div>
                            <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
                            <p>{{ str($inquiry->message)->limit(125) }}</p>
                            <span>{{ str($inquiry->service)->replace('-', ' ')->title() }}</span>
                            @if ($inquiry->budget)<span>{{ $inquiry->budget }}</span>@endif
                        </div>
                    </article>
                @empty
                    <div class="admin-empty">No project inquiries yet.</div>
                @endforelse
            </div>
        </section>

        <section class="admin-panel" id="users">
            <div class="admin-panel-head">
                <div><span class="admin-panel-label">Accounts</span><h2>Newest users</h2></div>
                <span>{{ $stats['users'] }} total</span>
            </div>
            <div class="admin-card-list">
                @forelse ($users as $user)
                    <article class="admin-user-row">
                        <div class="admin-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div><strong>{{ $user->name }}</strong><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></div>
                        <span>{{ $user->website_reports_count }} {{ str('report')->plural($user->website_reports_count) }}</span>
                    </article>
                @empty
                    <div class="admin-empty">No customer accounts yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
