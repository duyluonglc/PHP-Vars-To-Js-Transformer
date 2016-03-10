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
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/duyluonglc/PHP-Vars-To-Js-Transformer"
        }
    ],
    "require": {
        ...............
		"javascript/utilities": "dev-master"
	}
}
```

### Laravel 5.1 Users

```php

// config/app.php

'providers' => [
    '...',
    'JavaScript\Utilities\JavaScript\JavaScriptServiceProvider'
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
You can use jquery to extend objects and keep existing attributes by call Javascript::extend($var) instead use put method

You can change default namespace = window of variables by change config. See below.

### Defaults

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
    
];
```