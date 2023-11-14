<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainLayout extends Component
{
    public $title;
    public $altTitle;
    public $headerimg;
    public $notice;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $title = null,
        $altTitle = null,
        $notice = null,
    ) {
        $this->title = $title ?? 'Startseite';
        $this->altTitle = $altTitle ?? $this->title;
        $this->headerimg = rand(1, 6);
        $this->notice = $notice ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main-layout');
    }
}
