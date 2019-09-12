# Firebase Admin SDK for PHP 5.6

This repository is a fork of the original version 2.3.1 so that users of php version 5.6 or lower can work with google firebase.

## Thanks for all support of Jérôme Gamez. ##


[![Latest Stable Version](https://poser.pugx.org/nathanfeitoza/firebase-php5.6/v/stable)](https://packagist.org/packages/nathanfeitoza/firebase-php5.6)
[![Total Downloads](https://poser.pugx.org/nathanfeitoza/firebase-php5.6/downloads)](https://packagist.org/packages/nathanfeitoza/firebase-php5.6)
[![License](https://poser.pugx.org/nathanfeitoza/firebase-php5.6/license)](https://packagist.org/packages/nathanfeitoza/firebase-php5.6)
[![Build Status](https://travis-ci.org/nathanfeitoza/firebase-php5.6.svg?branch=master)](https://travis-ci.org/nathanfeitoza/firebase-php5.6)

This SDK makes it easy to interact with [Google Firebase](https://firebase.google.com>)
applications.
 

For support, please use the [issue tracker](https://github.com/kreait/firebase-php/issues/),
or join the Firebase Community Slack at https://firebase-community.appspot.com and join the #php channel.

- [Documentation](#documentation)
- [Usage example](#usage-example)
 
## Documentation

You can find the documentation at https://firebase-php.readthedocs.io/en/2.3.1

- [Requirements](http://firebase-php.readthedocs.io/en/latest/overview.html#requirements)
- [Installation](http://firebase-php.readthedocs.io/en/latest/overview.html#installation)

```
    composer require nathanfeitoza/firebase-php5.6
```

- [Authentication](http://firebase-php.readthedocs.io/en/latest/authentication.html)
- [Working with the Realtime Database](http://firebase-php.readthedocs.io/en/latest/realtime-database.html)

- [Roadmap](http://firebase-php.readthedocs.io/en/latest/overview.html#roadmap)

## Usage example

```php
$firebase = (new \Firebase\Factory())
    ->withCredentials(__DIR__.'/path/to/google-service-account.json')
    ->withDatabaseUri('https://my-project.firebaseio.com')
    ->create();

$database = $firebase->getDatabase();

$newPost = $database
    ->getReference('blog/posts')
    ->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);

$newPost->getKey(); // => -KVr5eu8gcTv7_AHb-3-
$newPost->getUri(); // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-

$newPost->getChild('title')->set('Changed post title');
$newPost->getValue(); // Fetches the data from the realtime database
$newPost->remove();
```
