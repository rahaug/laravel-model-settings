<?php

use Faker\Factory;
use RolfHaug\ModelSettings\Tests\User;
use Tests\TestCase;

abstract class BaseTestCase extends TestCase
{
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
            $user = User::create(['name' => Factory::create()->name, 'email' => Factory::create()->email, 'password' => bcrypt('pass')], $overrides);
            $users->push($user);
        }

        return (count($users) > 1) ? $users : $users[0];
    }
}
