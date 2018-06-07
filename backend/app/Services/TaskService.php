<?php

namespace App\Services;

use App\Task;

class TaskService
{
    // ��
    public static function createTask($data, $user_id)
    {
        $task = new Task();
        $task->title = $data['title'];
        $task->description  = $data['description'];
        $task->user_id = $user_id;
        $task->save();      
        return $task;
    }

    // ɾ
    public static function deleteTask($task_id)
    {
        $task = Task::find($task_id);
        $task->delete();
    }

    // ��
    public static function updateTask($data)
    {
    }

}