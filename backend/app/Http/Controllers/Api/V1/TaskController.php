<?php
namespace App\Http\Controllers\Api\V1;

use App\Task;
use App\Http\Controllers\Api\V1\Controller;
use App\Services\TaskService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;


class TaskController extends Controller
{
       

    public function store(Request $request) 
    {
        $this->validate($request, [
                'title' => 'required|max:255', 
                'description' => 'required',
            ]);

        $data = $request->only('title', 'description');       

        return TaskService::createTask($data, Auth::user()['id']);        
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $task = $user->tasks()->find($id);

        if (is_null($task))
            return "Non exist Task";

        $task->delete();

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
                'title' => 'required|max:255', 
                'description' => 'required',
            ]);

        $user = Auth::user();
        $task = $user->tasks()->find($id);
        if (is_null($task))
            return "Non exist Task";

        $task->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        return response()->json($task);
    }



    public function show($id)
    {
        $task = Task::findOrFail($id);
        return $task;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tasks = $user->tasks()->orderBy('created_at','desc')->paginate(5)->toArray();
        

        return $tasks;
    }

    
}