<?php

namespace App\Livewire;

use App\Models\AppSetting;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cara Pembelian - Oxyda Store')]
class HowToBuyPage extends Component
{
    public $settings;

    public function mount()
    {
        $this->settings = AppSetting::first();
    }

    public function render()
    {
        return view('livewire.how-to-buy-page');
    }
}
