<?php namespace RolfHaug\ModelSettings\Tests;

use Illuminate\Database\Eloquent\Model;

use RolfHaug\ModelSettings\Settings;

class User extends Model
{
    use Settings;

    protected $fillable = ['name', 'email'];
}
