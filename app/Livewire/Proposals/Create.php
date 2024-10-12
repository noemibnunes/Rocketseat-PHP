<?php

namespace App\Livewire\Proposals;

use App\Models\Project;
use Livewire\Component;
use App\Models\Proposal;
use Livewire\Attributes\Rule;
use App\Actions\ArrangePositions;
use Livewire\Attributes\Validate;
use App\Notifications\NewProposal;
use Illuminate\Support\Facades\DB;
use App\Livewire\Projects\Proposals;

class Create extends Component
{
    public bool $modal = false;

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

        DB::transaction(function () {
            $proposal = $this->project->proposals()
                ->updateOrCreate(
                    ['email' => $this->email],
                    ['hours' => $this->hours]
                );
            $this->arrangePositions($proposal);
        });

        $this->project->author->notify(new NewProposal($this->project));

        $this->dispatch('proposal::created');

        $this->modal = false;
    }

    public function arrangePositions(Proposal $proposal)
    {
        $query = DB::select('
            select *, row_number() over (order by hours asc) as newPosition
            from proposals
            where project_id = :project
            ', ['project' => $proposal->project_id]);
        $position = collect($query)->where('id', '=', $proposal->id)->first();
        $otherProposal = collect($query)->where('position', '=', $position->newPosition)->first();
        if ($otherProposal) {
            $proposal->update(['position_status' => 'up']);
            $oProposal = Proposal::find($otherProposal->id);
            
            $oProposal->update(['position_status' => 'down']);
        }
        ArrangePositions::run($proposal->project_id);
    }
    
    public function render()
    {
        return view('livewire.proposals.create');
    }
}

