<?php

namespace Illuminate\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use function Pest\Gwt\scenario;
use function PHPUnit\Framework\assertEquals;


uses(TestCase::class)
    ->beforeAll(function () {
        $db = new DB;
        $db->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();
        createSchema();
    });
function createSchema()
{
    schema()->create('times', function ($table) {
        $table->increments('id');
        $table->time('time');
        $table->timestamps();
    });
}

function schema()
{
    return Eloquent::getConnectionResolver()->connection()->getSchemaBuilder();
}

it('With FirstOrNew', function () {
    $time1 = Time::query()->withCasts(['time' => 'string'])
        ->firstOrNew(['time' => '07:30']);

    Time::query()->insert(['time' => '07:30']);

    $time2 = Time::query()->withCasts(['time' => 'string'])
        ->firstOrNew(['time' => '07:30']);

    $this->assertSame('07:30', $time1->time);
    $this->assertSame($time1->time, $time2->time);
});

scenario('given and when returning non array values')
    ->given(function () {
        return 'test';
    })
    ->when(function ( $number) {
        return $number;
    })
    ->then(function ( $answer) {
      expect($answer)->toBe('test');
    });

class Time extends Eloquent
{
    protected $guarded = [];

    protected $casts = [
        'time' => 'datetime',
    ];
}