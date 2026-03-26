<?php

namespace App\Livewire;

use App\Models\Rank;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Rank - Oxyda Store')]
class RankDetailPage extends Component
{
    public Rank $rank;

    public function mount(Rank $rank)
    {
        // Otomatis mencari rank berdasarkan ID
        $this->rank = $rank;

        // Cek jika rank tidak aktif, lempar balik ke shop
        if (! $this->rank->is_active) {
            return redirect()->route('shop');
        }
    }

    public function render()
    {
        return view('livewire.rank-detail-page');
    }
}
