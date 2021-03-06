<?php

namespace App\Http\Controllers;

use App\Project;
use App\Task;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function store(Project $project) {
        if(auth()->user()->isNot($project->owner)) {
            abort(403);
        }

        request()->validate(['body' => 'required']);
        
        $project->addTask(request('body'));

        return redirect($project->path());
    }

    public function update(Project $project, Task $task) {

        if(auth()->user()->isNot($task->project->owner)) {
            abort(403);
        }

        request()->validate(['body' => 'required']);

        $task->update(([
            'body' => request('body'),
            'completed' => request()->has('completed')
        ]));

        if(request('completed')) {
            $task->complete();
        } else {
            $task->incomplete();
        }

        return redirect($project->path());
    }
}
