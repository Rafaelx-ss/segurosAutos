<?php
// 'user' object
class Establecimientos{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "Establecimientos";
 
    // object properties
    public $Campos;
    public $Dataset;
	public $Mensaje;
    
    
 
    // constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
 

 
	// ObtenerEstados() method will be here
	// check if given email exist in the database


	function ObtenerDatos( $id=0, $grupoID=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select est.establecimientoID,est.aliasEstablecimiento,est.razonSocialEstablecimiento,est.rfcEstablecimiento,est.codigoEstablecimiento,est.codigo2Establecimiento,
    	est.representanteLegal,est.activoEstablecimiento,est.fechaAltaEstablecimiento,est.grupoID,grup.nombreGrupo,est.versionRegistro,est.regEstado 
		,est.regFechaUltimaModificacion,est.regUsuarioUltimaModificacion,est.regFormularioUltimaModificacion,est.regVersionUltimaModificacion
    	from Establecimientos est,GruposEstablecimientos grup where est.grupoID=grup.grupoID " . ($id > 0 ? " and est.establecimientoID = :establecimientoID " : "") . 
		($grupoID > 0 ? " and grup.grupoID = :grupoID " : "") . " order by est.establecimientoID";
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
    	$id=htmlspecialchars(strip_tags($id));
 
    	// bind given id value

    
    	if($id > 0){
      		$stmt->bindParam(":establecimientoID", $id);
    	}
		if($grupoID > 0){
      		$stmt->bindParam(":grupoID", $grupoID);
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
	}
 
	
}