<?php
session_start();
date_default_timezone_set( 'America/Bogota' );

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../lib/config.php';
require '../lib/bd.php';
require '../lib/class.phpmailer.php';
require '../vendor/autoload.php';



foreach( glob( "../lib/class/*.php" ) as $filename ) require $filename;

$app = new \Slim\App([
  'settings' => [
    "displayErrorDetails" => true,
    "addContentLengthHeader" => true
  ]
]);


$app -> get("/", function($request, $response, $args) 
{
  $response->getBody()->write("El Api está corriendo", 200);
});

foreach( glob( "../app/*.php" ) as $filename ) require $filename;

$app->run();