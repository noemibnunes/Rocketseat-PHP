<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class Index extends Component
{

    public function mount()
    {
        $this->projects = Project::all(); // ou qualquer lógica que você usar para obter os projetos
    }
    
    public function render()
    {
        return view('livewire.projects.index');
    }

    #[Computer()] // atributos novos para um metodo ou uma classe
    public function projects() {
        return Project::query()->inRandomOrder()->get();
    }
}
