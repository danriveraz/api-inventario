<?php
class bd
{
	var $link = null;

	function __construct()
	{
		try
		{
			$this -> link = new PDO( 'mysql:host='.HOST.';dbname='.BASE, USER, PASS, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ) );
			$this -> link -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch( PDOException $e )
		{
			echo "ERROR: " . $e->getMessage();
		}
	}

	function Consultar( $sql, $modo = 0 )
	{
		try
		{
			$consulta = $this -> link -> prepare( $sql );
			$result = $consulta -> execute();

			switch( $modo )
			{
				case 1:
					return $consulta -> fetch( PDO::FETCH_ASSOC );
				break;

				case 2:
					return $consulta -> fetchAll( PDO::FETCH_ASSOC );
				break;

				default:
					return $result;
				break;
			}
		}
		catch( Exception $e )
	    {
	        return $e -> getMessage();
	    }
	} 

	function start()
	{
		$this -> link -> beginTransaction();
	}

	function commit()
	{
		$this -> link -> commit();
	}

	function rollback()
	{
		$this -> link -> rollback();
	}

	function getId()
	{
		return $this -> link -> lastInsertId();
	}
}