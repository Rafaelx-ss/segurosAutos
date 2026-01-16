<?php 
class ContactosProveedores{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ContactosProveedores'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
public $Mensaje2;
// constructor 
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0,$establecimientoID=0){
    	// query to check if email exists
    	try{
		$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and contactoProveedorID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	
	 	$id=htmlspecialchars(strip_tags($id));
 
    	// bind given id value

    
    	
    	if($id > 0){
      		$stmt->bindParam(1, $id);
    	}
    	// execute the query
    	$stmt->execute();
 
    	// get number of rows
    	$num = $stmt->rowCount();
 
    	// if email exists, assign values to object properties for easy access and use for php sessions
    	if($num>0){
 
        	// get record details / values
        	$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
 
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
    	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
		}
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (contactoProveedorID,nombreContacto,email,telefono,celular,tipoContactoID,proveedorID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['contactoProveedorID'])){
				$this->Cambia($item['contactoProveedorID'],($item['nombreContacto']),($item['email']),($item['telefono']),($item['celular']),$item['tipoContactoID'],$item['proveedorID'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
			}
			else{
				 if($coma)
				 {
					 $comaText=",";
				 }
				 else
				 {
					 $comaText="";
					 $coma = true;
				 }
				
				$consulta= $consulta . $comaText . "(" . $item["contactoProveedorID"] . ",'" . ($item["nombreContacto"]) . "','" . ($item["email"]) . "','" . ($item["telefono"]) . "','" . ($item["celular"]) . "'," . $item["tipoContactoID"] . "," . $item["proveedorID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	$this->Mensaje2=$this->query;
    // sanitize
	$contactoProveedorID=htmlspecialchars(strip_tags($item['contactoProveedorID']));
	$nombreContacto=htmlspecialchars(strip_tags($item['nombreContacto']));
	$email=htmlspecialchars(strip_tags($item['email']));
	$telefono=htmlspecialchars(strip_tags($item['telefono']));
	$celular=htmlspecialchars(strip_tags($item['celular']));
	$tipoContactoID=htmlspecialchars(strip_tags($item['tipoContactoID']));
	$proveedorID=htmlspecialchars(strip_tags($item['proveedorID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':contactoProveedorID', $contactoProveedorID);
	$stmt->bindParam(':nombreContacto', $nombreContacto);
	$stmt->bindParam(':email', $email);
	$stmt->bindParam(':telefono', $telefono);
	$stmt->bindParam(':celular', $celular);
	$stmt->bindParam(':tipoContactoID', $tipoContactoID);
	$stmt->bindParam(':proveedorID', $proveedorID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    
}


function Cambia($contactoProveedorID,$nombreContacto,$email,$telefono,$celular,$tipoContactoID,$proveedorID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET nombreContacto=:nombreContacto,email=:email,telefono=:telefono,celular=:celular,tipoContactoID=:tipoContactoID,proveedorID=:proveedorID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE contactoProveedorID=:contactoProveedorID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
    $this->Mensaje2=$query;
     // sanitize
	$contactoProveedorID=htmlspecialchars(strip_tags($contactoProveedorID));
	$nombreContacto=htmlspecialchars(strip_tags($nombreContacto));
	$email=htmlspecialchars(strip_tags($email));
	$telefono=htmlspecialchars(strip_tags($telefono));
	$celular=htmlspecialchars(strip_tags($celular));
	$tipoContactoID=htmlspecialchars(strip_tags($tipoContactoID));
	$proveedorID=htmlspecialchars(strip_tags($proveedorID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':contactoProveedorID', $contactoProveedorID);
	$stmt->bindParam(':nombreContacto', $nombreContacto);
	$stmt->bindParam(':email', $email);
	$stmt->bindParam(':telefono', $telefono);
	$stmt->bindParam(':celular', $celular);
	$stmt->bindParam(':tipoContactoID', $tipoContactoID);
	$stmt->bindParam(':proveedorID', $proveedorID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

    try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    
}
	
		
}
?>