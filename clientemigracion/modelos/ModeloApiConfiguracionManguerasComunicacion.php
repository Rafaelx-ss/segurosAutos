<?php 
class ConfiguracionManguerasComunicacion{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionManguerasComunicacion'; 
 
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
	
	function ObtenerDatos($mangueraNumero=0, $establecimientoID=0, $bombaNumero=0){
		try{
			$query = "select * from " . $this->NombreTabla .  " where 1 " . ($mangueraNumero > 0 ? " and mangueraNumero = :mangueraNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "") . " " . ($bombaNumero > 0 ? " and bombaNumero = :bombaNumero" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		if($mangueraNumero > 0){
			$mangueraNumero=htmlspecialchars(strip_tags($mangueraNumero));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
		if($bombaNumero > 0){
			$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($mangueraNumero > 0){
			$stmt->bindParam(':mangueraNumero', $mangueraNumero);
		}
		if($establecimientoID > 0){
			$stmt->bindParam(':establecimientoID', $establecimientoID);
		}
		if($bombaNumero > 0){
			$stmt->bindParam(':bombaNumero', $bombaNumero);
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
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    	// query to check if email exists
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (mangueraNumero,mangueraPosicionInterfaz,mangueraPosicionFisicaDispensario,mangueraIndexPrecio,mangueraIndexElectronica,establecimientoID,bombaNumero,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,mangueraIndexGrado) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['mangueraNumero'], $item['establecimientoID'], $item['bombaNumero'])){
				$this->Cambia($item['mangueraNumero'],($item['mangueraPosicionInterfaz']),($item['mangueraPosicionFisicaDispensario']),($item['mangueraIndexPrecio']),($item['mangueraIndexElectronica']),($item['establecimientoID']),$item['bombaNumero'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['mangueraIndexGrado']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["mangueraNumero"] . ",'" . ($item["mangueraPosicionInterfaz"]) . "','" . ($item["mangueraPosicionFisicaDispensario"]) . "','" . ($item["mangueraIndexPrecio"]) . "','" . ($item["mangueraIndexElectronica"]) . "','" . ($item["establecimientoID"]) . "'," . $item["bombaNumero"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["mangueraIndexGrado"] . ")";
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
	$mangueraNumero=htmlspecialchars(strip_tags($item['mangueraNumero']));
	$mangueraPosicionInterfaz=htmlspecialchars(strip_tags($item['mangueraPosicionInterfaz']));
	$mangueraPosicionFisicaDispensario=htmlspecialchars(strip_tags($item['mangueraPosicionFisicaDispensario']));
	$mangueraIndexPrecio=htmlspecialchars(strip_tags($item['mangueraIndexPrecio']));
	$mangueraIndexElectronica=htmlspecialchars(strip_tags($item['mangueraIndexElectronica']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$mangueraIndexGrado=htmlspecialchars(strip_tags($item['mangueraIndexGrado']));
   
    // bind the values
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':mangueraPosicionInterfaz', $mangueraPosicionInterfaz);
	$stmt->bindParam(':mangueraPosicionFisicaDispensario', $mangueraPosicionFisicaDispensario);
	$stmt->bindParam(':mangueraIndexPrecio', $mangueraIndexPrecio);
	$stmt->bindParam(':mangueraIndexElectronica', $mangueraIndexElectronica);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':mangueraIndexGrado', $mangueraIndexGrado);
   
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
	} catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    
}


function Cambia($mangueraNumero,$mangueraPosicionInterfaz,$mangueraPosicionFisicaDispensario,$mangueraIndexPrecio,$mangueraIndexElectronica,$establecimientoID,$bombaNumero,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$mangueraIndexGrado){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET mangueraPosicionInterfaz=:mangueraPosicionInterfaz,mangueraPosicionFisicaDispensario=:mangueraPosicionFisicaDispensario,mangueraIndexPrecio=:mangueraIndexPrecio,mangueraIndexElectronica=:mangueraIndexElectronica,establecimientoID=:establecimientoID,bombaNumero=:bombaNumero,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,mangueraIndexGrado=:mangueraIndexGrado 
		WHERE mangueraNumero=:mangueraNumero and establecimientoID=:establecimientoID and bombaNumero=:bombaNumero ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$mangueraNumero=htmlspecialchars(strip_tags($mangueraNumero));
	$mangueraPosicionInterfaz=htmlspecialchars(strip_tags($mangueraPosicionInterfaz));
	$mangueraPosicionFisicaDispensario=htmlspecialchars(strip_tags($mangueraPosicionFisicaDispensario));
	$mangueraIndexPrecio=htmlspecialchars(strip_tags($mangueraIndexPrecio));
	$mangueraIndexElectronica=htmlspecialchars(strip_tags($mangueraIndexElectronica));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$mangueraIndexGrado=htmlspecialchars(strip_tags($mangueraIndexGrado));
   
    // bind the values
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':mangueraPosicionInterfaz', $mangueraPosicionInterfaz);
	$stmt->bindParam(':mangueraPosicionFisicaDispensario', $mangueraPosicionFisicaDispensario);
	$stmt->bindParam(':mangueraIndexPrecio', $mangueraIndexPrecio);
	$stmt->bindParam(':mangueraIndexElectronica', $mangueraIndexElectronica);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':mangueraIndexGrado', $mangueraIndexGrado);

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
	}
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
}
	
		
}
	?>