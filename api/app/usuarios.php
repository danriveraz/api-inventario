<?php

  use Firebase\JWT\JWT;
  $checkProxyHeaders = true;
  $trustedProxies = ['10.0.0.1', '10.0.0.2'];

  $app -> post( '/register', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $data = array(
        "nomUsuario" => $dataRequest['nomUsuario'],
        "apeUsuario" => $dataRequest['apeUsuario'],
        "emaUsuario" => $dataRequest['emaUsuario'],
        "clave" => $dataRequest['clave'],
        'nomEmpresa' => $dataRequest['nomEmpresa'],
        'nitEmpresa' => $dataRequest['nitEmpresa'],
        'emaEmpresa' => $dataRequest['emaEmpresa'],
        'activation' => $dataRequest['activation']
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> register( $data );

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


  $app->post("/login", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();
    
      $data = array(
        "email" => $dataRequest['email'],
        "clave" => $dataRequest['clave']
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $login = $core -> login( $data );
      
      if( $login )
      {
        if( isset( $login['status'] ) )
        {
          $result = $login;
        }
        else
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
              "codEmpresa" => $login['codEmpresa'],
              "perfil" => $login['codPerfil']
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

    return $response;
  });


  $app->post("/verificarLogin", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

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

    return $response;
  });


  $app->post("/getUserCompanies", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      define( md5( "api2021inv" ), true );
      include( "../model/core.php" );

      $core = new Core( new bd() );

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = $core -> getUserCompanies( $token );

      $response = $response -> withStatus(200);
      
      $response -> write( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    return $response;
  });


  $app->post("/changeCompany", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];
      $empresa = $dataRequest['empresa'];

      define( md5( "api2021inv" ), true );
      include( "../model/core.php" );

      $core = new Core( new bd() );

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = array(
        "token" => $token,
        "codEmpresa" => $empresa
      );

      $result = $core -> changeCompany( $data );

      $response = $response -> withStatus(200);
      
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


  $app -> post( '/editUser', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      $codUsuario = $dataRequest['codUsuario'];
      $nomUsuario = $dataRequest['nomUsuario'];
      $apeUsuario = $dataRequest['apeUsuario'];
      $telUsuario = $dataRequest['telUsuario'];
      $dirUsuario = $dataRequest['dirUsuario'];

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = array(
        "codUsuario" => $codUsuario,
        "nomUsuario" => $nomUsuario,
        "apeUsuario" => $apeUsuario,
        'telUsuario' => $telUsuario,
        "dirUsuario" => $dirUsuario,
        "token" => $token
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> editUser( $data );

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


  $app -> post( '/changePass', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      $pass = $dataRequest['pass'];
      $newPass = $dataRequest['newPass'];
      $confirmPass = $dataRequest['confirmPass'];

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = array(
        "pass" => $pass,
        "newPass" => $newPass,
        "confirmPass" => $confirmPass,
        "token" => $token
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> changePass( $data );

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


  $app -> post( '/deleteUser', function(  $request,  $response )
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $auth = $request -> getHeaderLine( 'Authorization' );
      $codUsuario = $request -> getHeaderLine( 'codUsuario' );

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = array(
        "codUsuario" => $codUsuario,
        "token" => $token
      );

      define( md5( "api2021inv" ), true );

      include( "../model/core.php" );

      $core = new Core( new bd() );

      $result = $core -> deleteUser( $data );

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


  $app->post("/getDataUser", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      define( md5( "api2021inv" ), true );
      include( "../model/core.php" );

      $core = new Core( new bd() );

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = $core -> getDataUser( $token );

      $response = $response -> withStatus(200);
      
      $response -> write( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    return $response;
  });


  $app->post("/getDataUserCompany", function ($request, $response) 
  {
    $response = $response -> withHeader('Content-Type', 'application/json');

    try  
    {
      $dataRequest = $request -> getParams();

      $auth = $dataRequest['Authorization'];

      define( md5( "api2021inv" ), true );
      include( "../model/core.php" );

      $core = new Core( new bd() );

      $token = str_replace("Bearer ", "", (string) $auth);

      $data = $core -> getDataUserCompany( $token );

      $response = $response -> withStatus(200);
      
      $response -> write( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ));
    }
    catch( PDOException $e )
    {
      $response = $response -> withStatus(400);
      $response -> write( json_encode( array( 'message' => $e -> getMessage(), "status" => false )));
    }

    return $response;
  });
?>