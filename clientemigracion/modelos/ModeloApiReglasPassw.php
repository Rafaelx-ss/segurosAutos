<?php 
class ReglasPassw{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ReglasPassw'; 
 
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
	
	function ObtenerDatos( $id=0){
		try{
			
			// query to check if email exists
			//$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where minimioLongitudPassw = ?" : "");
			$query = "select * from " . $this->NombreTabla ;
			// prepare the query
			$stmt = $this->Conexion->prepare( $query );
			$this->Mensaje2=$query;
 
			// sanitize
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
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}

	function Inserta($registros){
		try{
			$consulta="";
	
			$consulta = 'INSERT INTO ' . $this->NombreTabla . 
				" (minimioLongitudPassw,maximoIntentosFallidos,tiempoCaducidadCodigoRecuperacionPassw,tiempoCaducidadInactivadadPassw,contieneMayuscula,contieneCaracteresEspeciales,contieneNumeros,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
			$coma = false;
			$comaText = "";
			foreach($registros as $item)
			{
				if($this->ObtenerDatos($item['minimioLongitudPassw'])){
					$this->Cambia($item['minimioLongitudPassw'],$item['maximoIntentosFallidos'],$item['tiempoCaducidadCodigoRecuperacionPassw'],$item['tiempoCaducidadInactivadadPassw'],$item['contieneMayuscula'],$item['contieneCaracteresEspeciales'],$item['contieneNumeros'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
					$consulta= $consulta . $comaText . "(" . $item["minimioLongitudPassw"] . "," . $item["maximoIntentosFallidos"] . "," . $item["tiempoCaducidadCodigoRecuperacionPassw"] . "," . $item["tiempoCaducidadInactivadadPassw"] . ",'" . $item["contieneMayuscula"] . "','" . $item["contieneCaracteresEspeciales"] . "','" . $item["contieneNumeros"] . "'," . $item["versionRegistro"] . ",'" . $item["regEstado"] . "','" . $item["regFechaUltimaModificacion"] . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
			$minimioLongitudPassw=htmlspecialchars(strip_tags($item['minimioLongitudPassw']));
			$maximoIntentosFallidos=htmlspecialchars(strip_tags($item['maximoIntentosFallidos']));
			$tiempoCaducidadCodigoRecuperacionPassw=htmlspecialchars(strip_tags($item['tiempoCaducidadCodigoRecuperacionPassw']));
			$tiempoCaducidadInactivadadPassw=htmlspecialchars(strip_tags($item['tiempoCaducidadInactivadadPassw']));
			$contieneMayuscula=htmlspecialchars(strip_tags($item['contieneMayuscula']));
			$contieneCaracteresEspeciales=htmlspecialchars(strip_tags($item['contieneCaracteresEspeciales']));
			$contieneNumeros=htmlspecialchars(strip_tags($item['contieneNumeros']));
			$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
			$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
			// bind the values
			$stmt->bindParam(':minimioLongitudPassw', $minimioLongitudPassw);
			$stmt->bindParam(':maximoIntentosFallidos', $maximoIntentosFallidos);
			$stmt->bindParam(':tiempoCaducidadCodigoRecuperacionPassw', $tiempoCaducidadCodigoRecuperacionPassw);
			$stmt->bindParam(':tiempoCaducidadInactivadadPassw', $tiempoCaducidadInactivadadPassw);
			$stmt->bindParam(':contieneMayuscula', $contieneMayuscula);
			$stmt->bindParam(':contieneCaracteresEspeciales', $contieneCaracteresEspeciales);
			$stmt->bindParam(':contieneNumeros', $contieneNumeros);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$stmt->bindParam(':regEstado', $regEstado);
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
				$this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
			}
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}

	function Cambia($minimioLongitudPassw,$maximoIntentosFallidos,$tiempoCaducidadCodigoRecuperacionPassw,$tiempoCaducidadInactivadadPassw,$contieneMayuscula,$contieneCaracteresEspeciales,$contieneNumeros,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
		try{
			$query = "UPDATE " . $this->NombreTabla . " SET maximoIntentosFallidos=:maximoIntentosFallidos,tiempoCaducidadCodigoRecuperacionPassw=:tiempoCaducidadCodigoRecuperacionPassw,tiempoCaducidadInactivadadPassw=:tiempoCaducidadInactivadadPassw,contieneMayuscula=:contieneMayuscula,contieneCaracteresEspeciales=:contieneCaracteresEspeciales,contieneNumeros=:contieneNumeros,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
				WHERE minimioLongitudPassw=:minimioLongitudPassw ";

			// prepare the query
			$stmt = $this->Conexion->prepare($query);
			$this->Mensaje2=$query;
 
			// sanitize
			$minimioLongitudPassw=htmlspecialchars(strip_tags($minimioLongitudPassw));
			$maximoIntentosFallidos=htmlspecialchars(strip_tags($maximoIntentosFallidos));
			$tiempoCaducidadCodigoRecuperacionPassw=htmlspecialchars(strip_tags($tiempoCaducidadCodigoRecuperacionPassw));
			$tiempoCaducidadInactivadadPassw=htmlspecialchars(strip_tags($tiempoCaducidadInactivadadPassw));
			$contieneMayuscula=htmlspecialchars(strip_tags($contieneMayuscula));
			$contieneCaracteresEspeciales=htmlspecialchars(strip_tags($contieneCaracteresEspeciales));
			$contieneNumeros=htmlspecialchars(strip_tags($contieneNumeros));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
			// bind the values
			$stmt->bindParam(':minimioLongitudPassw', $minimioLongitudPassw);
			$stmt->bindParam(':maximoIntentosFallidos', $maximoIntentosFallidos);
			$stmt->bindParam(':tiempoCaducidadCodigoRecuperacionPassw', $tiempoCaducidadCodigoRecuperacionPassw);
			$stmt->bindParam(':tiempoCaducidadInactivadadPassw', $tiempoCaducidadInactivadadPassw);
			$stmt->bindParam(':contieneMayuscula', $contieneMayuscula);
			$stmt->bindParam(':contieneCaracteresEspeciales', $contieneCaracteresEspeciales);
			$stmt->bindParam(':contieneNumeros', $contieneNumeros);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$stmt->bindParam(':regEstado', $regEstado);
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
				$this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
			}
 
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}
}
?>