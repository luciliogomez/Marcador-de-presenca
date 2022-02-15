<?php
require __DIR__."/../vendor/autoload.php";

use App\Utils\View;
use WilliamCosta\DotEnv\Environment;
use App\Http\Middlewares\Queue as MiddlewareQueue;
Environment::load(__DIR__);

define("HOJE",date("Y-m-d"));
define("URL",getenv("URL"));

View::init([
    'URL'=> URL
]
);

MiddlewareQueue::setMap([
    'maintenance' => Maintenance::class
]);

MiddlewareQueue::setDeault([
    'maintenance' 
]);