<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use App\Models\User;

class UserAsign extends Component
{
    public $evento;
    public $users;
    public $selectedusers;
    public $asignUsers;

    public function render()
    {   
        return view('livewire.admin.user-asign');
    }

    public function mount(Evento $evento){
        $this->users = User::all();
        $this->evento = $evento;
        $this->asignUsers = $evento->users()->pluck('users.id');
        //$this->selectedusers = [1,5];
    }

    public function save()
    {
        $selusers=array();
        foreach ($this->selectedusers as $seluser) {
            if($seluser){
                array_push($selusers,$seluser);
            }
        }
        $this->evento->users()->sync($selusers);
        return redirect()->route('admin.eventos.index')->with('info', 'El Evento se actualizó con éxito');
    }
}
