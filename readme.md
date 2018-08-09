# Laravel Model Settings
This package allows you to easily extend any Laravel Model, with a Settings Model. Perfect for configuration settings and personal preferences.

You can then easily set and retrieve key/value pairs through an elegant API. If the setting does not exist, `NULL` will be returned.

```
$user = User::find(1);

$user->settings->set('my_setting', 'my_value');

echo $user->settings->my_setting; // my_value

```

---

# Installation

`composer require rolfhaug/laravel-model-settings`

If you're using an older version than **Laravel 5.6**, add this to your providers in `config/app.php`.

`RolfHaug\ModelSettings\ModelSettingsProvider::class,`

## Create a new Settings model

1) Use the command to create a new settings model

`art make:model-settings --model=user`

2) Add the Settings trait to your model

`use RolfHaug\ModelSettings\Settings;`

> Tip: The command creates a migration that can be rolled back.


---

# How to use the package


```
$user = User:find(1);
```

## Set settings
```
// Single setting
$user->settings->set('newsletter', true);

/ Aarray of settings
$settings = [
	['awesome_setting' => 'awesome_value'],
	['another_setting' => 'another_value']
];

$user->settings->set($settings);
```

## Access settings
If the setting does not exist, `NULL` will be returned.
```
$user->settings->newsletter;

// Array of all available settings
$user->settings->all();
```


## Destroy setting
```
$user->settings->delete('newsletter');
```

## Scopes
```
$users = User::whereSetting('newsletter', true)->get();

$users = User::whereHasSetting('newsletter')->get();

$users = User::whereDoesntHaveSetting('newsletter')->get();

```
