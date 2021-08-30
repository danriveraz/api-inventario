<?php 

//require( "../lib/fn.php" );

class CompanyKupi 
{
    var $conexion = NULL;

    function __construct( $conexion )
    {
        $this -> conexion = $conexion;
    } 


    function getCiudades( )
    {
        try  
        {   
            $sql = "SELECT c.codCiudad, CONCAT(c.nomCiudad, ' (', d.nomDepto, ')') AS nomCiudad
                    FROM tab_empresas a,
                        tab_empciudad b,
                        tab_ciudades c,
                        tab_deptos d
                    WHERE a.codVisible = '1' AND
                        a.codEmpresa = b.codEmpresa AND
                        b.codCiudad = c.codCiudad AND
                        c.codDepto = d.codDepto AND
                        c.codCiudad != 0
                    GROUP BY c.codCiudad
                    ORDER BY nomCiudad ASC ";

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            //if( $query -> rowCount() == 1 )
            if( $query -> rowCount() > 0 )
            {
                $ciudades = $query -> fetchAll( PDO::FETCH_ASSOC );
                //$ciudades = $query -> fetch( PDO::FETCH_ASSOC ); // Trae 1 sóla ciudad

                return $ciudades;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getDepartamentos( )
    {
        try  
        {   
            $sql = "SELECT d.nomDepto, d.codDepto
                    FROM tab_empresas a,
                        tab_empciudad b,
                        tab_ciudades c,
                        tab_deptos d
                    WHERE a.codVisible = '1' AND
                        a.codEmpresa = b.codEmpresa AND
                        b.codCiudad = c.codCiudad AND
                        c.codDepto = d.codDepto AND
                        c.codCiudad != 0
                    GROUP BY d.codDepto
                    ORDER BY d.nomDepto ASC ";

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            if( $query -> rowCount() > 0 )
            {
                $ciudades = $query -> fetchAll( PDO::FETCH_ASSOC );

                return $ciudades;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getCompaniesOnline( )
    {
        try  
        {   
            $sql = "SELECT a.nomEmpresa, a.telPrincipal AS telEmpresa, a.dirEmpresa, a.urlImagen, a.codEmpresa,
                            b.idCategoria
            FROM    tab_empresas a,
                    tab_catempresa b
            WHERE   a.codVisible = '1' AND
                    a.codEstado = '1' AND
                    a.codEmpresa = b.codEmpresa AND 
                    b.idCategoria = '11'  ";

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            if( $query -> rowCount() > 0 )
            {
                $comercios = $query -> fetchAll( PDO::FETCH_ASSOC );

                return $comercios;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getCatCiudad( $codCiudad )
    {
        try  
        {   
            $sql = "SELECT c.idCategoria AS id, c.nomCategoria AS categoria, c.idCategoria, c.nomCategoria
                    FROM tab_empresas a,
                        tab_catempresa b,
                        tab_categorias c,
                        tab_empciudad e
                    WHERE e.codCiudad = '$codCiudad' AND
                        a.codEmpresa = b.codEmpresa AND
                        a.codEmpresa = e.codEmpresa AND
                        b.idCategoria = c.idCategoria AND
                        a.codEstado = '1' AND
                        a.codVisible = '1'

                    GROUP BY b.idCategoria

                    UNION

                    SELECT c.idCategoria AS id, c.nomCategoria AS categoria, c.idCategoria, c.nomCategoria
                    FROM tab_empresas a
                        LEFT JOIN tab_empciudad e
                        ON a.codEmpresa = e.codEmpresa,
                        tab_catempresa b,
                        tab_categorias c
                    WHERE a.codEmpresa = b.codEmpresa AND
                        b.idCategoria = c.idCategoria AND
                        a.codEstado = '1' AND
                        e.codCiudad IS NULL

                    GROUP BY b.idCategoria

                    ORDER BY 2 ASC";

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            //if( $query -> rowCount() == 1 )
            if( $query -> rowCount() > 0 )
            {
                $categorias = $query -> fetchAll( PDO::FETCH_ASSOC );
                //$categorias = $query -> fetch( PDO::FETCH_ASSOC ); // Trae 1 sóla ciudad

                return $categorias;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getCiudadesDepto( $codDepto )
    {
        try  
        {   
            $sql = "SELECT codCiudad, nomCiudad
                    FROM tab_ciudades
                    WHERE codDepto = '$codDepto'
                    ORDER BY nomCiudad ASC ";

            $query = $this -> conexion -> prepare( $sql );

            $query -> execute();
            
            //if( $query -> rowCount() == 1 )
            if( $query -> rowCount() > 0 )
            {
                $ciudades = $query -> fetchAll( PDO::FETCH_ASSOC );
                //$ciudades = $query -> fetch( PDO::FETCH_ASSOC ); // Trae 1 sóla ciudad

                return $ciudades;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getCompaniesCity( $codCiudad )
    {
        try  
        {   
            $sql = "SELECT a.nomEmpresa, a.telPrincipal AS telEmpresa, a.dirEmpresa, a.urlImagen, a.codEmpresa
            FROM tab_empresas a,
                    tab_catempresa b,
                    tab_empciudad c
            WHERE c.codCiudad = '$codCiudad' AND
                    a.codVisible = '1' AND
                    a.codEstado = '1' AND
                    a.codEmpresa = b.codEmpresa AND 
                    a.codEmpresa = c.codEmpresa 
                    GROUP BY a.codEmpresa ";

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            //if( $query -> rowCount() == 1 )
            if( $query -> rowCount() > 0 )
            {
                $categorias = $query -> fetchAll( PDO::FETCH_ASSOC );
                //$categorias = $query -> fetch( PDO::FETCH_ASSOC ); // Trae 1 sóla ciudad

                return $categorias;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }


    function getCompaniesCityCategory( $city, $category )
    {
        try  
        {   
            $sql = "SELECT a.nomEmpresa, a.telPrincipal AS telEmpresa, a.dirEmpresa, a.urlImagen, a.codEmpresa,
                            b.idCategoria
            FROM    tab_empresas a,
                    tab_catempresa b,
                    tab_empciudad c
            WHERE   a.codVisible = '1' AND
                    a.codEstado = '1' AND
                    a.codEmpresa = b.codEmpresa AND 
                    a.codEmpresa = c.codEmpresa AND
                    b.idCategoria = '$category' ";

            if( $category != '11' )
            {
                $sql .= " AND c.codCiudad = '$city' ";
            }

            $query = $this -> conexion -> prepare( $sql );
            $query -> execute();
            
            if( $query -> rowCount() > 0 )
            {
                $categorias = $query -> fetchAll( PDO::FETCH_ASSOC );

                return $categorias;
            }
            else 
            {
                return false;
            }
        }
        catch( PDOException $e )
        {
            return $e -> getMessage();
        }
    }
}