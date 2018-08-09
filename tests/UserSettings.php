<?php

namespace RolfHaug\ModelSettings\Tests;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = ['user_id', 'setting', 'value'];
}
