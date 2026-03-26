<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\MigrationRequest;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Migrasi - Oxyda Store')]
class MigrationDetailPage extends Component
{
    public MigrationRequest $migration;

    public function mount($uuid)
    {
        $this->migration = MigrationRequest::where('uuid', $uuid)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.migration-detail-page', [
            'settings' => AppSetting::first(),
        ]);
    }
}
