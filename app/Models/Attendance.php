<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public const LUNCH = 'lunch';
    public const DINNER = 'dinner';

    public const BREAK_WINDOWS = [
        self::LUNCH  => ['start' => '12:00', 'end' => '13:00'],
        self::DINNER => ['start' => '18:00', 'end' => '19:00'],
    ];

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_ENDED = 'ended';
    public const STATUS_AUTO_ENDED = 'auto_ended';

    public const MAX_PAYABLE_MINUTES = 720;

    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'total_minutes_worked',
        'hours_worked',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_in_photo',
        'status',
        'lunch_break_start',
        'lunch_break_end',
        'lunch_break_status',
        'dinner_break_start',
        'dinner_break_end',
        'dinner_break_status',
    ];

    protected $casts = [
        'clock_in'           => 'datetime',
        'clock_out'          => 'datetime',
        'lunch_break_start'  => 'datetime',
        'lunch_break_end'    => 'datetime',
        'dinner_break_start' => 'datetime',
        'dinner_break_end'   => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function breakMinutesFor(string $type): int
    {
        $start = $this->{"{$type}_break_start"};
        $end   = $this->{"{$type}_break_end"};

        if (!$start || !$end) {
            return 0;
        }

        return max(0, Carbon::parse($end)->diffInMinutes(Carbon::parse($start)));
    }

    public function totalBreakMinutes(): int
    {
        return $this->breakMinutesFor(self::LUNCH) + $this->breakMinutesFor(self::DINNER);
    }

    public function activeBreakType(): ?string
    {
        if ($this->lunch_break_status === self::STATUS_IN_PROGRESS) {
            return self::LUNCH;
        }
        if ($this->dinner_break_status === self::STATUS_IN_PROGRESS) {
            return self::DINNER;
        }
        return null;
    }

    public static function windowFor(string $type, ?Carbon $date = null): array
    {
        $date = ($date ?? Carbon::today())->copy();
        $w = self::BREAK_WINDOWS[$type];
        return [
            'start' => $date->copy()->setTimeFromTimeString($w['start']),
            'end'   => $date->copy()->setTimeFromTimeString($w['end']),
        ];
    }

    public function autoEndExpiredBreaks(): bool
    {
        $changed = false;
        $now = Carbon::now();

        foreach ([self::LUNCH, self::DINNER] as $type) {
            if ($this->{"{$type}_break_status"} !== self::STATUS_IN_PROGRESS) {
                continue;
            }
            $start = $this->{"{$type}_break_start"};
            if (!$start) {
                continue;
            }
            $window = self::windowFor($type, Carbon::parse($start));
            if ($now->greaterThanOrEqualTo($window['end'])) {
                $this->{"{$type}_break_end"}    = $window['end'];
                $this->{"{$type}_break_status"} = self::STATUS_AUTO_ENDED;
                $changed = true;
            }
        }

        if ($changed) {
            $this->save();
        }

        return $changed;
    }
}
