<?php
namespace App\Http\Controllers\Api\V1;

use App\Task;
use App\Http\Controllers\Api\V1\Controller;

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


        //return $request->user()['id'];
//        return response()->json([
//            'data' => $request->user(),
//        ]);

        $data = $request->only('title', 'description');

        $args = $request->all();
        $task = new Task();
        $task->title = $data['title'];
        $task->description  = $data['description'];
        $task->user_id = $request->user()['id'];
        $task->save();       
        return $task;
    }

    
}