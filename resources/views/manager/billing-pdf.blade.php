<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Report - {{ $company }}</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        h1 { color: #1e40af; font-size: 24px; margin-bottom: 5px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 30px; }
        .summary { display: flex; gap: 20px; margin-bottom: 30px; }
        .summary-card { background: #f3f4f6; padding: 15px 20px; border-radius: 8px; text-align: center; flex: 1; }
        .summary-card .value { font-size: 24px; font-weight: bold; color: #111827; }
        .summary-card .label { font-size: 12px; color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #1e40af; color: white; padding: 10px; text-align: left; font-size: 13px; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tr:nth-child(even) { background: #f9fafb; }
        .total-row { background: #1e40af !important; color: white; font-weight: bold; }
        .total-row td { border-bottom: none; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 11px; }
        @media print {
            body { margin: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1e40af; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Print / Save as PDF
        </button>
    </div>

    <h1>Billing Report</h1>
    <p class="subtitle">{{ $company }} &mdash; {{ $period }}</p>

    <div class="summary">
        <div class="summary-card">
            <div class="value">{{ $tasks->count() }}</div>
            <div class="label">Total Tasks</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $totalHours }}h</div>
            <div class="label">Total Hours</div>
        </div>
        <div class="summary-card">
            <div class="value" style="color: #16a34a;">{{ $totalAmount }} EUR</div>
            <div class="label">Total Amount</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Service</th>
                <th>Duration</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}</td>
                    <td>{{ $task->location->name ?? 'N/A' }}</td>
                    <td>{{ $task->task_description ?? 'Cleaning Service' }}</td>
                    <td>{{ $task->duration ?? 0 }} min</td>
                    <td style="text-align: right;">{{ number_format($task->calculated_price ?? 0, 2) }} EUR</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td>{{ $tasks->sum('duration') }} min</td>
                <td style="text-align: right;">{{ $totalAmount }} EUR</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} &mdash; OptiCrew Workforce Management System</p>
    </div>
</body>
</html>
