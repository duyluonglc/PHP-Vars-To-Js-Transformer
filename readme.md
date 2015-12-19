# Transform PHP Vars to JavaScript

[![Build Status](https://travis-ci.org/laracasts/PHP-Vars-To-Js-Transformer.png?branch=master)](https://travis-ci.org/laracasts/PHP-Vars-To-Js-Transformer)

Often, you'll find yourself in situations, where you want to pass some server-side string/array/collection/whatever
to your JavaScript. Traditionally, this can be a bit of a pain - especially as your app grows.

This package simplifies the process drastically.

This source fo

## Installation

Begin by installing this package through Composer.

```js
{
    "require": {
		"laracasts/utilities": "~2.0"
	}
}
```

> If you use Laravel 4: instead install `~1.0` of this package (and use the documentation for that release). For Laravel 5 (or non-Laravel), `~2.0` will do the trick!

### Laravel Users

If you are a Laravel user, there is a service provider you can make use of to automatically prepare the bindings and such.

```php

// config/app.php

'providers' => [
    '...',
    'Laracasts\Utilities\JavaScript\JavaScriptServiceProvider'
];
```

When this provider is booted, you'll gain access to a helpful `JavaScript` facade, which you may use in your controllers.

```php
public function index()
{
    JavaScript::put([
        'foo' => 'bar',
        'user' => User::first(),
        'age' => 29
    ]);

    JavaScript::put('messages', ['hello', 'hi']);

    return View::make('hello');
}
```

> In Laravel 5, of course add `use JavaScript;` to the top of your controller.

Add render() to export variables to views. For example:

```
<body>
    <h1>My Page</h1>

    {!! JavaScript::render() !!} // <-- Variables prepended to this view
</body>
```

Using the code above, you'll now be able to access `foo`, `user`, and `age` from your JavaScript.

```js
console.log(foo); // bar
console.log(user); // User Obj
console.log(age); // 29
console.log(messages); //array of messages
```
I use jquery extend object to keep existing attributes

Naturally, you can change this default to a different view. See below.

### Defaults

If using Laravel, there are only two configuration options that you'll need to worry about. First, publish the default configuration.

```bash
php artisan vendor:publish
```

This will add a new configuration file to: `config/javascript.php`.

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JavaScript Namespace
    |--------------------------------------------------------------------------
    |
    | By default, we'll add variables to the global window object. However,
    | it's recommended that you change this to some namespace - anything.
    | That way, you can access vars, like "SomeNamespace.someVariable."
    |
    */
    'js_namespace' => 'window',
	
	/*
    |--------------------------------------------------------------------------
    | Use jquery extend
    |--------------------------------------------------------------------------
    |
    | Use jquery to extend object then existing attributes will not be removed
    |
    */
    'use_jquery_extend' => 'true',

    
];
```


[View the license](https://github.com/laracasts/PHP-Vars-To-Js-Transformer/blob/master/LICENSE) for this repo.
