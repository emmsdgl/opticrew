<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ContractedClient;
use App\Models\Location;
use App\Models\Task;
use App\Services\OptimizationService;

class TaskDashboard extends Component
{
    // These are public properties that the front-end can access.
    public $contractedClients;
    public $locations = [];
    public $tasks;
    public $selectedClientId;
    public $selectedLocations = []; // This will hold the IDs of the selected cabins
    public $serviceDate;          // This will be connected to the date input

    public function toggleLocation($locationId)
    {
        if (in_array($locationId, $this->selectedLocations)) {
            // If it's already selected, remove it
            $this->selectedLocations = array_diff($this->selectedLocations, [$locationId]);
        } else {
            // If it's not selected, add it
            $this->selectedLocations[] = $locationId;
        }
    }

    public function optimizeAndAssign(OptimizationService $optimizer)
    {
        // 1. Validate that a date and at least one cabin have been selected.
        if (empty($this->serviceDate) || empty($this->selectedLocations)) {
            session()->flash('error', 'Please select a service date and at least one cabin.');
            return;
        }

        // 2. Call the new Optimization Service to run the logic.
        $result = $optimizer->run($this->serviceDate, $this->selectedLocations);

        // // --- TEMPORARY DEBUGGING STEP ---
        // if ($result['status'] === 'error') {
        //     dd($result['message']); // Die and dump the exact error message
        // }
        // // --- END DEBUGGING STEP ---

        // 3. Display the result message to the user.
        if ($result['status'] === 'success') {
            session()->flash('message', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        // 4. Reset the UI and refresh the task list.
        $this->selectedLocations = []; // Clear the blue selection
        $this->tasks = Task::with('location')->get(); // Reload tasks to show the new ones on the Kanban board
    }

    /**
     * This is the "constructor" method. It runs once when the component is first loaded.
     */
    public function mount()
    {
        // 1. Get all contracted clients to populate the dropdown.
        $this->contractedClients = ContractedClient::all();

        // 2. Set a default selection. If there are any clients,
        //    select the first one by default so the page isn't empty.
        if ($this->contractedClients->isNotEmpty()) {
            $this->selectedClientId = $this->contractedClients->first()->id;
            $this->loadLocations();
        }
        
        // 3. Load all tasks for the Kanban boards.
        $this->tasks = Task::with('team.members.employee')->get();
    }

    /**
     * This is a special Livewire "lifecycle hook". It automatically runs
     * whenever the $selectedClientId property is updated by the dropdown.
     */
    public function updatedSelectedClientId($value)
    {
        $this->loadLocations();
    }

    /**
     * A helper function to load the locations for the selected client.
     */
    public function loadLocations()
    {
        $this->locations = Location::where('contracted_client_id', $this->selectedClientId)->get();
    }
    
    /**
     * This method renders the view.
     */
    public function render()
    {
        return view('livewire.admin.task-dashboard')
                ->layout('layouts.app'); // This tells Livewire: "Wrap my view in the app.blade.php file"
    }
}