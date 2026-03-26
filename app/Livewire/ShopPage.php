<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Rank;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Shop - Katalog Rank')]
class ShopPage extends Component
{
    public $settings;

    #[Url]
    public $category = '';

    #[Url]
    public $search = '';

    public function mount()
    {
        $this->settings = AppSetting::first();

        if (empty($this->category)) {
            $firstCategory = Category::where('is_active', true)->first();
            if ($firstCategory) {
                $this->category = $firstCategory->slug;
            }
        }
    }

    public function setCategory($slug)
    {
        $this->category = $slug;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $ranks = Rank::query()
            ->where('is_active', true)
            ->with('category')
            // Filter Search
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            // Filter Kategori Wajib (Hapus opsi 'all')
            ->whereHas('category', fn ($c) => $c->where('slug', $this->category))
            ->orderBy('price', 'asc')
            ->get();

        return view('livewire.shop-page', [
            'categories' => $categories,
            'ranks' => $ranks,
        ]);
    }
}
