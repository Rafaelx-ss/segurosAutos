<?php
// 'user' object
class Migracion{
 
    // database connection and table name
    private $Conexion;
    private $NombreTabla = "Paises";
 
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
function ObtenerUno($id){
 
    // query to check if email exists
    $query = "select prd.ProductoID, prd.codigoProducto, prd.nombreProducto, prp.precioVenta
        , prp.fechaInicio from " . $this->NombreTabla . " as prd
        inner join ProductosPreciosVenta as prp on prp.productoID=prd.productoID
        inner join TiposProductos as tpr on prd.tipoProductoID=tpr.tipoProductoID
        where tpr.nombreTipoProducto=? " . ($id > 0 ? " and prd.ProductoID = ?" : "");
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query );
 
    // sanitize
    $id=htmlspecialchars(strip_tags($id));
    $tipo=htmlspecialchars(strip_tags($tipo));
 
    // bind given id value

    $stmt->bindParam(1, $tipo);
    if($id > 0){
      $stmt->bindParam(2, $id);
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


function ObtenerDatos( $tabla){
 
	$tabla=str_replace('Api','', $tabla);
    $query = "select * from " . $tabla . " as prd " 
        . ($id > 0 ? " where prd.ProductoID = ?" : "");
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query );
 
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
}
 

function getQuery($tabla){
// insert query
    $consulta = 'INSERT INTO ' . $tabla . 
    " (paisID, nombrePais, estadoPais, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
    
    $coma = false;
    foreach($registros as $item)
     {
         if($coma)
         {
            $consulta= $consulta . ",(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
         else
         {
             $coma = true;
             $consulta= $consulta . "(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
       
     }
	 return $consulta;
}
function Inserta2($tabla,$registros){

	$tabla=str_replace('Api','', $tabla);
    
	$consulta = 'INSERT INTO ' . $tabla . 
    " (paisID, nombrePais, estadoPais, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
    
    $coma = false;
    foreach($registros as $item)
     {
         if($coma)
         {
            $consulta= $consulta . ",(" . $item['paisID'] . ",'" . utf8_decode($item['nombrePais']) . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
         else
         {
             $coma = true;
             $consulta= $consulta . "(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
       
     }
	//$this->query=getQuery($tabla);
    
	$this->query=$consulta;
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}

function Inserta($tabla,$registros){

	$tabla=str_replace('Api','', $tabla);
    $consulta="";
	
	if(trim($tabla) == "Paises"){
			$consulta = 'INSERT INTO ' . $tabla . 
			" (paisID, nombrePais, estadoPais, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
			$coma = false;
			foreach($registros as $item)
			 {
				 if($coma)
				 {
					$consulta= $consulta . ",(" . $item['paisID'] . ",'" . utf8_decode($item['nombrePais']) . "'," . 
					$item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
					$item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
					 $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
				 }
				 else
				 {
					 $coma = true;
					 $consulta= $consulta . "(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
					$item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
					$item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
					 $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
				 }
			   
			 }
	}
	if(trim($tabla) == "Estados"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (estadoID, nombreEstado, estadoEstado, paisID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['estadoID'] . ",'" . utf8_decode($item['nombreEstado']) . "'," . 
			$item['estadoEstado'] . "," . $item['paisID']. "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "Distribuidores"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (distribuidorID, nombreDistribuidor, activoDistribuidor, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['distribuidorID'] . ",'" . utf8_decode($item['nombreDistribuidor']) . "'," . 
			$item['activoDistribuidor'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "GruposEstablecimientos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (grupoID, nombreGrupo, activoGrupo, distribuidorID, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['grupoID'] . ",'" . utf8_decode($item['nombreGrupo']) . "'," . 
			$item['activoGrupo'] . "," . $item['distribuidorID']. "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "Establecimientos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (establecimientoID, aliasEstablecimiento, razonSocialEstablecimiento, rfcEstablecimiento, codigoEstablecimiento,
			codigo2Establecimiento,representanteLegal,activoEstablecimiento,fechaAltaEstablecimiento,grupoID,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['establecimientoID'] . ",'" . utf8_decode($item['aliasEstablecimiento']) . "',
			'" . utf8_decode($item['razonSocialEstablecimiento']) . "','" . $item['rfcEstablecimiento']. "','" . $item['codigoEstablecimiento']. "',
			'" . $item['codigo2Establecimiento'] . "','" . $item['representanteLegal'] . "'," . $item['activoEstablecimiento']. ",
			'" . $item['fechaAltaEstablecimiento']. "'," .  $item['grupoID'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "DireccionesEstablecimientos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (direccionEstablecimientoID, establecimientoID, alias, numeroInterior, numeroExterior,codigoPostal,colonia,
			localidad,referencia,municipio,esDefault,estadoID,activoDireccion,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion,calle) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['direccionEstablecimientoID'] . "," . $item['establecimientoID'] . ",
			'" . utf8_decode($item['alias']) . "',
			'" . $item['numeroInterior'] . "','" . $item['numeroExterior']. "','" . $item['codigoPostal']. "',
			'" . utf8_decode($item['colonia']) . "','" . utf8_decode($item['localidad']) . "','" . utf8_decode($item['referencia']) . "',
			'" . utf8_decode($item['municipio']) . "'," . $item['esDefault'] . "," . $item['estadoID']. ",
			" . $item['activoDireccion']. "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ",'" .  $item['calle'] . "')";
		   
		 }
	}
	if(trim($tabla) == "TiposInterfaz"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoInterfazID, nombreInterfaz, tipoComunicacion, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoInterfazID'] . ",'" . utf8_decode($item['nombreInterfaz']) . "',
			'" . $item['tipoComunicacion'] . "'," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposProductos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoProductoID, nombreTipoProducto, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoProductoID'] . ",'" . utf8_decode($item['nombreTipoProducto']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "FEClaveUnidad"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (claveUnidadID, claveUnidad, nombre, descripcion, fechaDeInicioDeVigencia, fechaDeFinDeVigencia, simbolo, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['claveUnidadID'] . ",'" . utf8_decode($item['claveUnidad']) . "',
			'" . utf8_decode($item['nombre']) . "','" . utf8_decode($item['descripcion']) . "',
			'" . $item['fechaDeInicioDeVigencia'] . "','" . $item['fechaDeFinDeVigencia'] . "','" . $item['simbolo'] . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "FEClaveProdServ"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (claveProdServID, claveProdServ, descripcion, fechaDeInicioDeVigencia, fechaDeFinDeVigencia, incluirIVATraslado, incluirIEPSTraslado,
			complementoQueDebeIncluir,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['claveProdServID'] . ",'" . utf8_decode($item['claveProdServ']) . "',
			'" . utf8_decode($item['descripcion']) . "',
			'" . $item['fechaDeInicioDeVigencia'] . "','" . $item['fechaDeFinDeVigencia'] . "','" . $item['incluirIVATraslado'] . "',
			'" . $item['incluirIEPSTraslado'] . "','" . $item['complementoQueDebeIncluir'] . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "ClavesProductosEnviosSAT"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (claveProductoEnvioSATID, claveProductoEnvioSAT, descripcionClaveProductoEnvioSAT, composicionOctanajeDeGasolina, 
			gasolinaConCombustibleNoFosil, composicionDeCombustibleNoFosilEnGasolina, dieselConCombustibleNoFosil,
			composicionDeCombustibleNoFosilEnDiesel,marcaComercial,marcaje,consentracionSustanciaMarcaje,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['claveProductoEnvioSATID'] . ",'" . utf8_decode($item['claveProductoEnvioSAT']) . "',
			'" . utf8_decode($item['descripcionClaveProductoEnvioSAT']) . "','" . utf8_decode($item['composicionOctanajeDeGasolina']) . "',
			'" . $item['gasolinaConCombustibleNoFosil'] . "','" . $item['composicionDeCombustibleNoFosilEnGasolina'] . "','" . $item['dieselConCombustibleNoFosil'] . "',
			'" . $item['composicionDeCombustibleNoFosilEnDiesel'] . "','" . $item['marcaComercial'] . "',
			'" . $item['marcaje'] . "','" . $item['consentracionSustanciaMarcaje'] . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "ClavesSubProductosEnviosSAT"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (claveSubProductoEnvioSATID, claveSubProductoEnvioSAT, descripcionClaveSubProductoEnvioSAT,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['claveSubProductoEnvioSATID'] . ",'" . utf8_decode($item['claveSubProductoEnvioSAT']) . "',
			'" . utf8_decode($item['descripcionClaveSubProductoEnvioSAT']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "ComponentesAlarmas"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (componenteAlarmaID, claveComponenteAlarma, descripcion,versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['componenteAlarmaID'] . ",'" . utf8_decode($item['claveComponenteAlarma']) . "',
			'" . utf8_decode($item['descripcion']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposEventos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoEventoID, claveTipoEvento, descripcion, generaAlarma, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoEventoID'] . ",'" . utf8_decode($item['claveTipoEvento']) . "',
			'" . utf8_decode($item['descripcion']) . "','" . $item['generaAlarma'] . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposEmpleados"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoEmpleadoCodigo, tipoEmpleadoNombre, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoEmpleadoCodigo'] . ",'" . utf8_decode($item['tipoEmpleadoNombre']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposDespachos"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoDespachoID, codigoTipoDespacho, descripcion, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoDespachoID'] . ",'" . utf8_decode($item['codigoTipoDespacho']) . "',
			'" . utf8_decode($item['descripcion']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposPOS"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoPosID, descripcionTipoPOS, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoPosID'] . ",'" . utf8_decode($item['descripcionTipoPOS']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "MenusPOS"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (menuPOSID, nombreMenuPOS, tipoPosID, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['menuPOSID'] . ",'" . utf8_decode($item['nombreMenuPOS']) . "',
			'" . $item['tipoPosID'] . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "TiposMovimientosVentas"){
		$consulta = 'INSERT INTO ' . $tabla . 
			" (tipoMovimientoID, codigoMovimiento, descripcionMovimiento, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['tipoMovimientoID'] . ",'" . utf8_decode($item['codigoMovimiento']) . "',
			'" . utf8_decode($item['descripcionMovimiento']) . "',
			" . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	}
	if(trim($tabla) == "FacturasCentrales"){
		
	}
	if(trim($tabla) == "Almacenes"){
		
	}
	if(trim($tabla) == "AccionesFormularios"){
		
	}

	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        echo $this->Mensaje = $e->getMessage().$consulta;
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}

function BorrarTodo($tabla,$error){

	$tabla=str_replace('Api','', $tabla);
    $query = 'DELETE FROM ' . $tabla ;
    
    
    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
	try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();
		echo $error= $this->Mensaje;
    }
   
    /*// execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }*/
 
    return false;
}
	
}