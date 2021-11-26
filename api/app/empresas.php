<?php

  use Firebase\JWT\JWT;

  $app -> post( '/editCompany', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      $nomEmpresa = $dataRequest['nomEmpresa'];
      $telEmpresa = $dataRequest['telEmpresa'];
      $emaEmpresa = $dataRequest['emaEmpresa'];

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = array(
        "nomEmpresa" => $nomEmpresa,
        "telEmpresa" => $telEmpresa,
        'emaEmpresa' => $emaEmpresa,
        "token" => $token
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> editCompany( $data );

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


  $app -> post( '/getDataCompany', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      $token = str_replace("Bearer ", "", (string) $auth);

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> getDataCompany( $token );

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
?>