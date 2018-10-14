[![License](https://poser.pugx.org/alshf/footman/license)](https://packagist.org/packages/alshf/footman)
[![Total Downloads](https://poser.pugx.org/alshf/footman/downloads)](https://packagist.org/packages/alshf/footman)
[![Build Status](https://travis-ci.org/alshf89/footman.svg?branch=master)](https://travis-ci.org/alshf89/footman)
[![Latest Stable Version](https://poser.pugx.org/alshf/footman/version)](https://packagist.org/packages/alshf/footman)

# Footman Request Sender

Footman can Send any Request to any URL. At least PHP 7.0 is required.

## Documentation

 - [Installation](#installation)
    - [Basic Setup](#basic-setup)
    - [Laravel Additional Steps](#laravel-additional-steps)
 - [How to use](#how-to-use)
 	- [Example](#example)
    - [Laravel Example](#laravel-example)
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
Alshf\Laravel\Providers\FootmanServiceProvider::class,
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
$client = new Footman([
    'header' => [
        'User-Agent' => 'Footman CURL'
    ],
    'allow_redirects' => true,
]);

// Now you can make a request by passing a closure in it
$response = $client->request(function ($request) {
    $request->request_type = 'GET';
    $request->request_url = 'https://someWebsiteName.com/';
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

// Rewind Body
$response->seek(0);

// Read 10 Characters of body
$response->read(10);

// Get Request Status 200, 404, ...
$response->getStatusCode();

// Get Request Status Pharase ex: OK...
$response->getStatusPhrase();
```
___

#### Laravel Example

You can use Footman Facade in Laravel so Footman Service Provider will set all configuration for you.

```PHP
namespace App\Http\Controllers;

use Footman;

class SomeController extends Controller
{
    private $response;

    public function index()
    {
        $this->response = Footman::request(function ($request) {
            $request->request_url = 'https://someWebsiteName.com/';
        });

        $this->response->getHeaders();
    }
}
```

You can also inject Footman into the constructor.

```PHP
namespace App\Http\Controllers;

use Alshf\Footman;

class SomeController extends Controller
{
    private $response;

    private $footman;

    public function __construct(Footman $footman)
    {
        $this->footman = $footman;
    }

    public function index()
    {
        $this->response = $this->footman->request(function ($request) {
            $request->request_url = 'https://someWebsiteName.com/';
        });

        $this->response->getHeaders();
    }
}
```
Check out all Footman Laravel Configuration in *config/footman.php* File.

#### Error Handler

you can get all Error with *FootmanException* but if you want to get request errors like connection error, Server errors like 5xx or 4xx errors you can catch them with *FootmanRequestException*, you can also catch cookies errors with *FootmanCookiesException*.

```PHP
// Use Request Provider & Exceptions
use Alshf\Footman;
use Alshf\Exceptions\FootmanCookiesException;
use Alshf\Exceptions\FootmanRequestException;

try {
    $client = new Footman;

    $response = $client->request(function ($request) {
    	// POST, GET, PUT, PATCH, DELETE
    	$request->request_type = 'POST';

    	// Request URL
        $request->request_url = 'https://someWebsiteName.com/';

        // Authenticate
        $request->auth = ['username', 'password'];
        
        // Form Data For POST Request
        $request->form_params = [
	        'foo' => 'bar',
	        'baz' => ['hi', 'there!']
	    ];

	    $request->allow_redirects = false;
    });
} catch (FootmanRequestException $e) {
    // Catch All HTML & connection Errors, timeouts and etc...
    echo $e->getMessage();

    // If we have 4xx or 5xx error
    echo $e->getStatusPhrase();
    echo $e->getStatusCode();
} catch (FootmanCookiesException $e) {
    // Catch All Cookies Errors
    echo $e->getMessage();
}
```

Footman also provides _Response_ Exception :

```PHP
use Alshf\Exceptions\FootmanResponseException;

try {
    $response->getHeaders();
    $response->hasHeader('content/type');
    $response->getHeader('content/type');
    $response->getbody();
    $response->getContents();
    $response->read(10);
    $response->getStatusCode();
    $response->getStatusPhrase();
    $response->seek(0);
} catch (FootmanResponseException $e) {
    // Catch All Response Errors.
    echo $e->getMessage();
}
```

### Contributing

Bugs and feature request are tracked on [GitHub](https://github.com/alshf89/footman/issues).

### Credits

The code on which this package is principally developed and maintained by [Ali Shafiee](https://github.com/alshf89).

### License

Footman package is released under [MIT](LICENSE.txt).