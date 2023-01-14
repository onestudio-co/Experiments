<?php

namespace Illuminate\Tests\View\Blade;

use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Mockery as m;

class BladeComponentsTest extends AbstractBladeTestCase
{
    public function testComponentsAreCompiled()
    {
        $this->assertSame('<?php $__env->startComponent(\'foo\', ["foo" => "bar"]); ?>', $this->compiler->compileString('@component(\'foo\', ["foo" => "bar"])'));
        $this->assertSame('<?php $__env->startComponent(\'foo\'); ?>', $this->compiler->compileString('@component(\'foo\')'));
    }

    public function testEndComponentsAreCompiled()
    {
        $this->compiler->newComponentHash('foo');

        $this->assertSame('<?php echo $__env->renderComponent(); ?>', $this->compiler->compileString('@endcomponent'));
    }

    public function testSlotsAreCompiled()
    {
        $this->assertSame('<?php $__env->slot(\'foo\', null, ["foo" => "bar"]); ?>', $this->compiler->compileString('@slot(\'foo\', null, ["foo" => "bar"])'));
        $this->assertSame('<?php $__env->slot(\'foo\'); ?>', $this->compiler->compileString('@slot(\'foo\')'));
    }

    public function testEndSlotsAreCompiled()
    {
        $this->assertSame('<?php $__env->endSlot(); ?>', $this->compiler->compileString('@endslot'));
    }

    public function testPropsAreExtractedFromParentAttributesCorrectlyForClassComponents()
    {
        $attributes = new ComponentAttributeBag(['foo' => 'baz', 'other' => 'ok']);

        $component = m::mock(Component::class);
        $component->shouldReceive('withName', 'test');
        $component->shouldReceive('shouldRender')->andReturn(false);

        Component::resolveComponentsUsing(fn () => $component);

        $template = $this->compiler->compileString('@component(\'Illuminate\Tests\View\Blade\ComponentStub::class\', \'test\', ["foo" => "bar"])');

        ob_start();
        eval(" ?> $template <?php endif; ");
        ob_get_clean();
    }
}

class ComponentStub extends Component
{
    public function render()
    {
        return '';
    }
}