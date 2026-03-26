<?php

namespace App\Livewire;

use App\Models\AppSetting;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Terms of Service - Syarat & Ketentuan')]
class TermsPage extends Component
{
    public $settings;

    public function mount()
    {
        $this->settings = AppSetting::first();
    }

    public function render()
    {
        return view('livewire.terms-page');
    }
}
