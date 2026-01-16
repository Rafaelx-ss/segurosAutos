<?php 
class UsuarioAcceso{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Usuarios'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
		
	
	function ObtenerDatos($usuario){
   		 $query = "select u.usuarioID,a.accionID,a.nombreAccion,f.formularioID,f.nombreFormulario, u.passw from Acciones a
                inner join AccionesFormularios af on a.accionID=af.accionID
                inner join Formularios f on af.formularioID=f.formularioID
                inner join PerfilAccionFormulario paf on af.accionFormularioID=paf.accionFormularioID 
                inner join PerfilesCompuestos pc on paf.perfilID=pc.perfilID 
                inner join Usuarios u on u.usuarioID=pc.usuarioID
                where u.usuario=:usuario and a.estadoAccion=1 and f.estadoFormulario=1 
                and paf.activoPerfilAccionFormulario=1 and u.activoUsuario=1
                and u.intentosValidos <= (SELECT maximoIntentosFallidos FROM ReglasPassw LIMIT 1)";
		
 
    	// return false if email does not exist in the database
    	 $stmt = $this->Conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
		// sanitize
		$usuario=htmlspecialchars(strip_tags($usuario));

		// bind given email value
		$stmt->bindParam(':usuario', $usuario);

		// execute the query
		$stmt->execute();

		// get number of rows
		$num = $stmt->rowCount();

		// if email exists, assign values to object properties for easy access and use for php sessions
		if($num>0){

			// get record details / values
			$this->Dataset = $stmt->fetch(PDO::FETCH_ASSOC);


			// return true because email exists in the database
			return true;
		}

		// return false if email does not exist in the database
		return false;
	}
 


		
}
	?>