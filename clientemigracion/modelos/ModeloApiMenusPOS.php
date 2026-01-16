<?php 
class MenusPOS{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'MenusPOS'; 
 
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
			$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where menuPOSID = ?" : "");
 
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
				" (menuPOSID,nombreMenuPOS,tipoPosID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
			$coma = false;
			$comaText = "";
			foreach($registros as $item)
			{
				//echo $item['textoID']."lolol";
				if($this->ObtenerDatos($item['menuPOSID'])){
					$this->Cambia($item['menuPOSID'],$item['nombreMenuPOS'],$item['tipoPosID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
					$consulta= $consulta . $comaText . "('" . $item["menuPOSID"] . "','" . $item["nombreMenuPOS"] . "','" . $item["tipoPosID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
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
			$menuPOSID=htmlspecialchars(strip_tags($item['menuPOSID']));
			$nombreMenuPOS=htmlspecialchars(strip_tags($item['nombreMenuPOS']));
			$tipoPosID=htmlspecialchars(strip_tags($item['tipoPosID']));
			$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
			$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
			// bind the values
			$stmt->bindParam(':menuPOSID', $menuPOSID);
			$stmt->bindParam(':nombreMenuPOS', $nombreMenuPOS);
			$stmt->bindParam(':tipoPosID', $tipoPosID);
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
				$this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
			}
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}

	function Cambia($menuPOSID,$nombreMenuPOS,$tipoPosID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
		try{
			$query = "UPDATE " . $this->NombreTabla . " SET nombreMenuPOS=:nombreMenuPOS,tipoPosID=:tipoPosID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
				WHERE menuPOSID=:menuPOSID ";

			// prepare the query
			$stmt = $this->Conexion->prepare($query);
			$this->Mensaje2=$query;

			// sanitize
			$menuPOSID=htmlspecialchars(strip_tags($menuPOSID));
			$nombreMenuPOS=htmlspecialchars(strip_tags($nombreMenuPOS));
			$tipoPosID=htmlspecialchars(strip_tags($tipoPosID));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
			// bind the values
			$stmt->bindParam(':menuPOSID', $menuPOSID);
			$stmt->bindParam(':nombreMenuPOS', $nombreMenuPOS);
			$stmt->bindParam(':tipoPosID', $tipoPosID);
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