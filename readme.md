[![License](https://poser.pugx.org/alshf/footman/license)](https://packagist.org/packages/alshf/footman)
[![Total Downloads](https://poser.pugx.org/alshf/footman/downloads)](https://packagist.org/packages/alshf/footman)
[![Latest Stable Version](https://poser.pugx.org/alshf/footman/version)](https://packagist.org/packages/alshf/footman)

# Footman Request Sender

Footman can Send any Request to any URL. At least PHP 7.0 is required.

## Documentation

 - [Installation](#installation)
    - [Basic Setup](#basic-setup)
    - [Laravel Additional Steps](#laravel-additional-steps)
 - [How to use](#how-to-use)
 	- [Example](#example)
 	- [Error Handler](#error-handler)
 - [Contributing](#contributing)
 - [Credits](#credits)
 - [License](#license)

### Installation
Footman uses [Composer](http://getcomposer.org/doc/00-intro.md#installation-nix) to make things easy.

Composer is a dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them into your project.
#### Basic Setup

Learn to use composer and run this Command Line:

    composer require alshf/footman

#### Laravel Additional Steps
If you're using Laravel and would like to use *Footman* with it, then follow the below instructions. Otherwise, you can skip this part.

Open _config/app.php_ and, to your providers array at the bottom, add:

```PHP
Alshf\Laravel\Providers\FootmanServiceProviders::class,
```

Optionally add an alias to make it easier to use the library. Open config/app.php and, to your _aliases_ array at the bottom, add

```PHP
'Footman' => Alshf\Laravel\Facades\Footman::class,
```

Now open your _terminal_ window and fire the following command to publish config file to your config directory

    php artisan vendor:publish --tag=footman

### How to use

Make sure you have Composer's autoload file included

```PHP
require 'vendor/autoload.php';
```

#### Example

```PHP
// Use Request Provider
use Alshf\Footman;

// Create New Instance of it
$client = new Footman;

// Now you can make a request by passing a closure in it
$response = $client->request(function ($request) {
    $request->header = [
        'User-Agent' => 'Footman User-Agent'
    ];

    $request->allow_redirects = [
        'max'             => 10,
        'strict'          => false,
        'referer'         => false,
        'protocols'       => ['http', 'https'],
        'track_redirects' => false
    ];

    $request->request_url = 'https://google.com/';
});

// Get All Headers as Laravel Collection
$response->getHeaders();

// Get Specific Header 
$response->getHeader('content/type');

// Check specific Header key
$response->hasHeader('content/type');

// Get Response body, You can cast it to String
$response->getbody();

// Get Response Raw Body
$response->getContents();

// Read 10 Characters of body
$response->read(10);

// Get Request Status 200, 404, ...
$response->getStatus();

// Get Request Status Pharase ex: OK...
$response->getStatusPhrase();
```
___

#### Error Handler

you can get all Error with *FootmanException* Exception.

```PHP
// Use Request Provider & Exceptions
use Alshf\Footman;
use Alshf\Exceptions\FootmanException;

try {
    $client = new Footman;

    $response = $client->request(function ($request) {
    	// POST, GET, PUT, PATCH, DELETE
    	$request->request_type = 'POST';

    	// Request URL
        $request->request_url = 'https://google.com/';

        // Authenticate
        $request->auth = ['username', 'password'];
        
        // Form Data For POST Request
        $request->form_params = [
	        'foo' => 'bar',
	        'baz' => ['hi', 'there!']
	    ];

	    $request->allow_redirects = false;
    });
} catch (FootmanException $e) {
    echo $e->getMessage();
}
```

### Contributing

Bugs and feature request are tracked on [GitHub](https://github.com/alshf89/footman/issues).

### Credits

The code on which this package is principally developed and maintained by [Ali Shafiee](https://github.com/alshf89).

### License

The HunterDog package is released under [BSD-3-Clause](LICENSE.txt).