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


  function consultUser( $numDocumento )
  {
    $sql = "SELECT *
            FROM usuarios 
            WHERE numDocumento = '$numDocumento'  
            AND codEstado = '1' ";

    return $this -> bd -> Consultar( $sql, 1 );
  }


  function getSecretKey()
  {
    return "ef91f45375acf9a07ec23de8e9f2bb02cfdab79ec4e46d40d5cd88305c74da95";
  }


  function registerUser( $data )
  {
    //Validaciones previas (llegada de datos)
    if( !$data['nomUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el nombre."
      );
    }
    elseif( !$data['apeUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el apellido."
      );
    }
    elseif( !$data['numDocumento'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el documento."
      );
    }
    elseif( !$data['clave'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar la clave."
      );
    }
    elseif( !$data['emaUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el email."
      );
    }
    elseif( !$data['telUsuario'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el telefono."
      );
    }
    else
    {
      //Se consulta si existe el usuario 

      $usuario = $this -> consultUser( $data['numDocumento'] );

      if( $usuario )
      {
        return array(
          "status" => false,
          "message" => "Error, el usuario ya se encuentra registrado."
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
              nomUsuario, apeUsuario, numDocumento, clave, emaUsuario, 
              telUsuario, dirUsuario, fecCreacion, codEstado
            )
            VALUES
            (
              '$nomUsuario', '$apeUsuario', '$data[numDocumento]', '$data[clave]', '$data[emaUsuario]', 
              '$data[telUsuario]', '$data[dirUsuario]', NOW(), '1'
            )";

        $this -> bd -> Consultar( $insert );

        $codUsuario = $this -> bd -> getId();

        if( $codUsuario )
        {
          return array(
            "status" => true,
            "message" => "Usuario creado exitosamente."
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
  }


  function login( $data )
  {
    $sql = "SELECT *
            FROM usuarios 
            WHERE SHA2(emaUsuario, 512) = '$data[email]'
            AND clave = '$data[clave]'
            AND codEstado = '1' ";

    return $this -> bd -> Consultar( $sql, 1 );
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
}

?>