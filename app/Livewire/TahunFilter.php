<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tahun;

class TahunFilter extends Component
{
    public $selectedTahun;

    public function mount()
    {
        $tahunAktif = Tahun::where("is_active", true)->first();
        $this->selectedTahun = session("selected_tahun_id", $tahunAktif?->id);
    }

    public function updatedSelectedTahun($value)
    {
        session(["selected_tahun_id" => $value]);
    }

    public function render()
    {
        return view("livewire.tahun-filter", [
            "tahunList" => Tahun::orderBy("tahun", "desc")->get(),
        ]);
    }
}
