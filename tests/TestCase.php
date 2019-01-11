<?php

use Faker\Factory;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;
use RolfHaug\ModelSettings\Tests\User;

abstract class TestCase extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->setUpDatabase();
        $this->migrateTables();
    }

    private function setUpDatabase()
    {
        $database = new DB;

        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    private function migrateTables()
    {
        DB::schema()->create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });

        DB::schema()->create('user_settings', function (Blueprint $table) {
            $table->integer('user_id')->references('id')->on('users')->unsigned();
            $table->string('setting');
            $table->string('value');
            $table->timestamps();

            $table->index('user_id');
            $table->index('setting');
        });
    }

    /**
     * @param array $overrides
     * @param int $amount
     *
     * @return \RolfHaug\ModelSettings\Tests\User
     */
    function createUser($overrides = [], $amount = 1)
    {
        $users = new \Illuminate\Database\Eloquent\Collection;
        for ($i = 0; $i < $amount; $i++) {
            $user = User::create(['name' => Factory::create()->name, 'email' => Factory::create()->email], $overrides);
            $users->push($user);
        }

        return (count($users) > 1) ? $users : $users[0];
    }
}