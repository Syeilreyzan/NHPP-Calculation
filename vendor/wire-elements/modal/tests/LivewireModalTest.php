<?php

namespace LivewireUI\Modal\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use LivewireUI\Modal\Modal;
use LivewireUI\Modal\Tests\Components\DemoModal;
use LivewireUI\Modal\Tests\Components\InvalidModal;
use LivewireUI\Modal\Tests\Models\TestUser;

class LivewireModalTest extends TestCase
{
    use RefreshDatabase;

    public function testOpenModalEventListener(): void
    {
        Schema::create('test_users', function ($table) {
            $table->id('id');
            $table->string('first_name');
            $table->timestamps();
        });

        $user = TestUser::forceCreate([
            'first_name' => 'Philo',
        ])->fresh();

        // Demo modal component
        Livewire::component('demo-modal', DemoModal::class);

        // Event attributes
        $component = 'demo-modal';
        $componentAttributes = ['user' => 1, 'number' => 42, 'message' => 'Hello World'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl',  'maxWidthClass' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false, 'destroyOnClose' => false];

        // Demo modal unique identifier
        $id = md5($component.serialize($componentAttributes));

        Livewire::test(Modal::class)
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            // Verify component is added to $components
            ->assertSet('components', [
                $id => [
                    'name' => $component,
                    // Swap the expected user id of 1 with the Eloquent model
                    'attributes' => array_merge($componentAttributes, ['user' => $user]),
                    'modalAttributes' => $modalAttributes,
                ],
            ])
            // Verify component is set to active
            ->assertSet('activeComponent', $id)
            // Verify event is emitted to client
            ->assertEmitted('activeModalComponentChanged', $id)
            // Verif if component attribute 'message' is visible
            ->assertSee(['Hello World', 'Philo', '42']);
    }

    public function testDestroyComponentEventListener(): void
    {
        // Demo modal component
        Livewire::component('demo-modal', DemoModal::class);

        $component = 'demo-modal';
        $componentAttributes = ['message' => 'Foobar'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl', 'maxWidthClass' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false, 'destroyOnClose' => false];

        // Demo modal unique identifier
        $id = md5($component.serialize($componentAttributes));

        Livewire::test(Modal::class)
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            ->assertSet('components', [
                $id => [
                    'name' => $component,
                    'attributes' => $componentAttributes,
                    'modalAttributes' => $modalAttributes,
                ],
            ])
            ->emit('destroyComponent', $id)
            ->assertSet('components', []);
    }

    public function testModalReset(): void
    {
        Livewire::component('demo-modal', DemoModal::class);

        Livewire::test(Modal::class)
            ->emit('openModal', 'demo-modal')
            ->set('components', [
                'some-component' => [
                    'name' => 'demo-modal',
                    'attributes' => 'bar',
                    'modalAttributes' => [],
                ],
            ])
            ->set('activeComponent', 'some-component')
            ->call('resetState')
            // Verify properties are reset
            ->assertSet('activeComponent', null)
            ->assertSet('components', []);
    }

    public function testIfExceptionIsThrownIfModalDoesNotImplementContract(): void
    {
        $component = InvalidModal::class;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("[{$component}] does not implement [LivewireUI\Modal\Contracts\ModalComponent] interface.");

        Livewire::component('invalid-modal', $component);
        Livewire::test(Modal::class)->emit('openModal', 'invalid-modal');
    }
}
