<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\TryCatch;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $search;

    public $editing_id;

    #[Rule('required|min:3|max:50')]
    public $editing_name;

    public function create(){

        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success','Created.');

        $this->resetPage();
    }

    public function edit($id){
        $this->editing_id = $id;
        $this->editing_name = Todo::find($id)->name;
    }

    public function cancelEdit(){
        $this->reset('editing_name','editing_id');
    }

    public function update(){
        $this->validateOnly('editing_name');
        Todo::find($this->editing_id)->update([
            'name'=>$this->editing_name
        ]);

        $this->cancelEdit();
    }

    public function delete($todo_id){
        try {
            //code...
            Todo::findOrfail($todo_id)->delete();
        } catch (Exception $e) {
            //throw $th;
            session()->flash('error','failed to delete Todo');
            return;
        }

    }

    public function toggle($id){
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function render()
    {
        return view('livewire.todo-list',[
            'todos'=>Todo::latest()->where('name','like',"%{$this->search}%")->paginate(2)
        ]);
    }
}
