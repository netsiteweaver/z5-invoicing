<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public ?string $title;
    public ?string $bgClass;
    public ?string $bgStyle;

    public function __construct(?string $title = null, ?string $bgClass = null, ?string $bgStyle = null)
    {
        $this->title = $title;
        $this->bgClass = $bgClass;
        $this->bgStyle = $bgStyle;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', [
            'title' => $this->title,
            'bgClass' => $this->bgClass,
            'bgStyle' => $this->bgStyle,
        ]);
    }
}
