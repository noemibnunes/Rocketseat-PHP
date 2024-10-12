<?php

namespace App\Livewire\Proposals;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

class Create extends Component
{
    public bool $modal = true;

    public Project $project;
    #[Validate(['required', 'email'])]
    public string $email = '';

    #[Rule(['required', 'numeric', 'gt:0'])]
    public int $hours = 0;

    public bool $agree = false;

    public function save()
    {
        
        $this->validate();
        
        if(!$this->agree) {
            $this->addError('agree', 'Você precisa concordar com os termos de uso');
            return;
        }

        $this->project->proposals()
            ->updateOrCreate(
                ['email' => $this->email],
                ['hours' => $this->hours]
            );

        $this->modal = false;
    }

    
    public function render()
    {
        return view('livewire.proposals.create');
    }
}

