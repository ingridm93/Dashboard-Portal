<?php
require_once "./vendor/autoload.php";

session_start();

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

/** @var  $kernel */
$kernel = new Kernel();
/** @var Request $request */

$request = Request::createFromGlobals();

/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $kernel->handle($request);
$response->send();






