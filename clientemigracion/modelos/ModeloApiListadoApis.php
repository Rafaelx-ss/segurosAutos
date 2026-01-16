<?php
// 'user' object
class ListadoApis{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "ListadoApis";
 
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


	function ObtenerDatos( $aplicacionID=0,$establecimientoID=0, $grupoestablecimientoID=0, $tipoLista=""){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select DISTINCT AplicacionesGrupos.aplicacionGrupoID,AplicacionesGrupos.nombreAplicacionGrupo,AplicacionesGrupos.aplicacionID
    	,Aplicaciones.nombreAplicacion ,ServidoresApis.direccionServidor,Apis.nombreApi,Apis.rutaApi
    	from AplicacionesGrupos,Aplicaciones,ServidoresApis,AplicacionessGruposEstablecimientos,Apis,Establecimientos
    	where Apis.estadoApi=1
		and AplicacionesGrupos.aplicacionID=Aplicaciones.aplicacionID 
    	and AplicacionesGrupos.aplicacionGrupoID=ServidoresApis.aplicacionGrupoID 
    	and AplicacionesGrupos.aplicacionGrupoID=AplicacionessGruposEstablecimientos.aplicacionGrupoID 
    	and AplicacionesGrupos.aplicacionID=Apis.aplicacionID
		and Establecimientos.establecimientoID=AplicacionessGruposEstablecimientos.establecimientoID
        " . ($aplicacionID > 0 ? " and AplicacionesGrupos.aplicacionID = :aplicacionID " : "") . "
		" . ($establecimientoID > 0 ? " and AplicacionessGruposEstablecimientos.establecimientoID = :establecimientoID " : "") . "
		" . ($grupoestablecimientoID > 0 ? " and Establecimientos.grupoID = :grupoestablecimientoID " : "") . " 
		" . ($tipoLista <> '' ? " and Apis.tipoLista = :tipoLista " : "") . " 
		order by ordenMigracion asc";
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
    	$aplicacionID=htmlspecialchars(strip_tags($aplicacionID));
    	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
    	$grupoestablecimientoID=htmlspecialchars(strip_tags($grupoestablecimientoID));
    	$tipoLista=htmlspecialchars(strip_tags($tipoLista));
 
    	// bind given id value

    
    	if($aplicacionID > 0){
      		$stmt->bindParam(":aplicacionID", $aplicacionID);
    	}
    	if($establecimientoID > 0){
			$stmt->bindParam(":establecimientoID", $establecimientoID);
    	}
    	if($grupoestablecimientoID > 0){
			$stmt->bindParam(":grupoestablecimientoID", $grupoestablecimientoID);
    	}
    	if($tipoLista <> ""){
			$stmt->bindParam(":tipoLista", $tipoLista);
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