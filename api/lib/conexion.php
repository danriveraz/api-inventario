<?php

define( "HOST", "localhost" );
define( "BASE", "database_inventario" );
define( "USER", "root" );
define( "PASS", "" );

function getConexion()
{
  try
  {
    $conexion = new PDO( 'mysql:host='.HOST.';dbname='.BASE, USER, PASS, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ) );
    $conexion -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    return $conexion;
  }
  catch( PDOException $e )
  {
    $error = json_encode( 
      array( 
      "message" => $e -> getMessage(), 
      "status" => false 
    ));

    return $error;
  }
}

function getVal( $tabla, $campo, $llave, $valor )
{
  try
  {
    $db = getConexion();

    $sql = "SELECT $campo
            FROM $tabla 
            WHERE $llave = '$valor' ";

    $result = $db -> query( $sql );

    $campo = $result -> fetch( PDO::FETCH_NUM );

    return $campo[0];
      
  }
  catch( PDOException $e )
  {
    return $e -> getMessage();
  }
}


function sendMail( $subject, $message )
{			
    $mail = new PHPMailer;
    $mail -> setFrom( "noreply@acspagos.co", "ACS APP" );//n172hU*pOXsU
    //$mail -> addAddress( "heishin007@gmail.com", "Joan" );
    $mail -> Subject = $subject;
    $mail -> Body = $message;
    //$mail -> IsHTML( true );
    $mail -> send();
}