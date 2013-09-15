FakerServiceProvider
====================

A [Faker](https://github.com/fzaninotto/Faker) service provider for [Silex](http://silex.sensiolabs.org/).

**N.B**: this provider is *locale aware*. It will automatically configure Faker
to use the most suited locale for the request.


## Usage

Initialize it using `register`. Its default behavior is to use Faker's factory
and try to guess the right locale to use.
```php
<?php

use KPhoen\Provider\FakerServiceProvider;

$app->register(new FakerServiceProvider());
```

In this example, we use a custom factory and force the locale (both in the
provider and in the whole application) to *fr_FR*:
```php
<?php

use KPhoen\Provider\FakerServiceProvider;

$app->register(new FakerServiceProvider('\Acme\Faker\Factory', $guessLocale = false), array(
    'locale' => 'fr_FR',
));
```

From your controllers:
```php
<?php

$app->get('/hello', function() use ($app) {
    return 'Hello ' . $app['faker']->name;
});
```

From [Twig](http://twig.sensiolabs.org/):
```html
<!DOCTYPE html>
<html>
    <body>
        <p>Hello {{ app.faker.name }}!</p>
    </body>
</html>
```


## Installation

Install the FakerServiceProvider adding `kphoen/faker-service-provider` to your composer.json or from CLI:

```
$ php composer.phar require 'kphoen/faker-service-provider:~1.0'
```


## Licence

This provider is released under the MIT license.
