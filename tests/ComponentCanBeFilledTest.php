<?php

namespace Tests;

use Livewire\Component;
use Livewire\LivewireManager;
use Illuminate\Database\Eloquent\Model;

class ComponentCanBeFilledTest extends TestCase
{
    /** @test */
    public function can_fill_from_an_array()
    {
        $component = app(LivewireManager::class)->test(ComponentWithFillableProperties::class);

        $component->assertSee('public');
        $component->assertSee('protected');
        $component->assertSee('private');

        $component->call('callFill', [
            'publicProperty' => 'Caleb',
            'protectedProperty' => 'Caleb',
            'privateProperty' => 'Caleb',
        ]);

        $component->assertSee('Caleb');
        $component->assertSee('protected');
        $component->assertSee('private');
    }

    /** @test */
    public function can_fill_from_an_object()
    {
        $component = app(LivewireManager::class)->test(ComponentWithFillableProperties::class);

        $component->assertSee('public');
        $component->assertSee('protected');
        $component->assertSee('private');

        $component->call('callFill', new User());

        $component->assertSee('Caleb');
        $component->assertSee('protected');
        $component->assertSee('private');
    }

    /** @test */
    public function can_fill_from_an_eloquent_model()
    {
        $component = app(LivewireManager::class)->test(ComponentWithFillableProperties::class);

        $component->assertSee('public');
        $component->assertSee('protected');
        $component->assertSee('private');

        $component->call('callFill', new UserModel());

        $component->assertSee('Caleb');
        $component->assertSee('protected');
        $component->assertSee('private');
    }

    /** @test */
    public function can_handle_the_id_on_an_eloquent_model()
    {
        $component = app(LivewireManager::class)->test(ComponentWithFillableProperties::class);

        $component->call('callFill', new UserModel(['id' => 1]));

        $this->assertEquals(1, $component->get('model_id'));
    }

    /** @test */
    public function if_the_model_has_no_id_there_is_no_error()
    {
        $component = app(LivewireManager::class)->test(ComponentWithFillableProperties::class);

        $component->call('callFill', new UserModel());

        $this->assertNull($component->get('model_id'));
    }
}

class User {
    public $publicProperty = 'Caleb';
    public $protectedProperty = 'Caleb';
    public $privateProperty = 'Caleb';
}

class UserModel extends Model {

    protected $guarded = [];
    public $appends = [
        'publicProperty',
        'protectedProperty',
        'privateProperty'
    ];

    public function getPublicPropertyAttribute() {
        return 'Caleb';
    }

    public function getProtectedPropertyAttribute() {
        return 'protected';
    }

    public function getPrivatePropertyAttribute() {
        return 'private';
    }
}

class ComponentWithFillableProperties extends Component
{
    public $model_id = null;
    public $publicProperty = 'public';
    protected $protectedProperty = 'protected';
    private $privateProperty = 'private';

    public function callFill($values)
    {
        $this->fill($values);
    }

    public function render()
    {
        return view('fillable-view', [
            'publicProperty' => $this->publicProperty,
            'protectedProperty' => $this->protectedProperty,
            'privateProperty' => $this->privateProperty,
        ]);
    }
}
