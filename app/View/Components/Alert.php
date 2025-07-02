<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class Alert extends Component
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $type;

    /**
     * The alert message.
     *
     * @var string
     */
    public $message;

    /**
     * Create the component instance.
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert');
    }

    public function renderIcon() {

        if($this->type == 'danger') {
            return 'ban';
        } elseif($this->type == 'warning') {
            return 'exclamation';
        } elseif($this->type == 'info') {
            return 'question-mark';
        } elseif($this->type == 'success') {
            return 'check';
        }

    }
}
