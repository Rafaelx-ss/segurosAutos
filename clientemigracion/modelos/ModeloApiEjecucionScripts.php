<?php 
class EjecucionScripts{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'EjecucionScripts'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($aplicacionID=0, $usuarioApiID="0", $fechaInicio=""){
   		// $id = intval($id);
		
		$consultaApp="";
		$consultaUser="";
		if($aplicacionID>0){
			$consultaApp=" and s.aplicacionID=" . $aplicacionID;
		}
		if($usuarioApiID<>"0"){
			$consultaUser=" and es.usuarioApiID=(select usuarioApiID from UsuariosAPI where usuario='" . $usuarioApiID . "' limit 1)";
		}
		
    	// query to check if email exists
		if($fechaInicio==""){
			/*$query = "
			SELECT s.scriptID, s.aplicacionID,s.version,s.descripcion,s.fechaInicio,s.fechaFin,s.versionRegistro,s.regEstado,s.regFechaUltimaModificacion,s.regUsuarioUltimaModificacion,s.regFormularioUltimaModificacion,s.regVersionUltimaModificacion,
			d.detalleScriptID,d.orden,d.texto,d.versionRegistro as version,d.regEstado as regEstado,d.regFechaUltimaModificacion as regFecha,d.regUsuarioUltimaModificacion as regUser,d.regFormularioUltimaModificacion as regform,d.regVersionUltimaModificacion as regVersion
			FROM Scripts s inner join DetalleScripts d on d.scriptID=s.scriptID
			where s.scriptID not in (select scriptID from EjecucionScripts ds where ds.scriptID=s.scriptID " . $consultaUser . ") 
			" . $consultaApp . " order by d.orden";*/
			
			$query= "SELECT distinct s.scriptID, s.aplicacionID,s.version,s.descripcion,s.fechaInicio,s.fechaFin,s.versionRegistro,s.regEstado,s.regFechaUltimaModificacion,s.regUsuarioUltimaModificacion,s.regFormularioUltimaModificacion,s.regVersionUltimaModificacion,
			d.detalleScriptID,d.orden,d.texto,d.versionRegistro as version,d.regEstado as regEstado,d.regFechaUltimaModificacion as regFecha,d.regUsuarioUltimaModificacion as regUser,d.regFormularioUltimaModificacion as regform,d.regVersionUltimaModificacion as regVersion
			,IFNULL((select usuarioApiID from UsuariosAPI where usuario='" . $usuarioApiID . "' limit 1),2) as usuarioApiID
			FROM Scripts s inner join DetalleScripts d on d.scriptID=s.scriptID
            inner join EjecucionScripts es on es.detalleScriptID=d.detalleScriptID 
			where es.numeroIntentos<(select numeroIntentos from IntentosMaximosReplica) " . $consultaApp . " " . $consultaUser . "
            AND es.estadoEjecucion = 0 
			union 
			SELECT distinct s.scriptID, s.aplicacionID,s.version,s.descripcion,s.fechaInicio,s.fechaFin,s.versionRegistro,s.regEstado,s.regFechaUltimaModificacion,s.regUsuarioUltimaModificacion,s.regFormularioUltimaModificacion,s.regVersionUltimaModificacion, d.detalleScriptID,d.orden,d.texto,d.versionRegistro as version,d.regEstado as regEstado,d.regFechaUltimaModificacion as regFecha,d.regUsuarioUltimaModificacion as regUser,d.regFormularioUltimaModificacion as regform,d.regVersionUltimaModificacion as regVersion 
			,IFNULL((select usuarioApiID from UsuariosAPI where usuario='" . $usuarioApiID . "' limit 1),2) as usuarioApiID
			FROM Scripts s inner join DetalleScripts d on d.scriptID=s.scriptID 
			where 1 " . $consultaApp . " 
			and d.detalleScriptID not in(select detalleScriptID from EjecucionScripts WHERE usuarioApiID=(select usuarioApiID from UsuariosAPI where usuario='" . $usuarioApiID . "' limit 1)) 
			order by orden";
		}
		else{
			$query = "select *from LogEjecucionScripts WHERE fechaEjecucion>'".$fechaInicio."';";
		}
		
		//echo $query;
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// execute the query
    	$stmt->execute();
 
    	// get number of rows
    	$num = $stmt->rowCount();
 
    	// if email exists, assign values to object properties for easy access and use for php sessions
    	if($num>0){
 
        	// get record details / values
        	$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($fechaInicio==""){
				////////////// ACTUALIZA FECHA INICIO ////////////////
				try{
					$query = "
						update Scripts set fechaInicio=now() where scriptID in (
							SELECT distinct s.scriptID FROM Scripts s inner join DetalleScripts d on d.scriptID=s.scriptID
							inner join EjecucionScripts es on es.detalleScriptID=d.detalleScriptID
							where es.numeroIntentos<(select numeroIntentos from IntentosMaximosReplica) " . $consultaApp . " " . $consultaUser . "
							AND es.estadoEjecucion = 0 
							union 
							SELECT distinct s.scriptID FROM Scripts s inner join DetalleScripts d on d.scriptID=s.scriptID 
							where 1 " . $consultaApp . " 
							and d.detalleScriptID not in(select detalleScriptID from EjecucionScripts WHERE usuarioApiID=(select usuarioApiID from UsuariosAPI where usuario='" . $usuarioApiID . "' limit 1))
						);";
					$stmt = $this->Conexion->prepare( $query );
					if($stmt->execute()){
						return true;

					}
				}   
				catch (Exception $e){
					echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$consulta;
				}
				////////////// fin ACTUALIZA FECHA INICIO ////////////////
			}
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

	function ObtenerDatosLog($aplicacionID=0, $usuarioApiID=0, $fechaInicio=""){
   		// $id = intval($id);
		try{
			$consultaApp="";
			$consultaUser="";
			if($aplicacionID>0){
				$consultaApp=" and aplicacionID=" . $aplicacionID;
			} 
			//echo $usuarioApiID;
			// query to check if email exists
			//$query = "select *from LogEjecucionScripts WHERE fechaEjecucion>'".$fechaInicio."' and usuarioApiID=".$usuarioApiID. " order by ejecucionScriptID desc";//.$consultaUser."";
			$query = "select *from LogEjecucionScripts WHERE detalleScriptID=".$usuarioApiID. " order by ejecucionScriptID desc limit 1";
			
			//echo $query;
			// prepare the query
			$stmt = $this->Conexion->prepare( $query );
	 
			// execute the query
			$stmt->execute();
	 
			// get number of rows
			$num = $stmt->rowCount();
	 
			// if email exists, assign values to object properties for easy access and use for php sessions
			if($num>0){
	 
				// get record details / values
				$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				// return true because email exists in the database
				return $this->Dataset;
			}
			else{
				$query ="SELECT 1 as ejecucionScriptID, 1 as usuarioApiID, 1 as scriptID, ".$usuarioApiID. " as detalleScriptID, now() as fechaEjecucion, 0 as estadoEjecucion, 
				'ERROR, EL SCRIPT NO FALLO AL GUARDARSE EN LA TABLA LogEjecucionScripts DE LA BD LOCAL' as resultado, 1 as versionRegistro, 1 as regEstado, now() as regFechaUltimaModificacion, 1 as regUsuarioUltimaModificacion, 1 as regFormularioUltimaModificacion, 1 as regVersionUltimaModificacion";
				$stmt = $this->Conexion->prepare( $query );
	 
				// execute the query
				$stmt->execute();
		 
				// get number of rows
				$num = $stmt->rowCount();

				$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				return $this->Dataset;
			}
	 
			// return false if email does not exist in the database
			return false;
		}	  
		catch (Exception $e){
			echo $this->Mensaje = "ERROR: ".$e->getMessage().'<br /> <br />Consulta: <br />'.$query;
		}
	}
	
	function ObtenerDatosEjecucionScripts($usuarioApiID=0, $detalleScriptID=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from EjecucionScripts WHERE usuarioApiID=".$usuarioApiID." and detalleScriptID=".$detalleScriptID;
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// execute the query
    	$stmt->execute();
 
    	$num = $stmt->rowCount();
 
    	if($num>0){
        	// get record details / values
        	$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 
function ActualizaRegistros($item){

    $consulta="";
	$valorReturn=false;
	//foreach($registros as $item){
		
		/////////////////////////////
		try{
			$query="UPDATE LogEjecucionScripts SET estadoEjecucion=1 where usuarioApiID=".$item['usuarioApiID']." and scriptID=".$item['scriptID']." 
	and detalleScriptID=".$item['detalleScriptID'].";";
					
			
			$stmt = $this->Conexion->prepare( $query );
			if($stmt->execute()){
				$valorReturn= true;
			}
		}   
		catch (Exception $e){
			echo $this->Mensaje = "ERROR: ".$e->getMessage().'<br /> <br />Consulta: <br />'.$query;
		}
		/////////////////////////////
		$cadenaEjecucionScript="";
		try{
			if($this->ObtenerDatosEjecucionScripts($item['usuarioApiID'],$item['detalleScriptID'])){
				$cadenaEjecucionScript= "UPDATE EjecucionScripts SET numeroIntentos=numeroIntentos+1, estadoEjecucion=".$item['estadoEjecucion']."
					WHERE usuarioApiID=".$item['usuarioApiID']." AND detalleScriptID=".$item['detalleScriptID'].";";
				
			}
			else{
				$cadenaEjecucionScript= "INSERT INTO EjecucionScripts (usuarioApiID, detalleScriptID, numeroIntentos, estadoEjecucion, versionRegistro, regEstado, regFechaUltimaModificacion, 
					regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)
					VALUES (".$item['usuarioApiID'].",".$item['detalleScriptID'].",1,".$item['estadoEjecucion'].",".$item['versionRegistro'].", ".$item['regEstado'].", 
					'".$item['regFechaUltimaModificacion']."', ".$item['regUsuarioUltimaModificacion'].", ".$item['regFormularioUltimaModificacion'].", 
					".$item['regVersionUltimaModificacion'].");";
			}
		}
		catch (Exception $e){
			echo $this->Mensaje = "ERROR E.S.: ".$e->getMessage().'<br /> <br />Consulta: <br />'.$query;
		}
		
		try{
			$query="
			UPDATE LogEjecucionScripts SET estadoEjecucion=1 where usuarioApiID=".$item['usuarioApiID']." and scriptID=".$item['scriptID']." and detalleScriptID=".$item['detalleScriptID'].";
			".$cadenaEjecucionScript."
			INSERT INTO LogEjecucionScripts (usuarioApiID, scriptID, detalleScriptID, fechaEjecucion, estadoEjecucion, resultado, 
			versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) 
					VALUES (".$item['usuarioApiID'].",".$item['scriptID'].", ".$item['detalleScriptID'].", now(), ".$item['estadoEjecucion'].", '".str_replace("'","",$item['resultado'])."', 
					".$item['versionRegistro'].", ".$item['regEstado'].", '".$item['regFechaUltimaModificacion']."', 
					".$item['regUsuarioUltimaModificacion'].", ".$item['regFormularioUltimaModificacion'].", ".$item['regVersionUltimaModificacion'].");";
			
			$stmt = $this->Conexion->prepare( $query );
			if($stmt->execute()){
				$valorReturn= true;
				
			}
		}   
		catch (Exception $e){
			echo $this->Mensaje = "ERROR: ".$e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		}
	//}
	return $valorReturn;
}


function FinalizarProceso($scriptID=0, $detalleScriptID=0){

    $consulta="";
	$valorReturn=false;
	//foreach($registros as $item){
		try{
			$query="UPDATE Scripts set fechaFin=now() where scriptID in(".$scriptID.");
						UPDATE DetalleScripts set numeroIntentos=numeroIntentos+1 where detalleScriptID in(".$detalleScriptID.");";
					
			
			$stmt = $this->Conexion->prepare( $query );
			if($stmt->execute()){
				$valorReturn= true;
			}
		}   
		catch (Exception $e){
			echo $this->Mensaje = "ERROR: ".$e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		}
	//}
	return $valorReturn;
}

//EjecutaScripts
function EjecutaScripts($registros, $usuarioApiID, $aregloID){

    $aregloID="MARCO BLANCO COCOM";
	
	$consulta = "";
			
		$coma = false;
		$comaText = "";
		$fechaInicio="";
		$scriptIDInicio="0";
		$valorReturn=false;
		foreach($registros as $item)
		 {
			$scriptID= $item['scriptID'];
			$usuarioApiID= $item['usuarioApiID'];
			$detalleScriptID= $item['detalleScriptID'];
			$aplicacionID= $item['aplicacionID'];
			$fechaInicio= $item['fechaInicio'];
			$descripcion= $item['descripcion'];
			$version= $item['version'];
			$versionRegistro= $item['versionRegistro'];
			$regEstado= $item['regEstado'];
			$regFechaUltimaModificacion= $item['regFechaUltimaModificacion'];
			$regUsuarioUltimaModificacion= $item['regUsuarioUltimaModificacion'];
			$regFormularioUltimaModificacion= $item['regFormularioUltimaModificacion'];
			$regVersionUltimaModificacion= $item['regVersionUltimaModificacion'];
			 
			//echo "<br />";
			$TEXTO= str_replace("DELIMITER","",$item["texto"],$contar);
			/*
			IF($contar>0){
				ECHO "DELIMITER
				". $TEXTO;
			}
			ELSE{
				ECHO "345678";
			}*/
			$consulta= $consulta . $TEXTO;
			//echo "<br />";
			$scriptIDInicio=$scriptID;
			//if($scriptIDInicio<>$item['scriptID'] and $scriptIDInicio<>"0"){ 
			//if(1==1){
			//echo $item["texto"];
				//////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////////////////////
				$this->query=$TEXTO;
	
				// prepare the query
				$stmt = $this->Conexion->prepare($this->query);
				
				$validaejecucion= 0;
				try{
				   $resultado= "";
					// execute the query, also check if query was successful
					if($stmt->execute()){
						$validaejecucion= 1;
						$resultado= "SCRIPTS INSERTADOS CORRECTAMENTE: " . str_replace("'","\'",$consulta);

					}
					else{ 
						$validaejecucion=0;
						$resultado= "SCRIPT NO EJECUTADO " . $consulta;
					}
				}   
				catch (Exception $e){
						$validaejecucion=0;
					$resultado= "EXCEPCION AL EJECUTAR EL SCRIPT: " . str_replace("'","\'",$TEXTO). $e->getMessage();// . " CONSULTA: " . $consulta;
					$this->Mensaje = "DATOOO".$e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
				}
				////////////// INSERTA LOG ////////////////
				try{
					$query="INSERT INTO LogEjecucionScripts (usuarioApiID, scriptID, detalleScriptID, fechaEjecucion, estadoEjecucion, resultado, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) 
							VALUES (".$usuarioApiID.",".$scriptID.", ".$detalleScriptID.", now(), ".$validaejecucion.", '".str_replace("'","",str_replace("\'","",$resultado))."', ".$versionRegistro.", ".$regEstado.", '".$regFechaUltimaModificacion."', ".$regUsuarioUltimaModificacion.", ".$regFormularioUltimaModificacion.", ".$regVersionUltimaModificacion.");";
					//echo "<br />".$query."<br />";
					$stmt = $this->Conexion->prepare( $query );
					if($stmt->execute()){
						$valorReturn= true;

					}
				}   
				catch (Exception $e){
					echo $scriptID."-Error en LogEjecucionScripts: ".$this->Mensaje = "ERROR: ".$e->getMessage()."<br />";//.'<br /> <br />Consulta: <br />'.$consulta;
					$valorReturn= false;
					echo "<br/>";
				}
				////////////// fin INSERTA LOG ////////////////
				
				$consulta = "";
				
				//////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////////////////////
			//}
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
    return $valorReturn;
}



function Cambia($ejecucionScriptID,$usuarioApiID,$scriptID,$fechaEjecucion,$estadoEjecucion,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET usuarioApiID=:usuarioApiID,scriptID=:scriptID,fechaEjecucion=:fechaEjecucion,estadoEjecucion=:estadoEjecucion,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE ejecucionScriptID=:ejecucionScriptID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$ejecucionScriptID=htmlspecialchars(strip_tags($ejecucionScriptID));
	$usuarioApiID=htmlspecialchars(strip_tags($usuarioApiID));
	$scriptID=htmlspecialchars(strip_tags($scriptID));
	$fechaEjecucion=htmlspecialchars(strip_tags($fechaEjecucion));
	$estadoEjecucion=htmlspecialchars(strip_tags($estadoEjecucion));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':ejecucionScriptID', $ejecucionScriptID);
	$stmt->bindParam(':usuarioApiID', $usuarioApiID);
	$stmt->bindParam(':scriptID', $scriptID);
	$stmt->bindParam(':fechaEjecucion', $fechaEjecucion);
	$estadoEjecucion = (int)$estadoEjecucion;
	$stmt->bindValue(':estadoEjecucion', $estadoEjecucion, PDO::PARAM_INT);
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
        echo $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}
	
		
}
	?>