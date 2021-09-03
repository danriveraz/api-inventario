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


  $app->post("/login", function ($request, $response, $arguments) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $email = $request -> getHeaderLine( 'email' );
      $clave = $request -> getHeaderLine( 'clave' );

      $data = array(
        "email" => $email,
        "clave" => $clave
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $login = $core -> login( $data );

      if( $login )
      {
        $response = $response -> withStatus(200);

        $now = new DateTime();
        $future = new DateTime("now +2 hours");
  
        $payload = [
          "iat" => $now->getTimeStamp(),
          "exp" => $future->getTimeStamp(),
          "userData" => array(
            "codUsuario" => $login['codUsuario'],
            "emaUsuario" => $login['emaUsuario'],
            "codEmpresa" => "",
            "perfil" => ""
          )
        ];
  
        $secret = $core -> getSecretKey();

        $token = JWT::encode($payload, $secret);
        
        $result = array(
          "status" => true,
          "message" => "Bienvenido " . $login['nomUsuario'] . " " . $login['apeUsuario'],
          "token" => $token
        );
      }
      else 
      {
          $result = array( 
              "message" => "No hay datos", 
              "status" => false 
          );

          $response = $response -> withStatus(404);
      }

      //$response->withStatus(201)->withHeader("Content-Type", "application/json")->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      
      $response -> write( json_encode( $result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    $result = null;

    return $response;

  });


  $app->post("/verificarLogin", function ($request, $response, $arguments) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $auth = $request -> getHeaderLine( 'Authorization' );

      define( md5( "api2021inv" ), true );
      include( "../model/core.php" );

      $core = new Core( new bd() );

      $token = str_replace("Bearer ", "", (string)$auth);

      $data = $core -> validarToken( $token );

      $response = $response -> withStatus(200);
      
      $response -> write( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    $result = null;

    return $response;

  });

?>