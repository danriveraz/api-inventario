<?php

  $app -> get( '/usuarios', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $db = getConexion();

      $sql = "SELECT *
              FROM usuarios  ";

      $result = $db -> query( $sql );

      if( $result -> rowCount() > 0 )
      {
          $list = $result -> fetchAll( PDO::FETCH_ASSOC );

          $result = array( 
            "status" => true,
            "usuarios" => $list 
          );

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
    $db = null;

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