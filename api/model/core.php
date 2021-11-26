<?php

if( !md5( "api2021inv" ) ) die( "No cuenta con los permisos requeridos." );

use Firebase\JWT\JWT;
class Core
{
  var $bd = null;

	function __construct( $bd )
	{
		$this -> bd = $bd;
	}


  function getSecretKey()
  {
    return "ef91f45375acf9a07ec23de8e9f2bb02cfdab79ec4e46d40d5cd88305c74da95";
  }


  function consultUser( $data ) // Consulta si un usuario existe
  {
    $sql = "SELECT *
            FROM usuarios 
            WHERE emaUsuario = '$data'  
            AND codEstado = '1' ";

    return $this -> bd -> Consultar( $sql, 1 );
  }


  function registerUser( $data ) // Registra un usuario nuevo
  {
    //Validaciones previas (llegada de datos)
    if( !$data['nomUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el nombre del usuario."
      );
    }
    elseif( !$data['apeUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el apellido del usuario."
      );
    }
    elseif( !$data['clave'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar la clave del usuario."
      );
    }
    elseif( !$data['emaUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el email del usuario."
      );
    }
    else
    {
      //Se registra el usuario

      // Colocamos la Primera Letra Mayuscula
      $data['nomUsuario'] = ucwords( strtolower( $data['nomUsuario'] ) );
      $data['apeUsuario'] = ucwords( strtolower( $data['apeUsuario'] ) );

      $nomUsuario = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $data['nomUsuario']
      );

      $apeUsuario = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $data['apeUsuario']
      );

      $data['clave'] = hash( "sha512", $data['clave'] );

      $insert = "INSERT INTO usuarios
          (
            nomUsuario, apeUsuario, clave, emaUsuario, fecCreacion, codEstado
          )
          VALUES
          (
            '$nomUsuario', '$apeUsuario', '$data[clave]', '$data[emaUsuario]', NOW(), '1'
          )";

      $this -> bd -> Consultar( $insert );

      $codUsuario = $this -> bd -> getId();

      if( $codUsuario )
      {
        return array(
          "status" => true,
          "message" => "Usuario creado exitosamente.",
          "codUsuario" => $codUsuario 
        );
      }
      else
      {
        return array(
          "status" => false,
          "message" => "Error al crear el usuario."
        );
      }
    }
  }


  function editUser( $data )
  {
    $dataUser = $this -> validarToken( $data['token'] );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      if( !$data['codUsuario'] )
      {
        return array(
          "status" => false,
          "message" => "Error, debe ingresar el usuario.",
          "token" => $data['token']
        );
      }
      else
      {
        $sql = "UPDATE usuarios
                SET nomUsuario = '$data[nomUsuario]',
                    apeUsuario = '$data[apeUsuario]',
                    telUsuario = '$data[telUsuario]',
                    dirUsuario = '$data[dirUsuario]',
                    fecEdicion = NOW()
                WHERE codUsuario = '$data[codUsuario]' ";

        $this -> bd -> Consultar( $sql );

        return array(
          "status" => true,
          "message" => "Proceso exitoso.",
          "token" => $data['token']
        );
      }
    }
  }


  function changePass( $data )
  {
    $dataUser = $this -> validarToken( $data['token'] );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      if( !$data['pass'] )
      {
        return array(
          "status" => false,
          "message" => "Error, debe ingresar su clave actual.",
          "token" => $data['token']
        );
      }
      elseif( $data['newPass'] == '' || $data['newPass'] != $data['confirmPass'] )
      {
        return array(
          "status" => false,
          "message" => "Error, la nueva clave debe ser igual a la confirmación de la misma.",
          "token" => $data['token']
        );
      }
      else
      {
        $codUsuario = $dataUser['data']-> userData -> codUsuario;

        $data['pass'] = hash( "sha512", $data['pass'] );

        $sql = "SELECT *
                FROM usuarios 
                WHERE clave = '$data[pass]'
                AND codUsuario = '$codUsuario'
                AND codEstado = '1' ";

        $usuario = $this -> bd -> Consultar( $sql, 1 );

        if( !$usuario )
        {
          return array(
            "status" => false,
            "message" => "Error, la clave ingresada no es correcta.",
            "token" => $data['token']
          );
        }
        else
        {
          $newPass = hash( "sha512", $data['newPass'] );

          $sql = "UPDATE usuarios
                  SET clave = '$newPass',
                      fecEdicion = NOW()
                  WHERE codUsuario = '$codUsuario' ";

          $this -> bd -> Consultar( $sql );

          return array(
            "status" => true,
            "message" => "Proceso exitoso.",
            "token" => $data['token']
          );
        }
      }
    }
  }


  function deleteUser( $data )
  {
    $dataUser = $this -> validarToken( $data['token'] );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      if( !$data['codUsuario'] )
      {
        return array(
          "status" => false,
          "message" => "Error, debe ingresar el usuario."
        );
      }
      else
      {
        $sql = "UPDATE usuarios
                SET codEstado = '0'
                WHERE codUsuario = '$data[codUsuario]' ";

        $this -> bd -> Consultar( $sql );

        return array(
          "status" => true,
          "message" => "Proceso exitoso.",
          "token" => $data['token']
        );
      }
    }
  }


  function consultCompany( $data ) // Consulta una empresa
  {
    $sql = "SELECT *
            FROM empresas 
            WHERE nitEmpresa = '$data'  
            AND codEstado = '1' ";

    return $this -> bd -> Consultar( $sql, 1 );
  }


  function registerCompany( $data ) // Registra una empresa
  {
    //Validaciones previas (llegada de datos)
    if( !$data['nomEmpresa'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el nombre de la empresa."
      );
    }
    elseif( !$data['nitEmpresa'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el nit de la empresa."
      );
    }
    elseif( !$data['emaEmpresa'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el email de la empresa."
      );
    }
    else
    {      
      //Se registra la empresa

      // Colocamos la Primera Letra Mayuscula
      $data['nomEmpresa'] = ucwords( strtolower( $data['nomEmpresa'] ) );

      $nomEmpresa = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $data['nomEmpresa']
      );

      $insert = "INSERT INTO empresas
          (
            nomEmpresa, nitEmpresa, emaEmpresa, fecRegistro, codEstado
          )
          VALUES
          (
            '$nomEmpresa', '$data[nitEmpresa]', '$data[emaEmpresa]', NOW(), '1'
          )";

      $this -> bd -> Consultar( $insert );

      $codEmpresa = $this -> bd -> getId();

      if( $codEmpresa )
      {
        return array(
          "status" => true,
          "message" => "Empresa creado exitosamente.",
          "codEmpresa" => $codEmpresa
        );
      }
      else
      {
        return array(
          "status" => false,
          "message" => "Error al crear la empresa."
        );
      }
    }
  }


  function register( $data ) // Se registra tanto el usuario como la empresa (Formulario registro login)
  {
    //Validaciones previas (llegada de datos)

    if( $data['activation'] != 347519 )
    {
      return array(
        "status" => false,
        "message" => "Error, el codigo de activación no es valido."
      );
    }
    else
    {
      if( !$data['emaUsuario'] )
      {
        return array(
          "status" => false,
          "message" => "Error, debe ingresar el email del usuario."
        );
      }
      elseif( !$data['nitEmpresa'] )
      {
        return array(
          "status" => false,
          "message" => "Error, debe ingresar el nit de la empresa."
        );
      }
      else
      {
        //Se consulta si existela empresa
        $empresa = $this -> consultCompany( $data['nitEmpresa'] );

        if( $empresa )
        {
          return array(
            "status" => false,
            "message" => "Error, la empresa ya se encuentra registrada."
          );
        }
        else
        {
          //Se consulta si existe el usuario 
          $usuario = $this -> consultUser( $data['emaUsuario'] );

          if( !$usuario )
          {
            $usuario = $this -> registerUser( $data );

            if( !$usuario['status'] )
            {
              return array(
                "status" => false,
                "message" => $usuario['message']
              );
            }
          }

          $empresa = $this -> registerCompany( $data );
          
          if( !$empresa['status'] )
          {
            return array(
              "status" => false,
              "message" => $empresa['message']
            );
          }
          else
          {
            //Asocio la empresa al usuario con perfil administrador

            $insert = "INSERT INTO empusuario
                (
                  codUsuario, codEmpresa, codPerfil, fecCreacion, codEstado
                )
                VALUES
                (
                  '$usuario[codUsuario]', '$empresa[codEmpresa]', '2', NOW(), '1'
                )";

            $registro = $this -> bd -> Consultar( $insert );

            if( $registro == 1 )
            {
              return array(
                "status" => true,
                "message" => "Proceso exitoso, por favor ingrese a su cuenta."
              );
            }
            else
            {
              return array(
                "status" => false,
                "message" => "El usuario ya se encontraba asociado a esta empresa"
              );
            }
          }
        }
      }
    }
  }


  function login( $data )
  {
    $sql = "SELECT a.*, b.codEmpresa, b.codPerfil
            FROM usuarios a
            LEFT JOIN empusuario b
            ON a.codUsuario = b.codUsuario
            WHERE a.emaUsuario = '$data[email]'
            AND a.clave = '".hash( "sha512", $data['clave'] )."' 
            AND a.codEstado = '1' ";

    $usuario = $this -> bd -> Consultar( $sql, 1 );
    
    if( !$usuario )
    {
      return array(
        "status" => false,
        "message" => "Error, el usuario o la clave son incorrectos."
      );
    }
    else
    {
      return $usuario;
    }
  }


  function validarToken( $token )
  {
    try
    {
      $dataObject = JWT::decode($token, $this -> getSecretKey(), array('HS256'));

      return array(
        "status" => true,
        "data" => $dataObject
      );
    }
    catch( Exception $e )
    {
      return array( 
        "status" => false,
        "message" => "Error, el token no es valido.", 
      );
    }
  }


  function getUserCompanies( $token )
  {
    $dataUser = $this -> validarToken( $token );
    
    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codUsuario = $dataUser['data']-> userData -> codUsuario;
     
      $sql = "SELECT a.*, b.nomEmpresa
              FROM empusuario a
              LEFT JOIN empresas b
              ON a.codEmpresa = b.codEmpresa
              WHERE a.codUsuario = '$codUsuario'  
              AND a.codEstado = '1' ";

      $empresas = $this -> bd -> Consultar( $sql, 2 );

      if( $empresas )
      {
        return array(
          "status" => true,
          "message" => "Proceso exitoso.",
          "empresas" => $empresas,
          "token" => $token
        );
      }
      else
      {
        return array(
          "status" => false,
          "message" => "Error, no se encontraron empresas asociadas al usuario."
        );
      }
    }
  }


  function changeCompany( $data )
  {
    $dataUser = $this -> validarToken( $data['token'] );
    
    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codUsuario = $dataUser['data']-> userData -> codUsuario;
     
      $sql = "SELECT *
              FROM empusuario
              WHERE codUsuario = '$codUsuario'
              AND codEmpresa = '$data[codEmpresa]'  
              AND codEstado = '1' ";

      $empresa = $this -> bd -> Consultar( $sql, 1 );

      if( !$empresa )
      {
        return array(
          "status" => false,
          "message" => "Error, la empresa no se encuentra asociada a este usuario"
        );
      }
      else
      {
        $dataUser['data'] -> userData -> codEmpresa = $data['codEmpresa'];

        $secret = $this -> getSecretKey();

        $token = JWT::encode($dataUser['data'], $secret);
      
        return array(
          "status" => true,
          "message" => "Proceso exitoso.",
          "token" => $token
        );
      }
    }
  }


  function getDataUserCompany( $token ) // Obtiene los datos de un usuario y su empresa asociada
  {
    $dataUser = $this -> validarToken( $token );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codUsuario = $dataUser['data']-> userData -> codUsuario;
      $codEmpresa = $dataUser['data']-> userData -> codEmpresa;

      $sql = "SELECT a.nomUsuario, a.apeUsuario, a.numDocumento, a.telUsuario, a.emaUsuario,
                    c.nomEmpresa, c.nitEmpresa, c.telEmpresa, c.emaEmpresa, d.nomPerfil
              FROM usuarios a
              LEFT JOIN empusuario b
              ON a.codUsuario = b.codUsuario
              LEFT JOIN empresas c
              ON b.codEmpresa = c.codEmpresa
              LEFT JOIN perfiles d
              ON b.codPerfil = d.codPerfil
              WHERE a.codUsuario = '$codUsuario'  
              AND b.codEmpresa = '$codEmpresa'
              AND a.codEstado = '1' ";

      $dataUser = $this -> bd -> Consultar( $sql, 1 );

      return array(
        "status" => true,
        "message" => "Proceso exitoso.",
        "data" => $dataUser,
        "token" => $token
      );
    }
  }


  function getDataUser( $token ) // Obtiene los datos de un usuario
  {
    $dataUser = $this -> validarToken( $token );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codUsuario = $dataUser['data']-> userData -> codUsuario;

      $sql = "SELECT a.nomUsuario, a.apeUsuario, a.numDocumento, a.telUsuario, a.emaUsuario
              FROM usuarios a
              WHERE a.codUsuario = '$codUsuario'  
              AND a.codEstado = '1' ";

      $dataUser = $this -> bd -> Consultar( $sql, 1 );

      return array(
        "status" => true,
        "message" => "Proceso exitoso.",
        "data" => $dataUser,
        "token" => $token
      );
    }
  }


  function editCompany( $data )
  {
    $dataUser = $this -> validarToken( $data['token'] );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codEmpresa = $dataUser['data']-> userData -> codEmpresa;

      $sql = "UPDATE empresas
              SET nomEmpresa = '$data[nomEmpresa]',
                  telEmpresa = '$data[telEmpresa]',
                  emaEmpresa = '$data[emaEmpresa]',
                  fecEdicion = NOW()
              WHERE codEmpresa = '$codEmpresa' ";

      $this -> bd -> Consultar( $sql );

      return array(
        "status" => true,
        "message" => "Proceso exitoso.",
        "token" => $data['token']
      );
    }
  }


  function getDataCompany( $token ) // Obtiene los datos de un usuario
  {
    $dataUser = $this -> validarToken( $token );

    if( !$dataUser['status'] )
    {
      return $dataUser;
    }
    else
    {
      $codEmpresa = $dataUser['data']-> userData -> codEmpresa;

      $sql = "SELECT a.nomEmpresa, a.nitEmpresa, a.telEmpresa, a.emaEmpresa
              FROM empresas a
              WHERE a.codEmpresa = '$codEmpresa'  
              AND a.codEstado = '1' ";

      $dataUser = $this -> bd -> Consultar( $sql, 1 );

      return array(
        "status" => true,
        "message" => "Proceso exitoso.",
        "data" => $dataUser,
        "token" => $token
      );
    }
  }
}

?>