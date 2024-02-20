<?php

namespace App\View\Components;

use Illuminate\View\View;
use Illuminate\View\Component;
use Carbon\Carbon;

class DateTime extends Component
{
    public Carbon $time;
    public bool $onlyDate;
    public bool $isCurrent;
    public string $dateString;
    public string $timeString;

    /**
     * Create a new component instance.
     */
    public function __construct(
        Carbon $time = null,
        $onlyDate = null,
    ) {
        $this->time = $time ?? Carbon::now();
        $this->onlyDate = $onlyDate ? true : false;
        $this->isCurrent = $time->greaterThanOrEqualTo(Carbon::today()->subDays(7));

        if ($time->isToday()) {
            $this->dateString = 'heute';
        } elseif ($time->isYesterday()) {
            $this->dateString = 'gestern';
        } elseif ($this->isCurrent) {
            $this->dateString = match ((int)$time->isoFormat('E')) {
                1 => 'Montag',
                2 => 'Dienstag',
                3 => 'Mittwoch',
                4 => 'Donnerstag',
                5 => 'Freitag',
                6 => 'Samstag',
                7 => 'Sonntag',
            };
        } else {
            $this->dateString = $time->format('d.m.Y');
        }
        $this->timeString = $time->format('H:i');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.datetime');
    }
}
