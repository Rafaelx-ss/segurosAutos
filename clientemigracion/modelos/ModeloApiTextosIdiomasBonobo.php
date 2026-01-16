<?php
class TextosIdiomasBonobo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'TextosIdiomas'; 
 
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
	
	function ObtenerDatos($id=0, $tipoconsulta=""){
   		try{
			// query to check if email exists
			$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where textoIdiomaID = ?" : "");
 
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
 
				if($tipoconsulta=="" or $tipoconsulta=="CONSULTA_LOCAL"){
					return true;
				}
				else{
					return $this->Dataset;
				}
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
				" (textoIdiomaID,texto,textoID,idiomaID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
			$coma = false;
			$comaText = "";
			foreach($registros as $item)
			{
				if($this->ObtenerDatos($item['textoIdiomaID'])){
					$this->Cambia($item['textoIdiomaID'],$item['texto'],$item['textoID'],$item['idiomaID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
					$consulta= $consulta . $comaText . "('" . $item["textoIdiomaID"] . "','" . $item["texto"] . "','" . $item["textoID"] . "','" . $item["idiomaID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
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
			$textoIdiomaID=htmlspecialchars(strip_tags($item['textoIdiomaID']));
			$texto=htmlspecialchars(strip_tags($item['texto']));
			$textoID=htmlspecialchars(strip_tags($item['textoID']));
			$idiomaID=htmlspecialchars(strip_tags($item['idiomaID']));
			$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
			$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
			// bind the values
			$stmt->bindParam(':textoIdiomaID', $textoIdiomaID);
			$stmt->bindParam(':texto', $texto);
			$stmt->bindParam(':textoID', $textoID);
			$stmt->bindParam(':idiomaID', $idiomaID);
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
				$this->mensaje = $e->getMessage().$consulta;
			}
			// execute the query, also check if query was successful
			if($stmt->execute()){
				return true;
			}
 
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}

	function InsertaRegreso($item){
		//print_r($item);
		//return true;
		
		try{
			$consulta="";
		
			$consulta = 'INSERT INTO ' . $this->NombreTabla . 
				" (textoIdiomaID,texto,textoID,idiomaID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
			$coma = false;
			$comaText = "";
			if($this->ObtenerDatos($item['textoIdiomaID'])){
				$this->Cambia($item['textoIdiomaID'],$item['texto'],$item['textoID'],$item['idiomaID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
			
				$consulta= $consulta . $comaText . "('" . $item["textoIdiomaID"] . "','" . $item["texto"] . "','" . $item["textoID"] . "','" . $item["idiomaID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
		
			if(!$coma){
				$consulta="select 1";
			}
	
			$this->query=$consulta;
	
			//return true;
			// prepare the query
			$stmt = $this->Conexion->prepare($this->query);
			$this->Mensaje2=$this->query;
	
			// sanitize
			$textoIdiomaID=htmlspecialchars(strip_tags($item['textoIdiomaID']));
			$texto=htmlspecialchars(strip_tags($item['texto']));
			$textoID=htmlspecialchars(strip_tags($item['textoID']));
			$idiomaID=htmlspecialchars(strip_tags($item['idiomaID']));
			$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
			$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
			// bind the values
			$stmt->bindParam(':textoIdiomaID', $textoIdiomaID);
			$stmt->bindParam(':texto', $texto);
			$stmt->bindParam(':textoID', $textoID);
			$stmt->bindParam(':idiomaID', $idiomaID);
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
				$this->mensaje = $e->getMessage().$consulta;
			}

			// execute the query, also check if query was successful
			 
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}

	function Cambia($textoIdiomaID,$texto,$textoID,$idiomaID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
		try{
			$query = "UPDATE " . $this->NombreTabla . " SET texto=:texto,textoID=:textoID,idiomaID=:idiomaID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
				WHERE textoIdiomaID=:textoIdiomaID ";

			// prepare the query
			$stmt = $this->Conexion->prepare($query);
			$this->Mensaje2=$query;
 
			// sanitize
			$textoIdiomaID=htmlspecialchars(strip_tags($textoIdiomaID));
			$texto=htmlspecialchars(strip_tags($texto));
			$textoID=htmlspecialchars(strip_tags($textoID));
			$idiomaID=htmlspecialchars(strip_tags($idiomaID));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
			// bind the values
			$stmt->bindParam(':textoIdiomaID', $textoIdiomaID);
			$stmt->bindParam(':texto', $texto);
			$stmt->bindParam(':textoID', $textoID);
			$stmt->bindParam(':idiomaID', $idiomaID);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$regEstado = (int)$regEstado;
			$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
			$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
			$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
			$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
			$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	
			// execute the query, also check if query was successful
			if($stmt->execute()){
				return true;
			}
 
			return false;
		}catch (Exception $e){
			$this->Mensaje = $e.getMessage();
			return false;
		}
	}
}
?>
