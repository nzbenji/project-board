<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_project_has_tasks()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(\App\Project::class)->raw()
        );

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())->assertSee('Test task');
    }

    public function test_only_a_owner_of_project_can_add_tasks() {

        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks/', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    public function test_task_requires_a_body() {
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(\App\Project::class)->raw()
        );

        $attributes = factory(\App\Task::class)->raw(['body' => '']);

        $this->post($project->path() . '/tasks/', $attributes)->assertSessionHasErrors('body');
    }
}
