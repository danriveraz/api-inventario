<?php

  use Firebase\JWT\JWT;

  $app -> get( '/conexion', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $result = array(
        "status" => true,
        "message" => "Hay conexión"
      );
      
      $response -> write( json_encode( $result ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    $result = null;

    return $response;
  }); 


  $app -> post( '/registerUser', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $nomUsuario = $request -> getHeaderLine( 'nomUsuario' );
      $apeUsuario = $request -> getHeaderLine( 'apeUsuario' );
      $numDocumento = $request -> getHeaderLine( 'numDocumento' );
      $clave = $request -> getHeaderLine( 'clave' );
      $emaUsuario = $request -> getHeaderLine( 'emaUsuario' );
      $dirUsuario = $request -> getHeaderLine( 'dirUsuario' );
      $telUsuario = $request -> getHeaderLine( 'telUsuario' );

      $data = array(
        "nomUsuario" => $nomUsuario,
        "apeUsuario" => $apeUsuario,
        "numDocumento" => $numDocumento,
        "clave" => $clave,
        "emaUsuario" => $emaUsuario,
        "dirUsuario" => $dirUsuario,
        "telUsuario" => $telUsuario
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> registerUser( $data );

      if( $result )
      {
          $response = $response -> withStatus(200);
      }
      else 
      {
          $result = array( 
              "message" => "No hay datos", 
              "status" => false 
          );

          $response = $response -> withStatus(404);
      }
      
      $response -> write( json_encode( $result ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    $result = null;

    return $response;
  });


  $app->post("/token", function ($request, $response, $arguments) 
  {
    $now = new DateTime();
    $future = new DateTime("now +2 hours");

    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "iss" => "api-inventario",
    ];

    $secret = "ef91f45375acf9a07ec23de8e9f2bb02cfdab79ec4e46d40d5cd88305c74da95"; //MD5(APIDANINVMD5CRI)
    $token = JWT::encode($payload, $secret, "HS256");
    
    $data["status"] = "true";
    $data["token"] = $token;

    return $response->withStatus(201)->withHeader("Content-Type", "application/json")->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  });

?>