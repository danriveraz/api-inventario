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


  function consultUser( $data )
  {
    $sql = "SELECT *
            FROM usuarios 
            WHERE emaUsuario = '$data'  
            AND codEstado = '1' ";

    return $this -> bd -> Consultar( $sql, 1 );
  }


  function consultCompany( $data )
  {
    $sql = "SELECT *
            FROM empresas 
            WHERE nitEmpresa = '$data'  
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


  function registerCompany( $data )
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
    elseif( !$data['telEmpresa'] )
    {
      return array(
        "status" => false,
        "message" => "Error, debe ingresar el telefono de la empresa."
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
            nomEmpresa, nitEmpresa, telEmpresa, emaEmpresa, fecRegistro, codEstado
          )
          VALUES
          (
            '$nomEmpresa', '$data[nitEmpresa]', '$data[telEmpresa]', '$data[emaEmpresa]', NOW(), '1'
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


  function register( $data )
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
            WHERE SHA2(a.emaUsuario, 512) = '$data[email]'
            AND a.clave = '$data[clave]'
            AND a.codEstado = '1' ";

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