<?php 
class ParidadSerial{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ParidadSerial'; 
 
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
	
		
	
	function ObtenerDatos($id=0){
   		try{
			/*
			$consulta="";
			if($paridadID>0){
				$consulta = " where paridadID = ".$paridadID;
			}
			// query to check if email exists
			$query = "select *  from " . $this->NombreTabla .  "  ".$consulta."  order by paridadID ASC"; 
			// prepare the query
			*/
			$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where paridadID = ?" : "");
			$stmt = $this->Conexion->prepare( $query );


			$id = htmlspecialchars(strip_tags($id));

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
			}else{
				$this->Mensaje2 = "No encontramos registros con el id indicado";
			}

			// return false if email does not exist in the database
			return false;
		}catch (Exception $e){
			$this->Mensaje2 = $e->getMessage().'<br /> <br />Consulta: <br />'.$query;
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
	}
 

	function Inserta($registros){

    	$consulta="";
		
			$consulta = 'INSERT INTO '.$this->NombreTabla. 
			" (paridadID,
			  nombre,
			  valor,
			  descripcion,
			  versionRegistro,
			  regEstado,
			  regFechaUltimaModificacion,
			  regUsuarioUltimaModificacion,
			  regFormularioUltimaModificacion,
			  regVersionUltimaModificacion
			  ) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			if($this->ObtenerDatos($item['paridadID'])){
				$this->Cambia($item["paridadID"], $item["nombre"], $item["valor"], $item['descripcion'], $item["versionRegistro"], $item["regEstado"], utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
			}else{
				 if($coma){
					 $comaText=",";
				 }else{
					 $comaText="";
					 $coma = true;
				 }
					
				$consulta= $consulta . $comaText . "(
				'".($item["paridadID"])."', 
				'".($item["nombre"])."', 
				'".($item["valor"])."', 
				'".($item["descripcion"])."', 
				" . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			 
		   
		 }
	}
		
	if(!$coma){
		$consulta="select 1";
	}
	
	//$this->Dataset = $consulta;
	//return true;
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
			$this->Dataset = "Datos almacenados con exito";
            return true;

        }
    }   
    catch (Exception $e){
        echo $this->Mensaje2 = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}
	
function InsertaRegreso($registros){

    	$consulta="";
		
			$consulta = 'INSERT INTO '.$this->NombreTabla. 
			" (paridadID,
			  nombre,
			  valor,
			  descripcion,
			  versionRegistro,
			  regEstado,
			  regFechaUltimaModificacion,
			  regUsuarioUltimaModificacion,
			  regFormularioUltimaModificacion,
			  regVersionUltimaModificacion
			  ) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			if($this->ObtenerDatos($item['paridadID'])){
				$this->Cambia($item["paridadID"], $item["nombre"], $item["valor"], $item['descripcion'], $item["versionRegistro"], $item["regEstado"], utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
			}else{
				 if($coma){
					 $comaText=",";
				 }else{
					 $comaText="";
					 $coma = true;
				 }
					
				$consulta= $consulta . $comaText . "(
				'".($item["paridadID"])."', 
				'".($item["nombre"])."', 
				'".($item["valor"])."', 
				'".($item["descripcion"])."', 
				" . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			 
		   
		 }
	}
		
	if(!$coma){
		$consulta="select 1";
	}
	
	//$this->Dataset = $consulta;
	//return true;
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
			$this->Dataset = "Datos almacenados con exito";
            return true;

        }
    }   
    catch (Exception $e){
        echo $this->Mensaje2 = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}
	
	
function Cambia($paridadID, $nombre, $valor, $descripcion, $versionRegistro, $regEstado, $regFechaUltimaModificacion, $regUsuarioUltimaModificacion, $regFormularioUltimaModificacion, $regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombre=:nombre, descripcion=:descripcion, valor=:valor, versionRegistro=:versionRegistro, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion WHERE paridadID=:paridadID ";

	
	 $queryShow = "UPDATE " . $this->NombreTabla . " SET nombre='".$nombre."', descripcion='".$descripcion."', valor='".$valor."', versionRegistro='".$versionRegistro."', regEstado='".$regEstado."', regFechaUltimaModificacion='".$regFechaUltimaModificacion."', regUsuarioUltimaModificacion='".$regUsuarioUltimaModificacion."', regFormularioUltimaModificacion='".$regFormularioUltimaModificacion."', regVersionUltimaModificacion='".$regVersionUltimaModificacion."' WHERE paridadID='".$paridadID."' ";
    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$nombre=htmlspecialchars(strip_tags($nombre));
	$descripcion=htmlspecialchars(strip_tags($descripcion));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':paridadID', $paridadID);
	$stmt->bindParam(':nombre', $nombre);
	$stmt->bindParam(':descripcion', $descripcion);
	$stmt->bindParam(':valor', $valor);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	//$stmt->bindParam(':regEstado', $regEstado);

    try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
			$this->Dataset = "Datos actualizados con exito";
            return true;

        }
    }   
    catch (Exception $e){
        $this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex2", "Registo : ".$queryShow, $e->getMessage());
	echo $this->mensaje = $e->getMessage().$queryShow;
        //echo $this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}

	
function LogMigracion($Api, $metodo, $consulta, $mensaje){
	try{
		$query2 ="INSERT INTO LogMigracion (logMigracionId, api, metodo, consulta, mensaje, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) 
		VALUES (NULL, '".$Api."', '".$metodo."', '".str_replace("'","",$consulta)."', '".str_replace("'","",$mensaje)."', b'1', now(), '1', '1', '1');";
			
		// prepare the query
		$stmt = $this->Conexion->prepare($query2);
		if($stmt->execute()){
		}
	}
	catch (Exception $e){
		$this->mensaje .= $e->getMessage();
	}
}

	
}
	?>