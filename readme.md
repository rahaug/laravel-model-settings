# Documentation
This packages allows you to easily extend any Laravel Model and get a Settings Model.

With the settings model you can easily set and retrieve values through an elegant API.

## Installation

`composer require rolfhaug/laravel-model-settings`

If you're using a later version than Laravel 5.6 add this to your providers in `config/app.php`:
`RolfHaug\ModelSettings\ModelSettingsProvider::class,`

## Setup - create settings for User model
Use the artisan comand `make:model-settings` and pass inn the name of the model you want to create settings for.

`php artisan make:model-settings user`

Lastly, add the settings trait to your model:

`use RolfHaug\ModelSettings\Settings`;


## API & Available methods

```

$user = User:find(1);

$user->settings->set('newsletter', true);

// return value or null if setting doesn't exists
$user->settings->newsletter;

// returns all settings in an array
$user->settings->all(); 

// Destroy setting
$user->settings->delete('newsletter')


```





