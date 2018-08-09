# Documentation
This packages allows you to easily extend any Laravel Model and get a Settings Model.

With the settings model you can easily set and retrieve values through an elegant API.

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