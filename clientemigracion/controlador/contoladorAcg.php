
        <?php
            //require 'lib/database.php';
           //echo $txtBanderaApi;
            require 'modelos/ModeloApiListado.php';
            //require 'modelos/ModeloApiMigracion.php';
			$incluir= 'modelos/Modelo' . trim($txtBanderaApi) . '.php';
			require $incluir;
            $database = new Database();
            $conn = $database->getConnection();
            
			$listadoApi = new ListadoApi($database);
			
			if(trim($txtBanderaApi) != "ListadoApi"){
				$objeto= str_replace('Api','',trim($txtBanderaApi));
				$migracion = new $objeto($database);
			}
			echo "--".$txtBanderaApi."--";
			$exito = false;
			$resultado= $migracion->ObtenerDatos($txtTabla, $DB);
			if($resultado)
			{
				echo $txtTabla;
				$conexion->Dataset['UsuarioApiLista'];
				$LeerTabla= $migracion->Dataset;
				$contador=0;
				$CampoPrimary="";
				$Campos="";
				$CamposActualiza="";
				$CamposInserta="";
				$CamposParametroActualiza="";
				$CamposParametro="";
				$CampoAactualizar="";
				$CampoWhereActualiza="";
				$CamposBindActualizar="";
				$Separador="";
				$Sanitizar=" // sanitize";
				$SanitizarActualizar=" // sanitize";
				$BindearValores=" // bind the values";
				foreach($LeerTabla as $item)
                {
					echo $item['columna']." tipo: ". $item['COLUMN_TYPE']." EXTRA: ". $item['EXTRA']."<br />";
					
					IF ($item['EXTRA'] == "auto_increment"){
						$CampoPrimary=$item['columna'];
						$CampoWhereActualiza= "" . $item['columna'] . "=:" . $item['columna'] . "";
					}
					else{
						if($contador == 1){
							$CampoAactualizar= "" . $item['columna'] . "=:" . $item['columna'] . "";
						}else{
							$CampoAactualizar .= "," . $item['columna'] . "=:" . $item['columna'] . "";
						}
					}
						
					if($contador > 0){
						$Separador=",";
					}
					$Campos.= $Separador.$item['columna'];
					//$CamposInserta.= $Separador.":".$item['columna'];
					$CamposParametro .= $Separador."$"."" . $item['columna'] . "";
					$CamposParametroActualiza .= $Separador."$"."item['".$item['columna']."']";//"   $"."" . $item['columna'] . "";
					
					// sanitize
					$Sanitizar.="
	$" . $item['columna'] . "=htmlspecialchars(strip_tags($".""."item['" . $item['columna'] . "']));";
					$SanitizarActualizar.="
	$" . $item['columna'] . "=htmlspecialchars(strip_tags($"."" . $item['columna'] . "));";
					
					// bind the values
					if($item['COLUMN_TYPE'] == "bit(1)"){
						$BindearValores.="
	$".$item['columna']." = (int)$".$item['columna'].";
	$"."stmt->bindValue(':".$item['columna']."', $".$item['columna'].", PDO::PARAM_INT);";
						$CamposInserta.= $Separador.'" . $item["'.$item['columna'].'"] . "';
					}else{
						if($item['COLUMN_TYPE'] == "int(11)"){
							$CamposInserta.= $Separador.'" . $item["'.$item['columna'].'"] . "';
						}
						else{
							$CamposInserta.= $Separador.'\'" . $item["'.$item['columna'].'"] . "\'';
						}
						$BindearValores.="
	$"."stmt->bindParam(':".$item['columna']."', $".$item['columna'].");";
						}
					
					/*if($contador == 0){
						$CamposActualiza = "$"."item['" . $item['columna'] . "']";
						$CamposParametroActualiza= "$"."" . $item['columna'] . "";
					}else{
						$CamposActualiza .= ",$"."item['" . $item['columna'] . "']";
						$CamposParametroActualiza .= ",$"."" . $item['columna'] . "";
					}*/
					
					$contador++;
				}
				
				//crear archivo
				$fh = fopen("modelos/ModeloApi" . ucfirst($txtTabla) . ".php", 'w') or die("Se produjo un error al crear el archivo");
				
				echo ucfirst($txtTabla)."<br />";
				
				$archivo .= "<?php \n";
				$archivo .= "class " . $txtTabla . "{ \n \n";
				$archivo .= "private $"."Conexion; \n";
				$archivo .= "private $"."Database; \n";
				$archivo .= "private $"."NombreTabla = '" . $txtTabla . "'; \n \n";
				$archivo .= "// object properties\n";
				$archivo .= "public $"."Campos;\n";
				$archivo .= "public $"."Dataset;\n \n";
				$archivo .= "// constructor
    public function __construct($"."dbf){
        $"."this->Database = $"."dbf;
        $"."this->Conexion = $"."dbf->conn;
    }
	
	function ObtenerDatos( $"."id=0){
   		// $"."id = intval($"."id);
    	// query to check if email exists
    	$"."query = \"select * from \" . $"."this->NombreTabla . \" \" . ($"."id > 0 ? \" where " . $CampoPrimary . " = ?\" : \"\");
 
    	// prepare the query
    	$"."stmt = $"."this->Conexion->prepare( $"."query );
 
    	// sanitize
    	$"."id=htmlspecialchars(strip_tags($"."id));
 
    	// bind given id value

    
    	if($"."id > 0){
      		$"."stmt->bindParam(1, $"."id);
    	}
    	// execute the query
    	$"."stmt->execute();
 
    	// get number of rows
    	$"."num = $"."stmt->rowCount();
 
    	// if email exists, assign values to object properties for easy access and use for php sessions
    	if($"."num>0){
 
        	// get record details / values
        	$"."this->Dataset = $"."stmt->fetchAll(PDO::FETCH_ASSOC);
 
 
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($"."registros){

    $"."consulta=\"\";
	
	$"."consulta = 'INSERT INTO ' . $"."this->NombreTabla . 
			\" (" . $Campos . ") VALUES \";
			
		$"."coma = false;
		$"."comaText = \"\";
		foreach($"."registros as $"."item)
		 {
			//echo $"."item['textoID'].\"lolol\";
			 if($"."this->ObtenerDatos($"."item['" . $CampoPrimary . "'])){
				$"."this->Cambia(" . $CamposParametroActualiza . ");
			}
			else{
				 if($"."coma)
				 {
					 $"."comaText=\",\";
				 }
				 else
				 {
					 $"."comaText=\"\";
					 $"."coma = true;
				 }
				
				$"."consulta= $"."consulta . $"."comaText . \"(" . $CamposInserta . ")\";
			}
			 
		   
		 }
	if(!$"."coma){
		$"."consulta=\"select 1\";
	}
	
	$"."this->query=$"."consulta;
	
    // prepare the query
    $"."stmt = $"."this->Conexion->prepare($"."this->query);
	
   ".$Sanitizar."
   
   ".$BindearValores."
   
   try{
        // execute the query, also check if query was successful
        if($"."stmt->execute()){
            return true;

        }
    }   
    catch (Exception $"."e){
        echo $"."this->mensaje = $"."e->getMessage();//.'<br /> <br />Consulta: <br />'.$"."consulta;
    }

 
    return false;
}


function Cambia(" . $CamposParametro . "){
    $"."query = \"UPDATE \" . $"."this->NombreTabla . \" SET " . $CampoAactualizar . " 
		WHERE " . $CampoWhereActualiza . " \";

    // prepare the query
    $"."stmt = $"."this->Conexion->prepare($"."query);
 
    ".$SanitizarActualizar."
   
   ".$BindearValores."

    try{
        // execute the query, also check if query was successful
        if($"."stmt->execute()){
            return true;

        }
    }   
    catch (Exception $"."e){
        echo $"."this->mensaje = $"."e->getMessage();//.'<br /> <br />Consulta: <br />'.$"."consulta;
    }
 
    return false;
}
	
		
}
	";
				
				$mensaje2 = "Se guardaron " . count($migracion->Dataset) . " Registros.";
				$exito=true;
				
				$archivo .= '?>';

				//echo $archivo;
				
				fwrite($fh, $archivo) or die("No se pudo escribir en el archivo");
				fclose($fh);
				//fin crear archivo
				
				
				
				
				
				
				/////////////////////////////////////////////////////////////////////////////////////////////
				//crear archivo 2  //////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////
				$archivo="";
				$fh = fopen("api/Api" . ucfirst($txtTabla) . ".php", 'w') or die("Se produjo un error al crear el archivo");
				
				echo ucfirst($txtTabla)."<br />";
				
				$archivo .= "<?php \n";
				//codigo aki
				$archivo .= "require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $"."tabla = ApiTools::getModel('".$txtTabla."');
    $"."id=ApiTools::getParam('Id');
    $"."tabla->ObtenerDatos($"."id);
    ApiTools::asignaRespuesta(200,'datos entregados' ,true,$"."tabla->Dataset);
},'GET');

ApiTools::asignaMetodo(function() {
    $"."tabla = ApiTools::getModel('".$txtTabla."'); // abrir la tabla
	
    if( $"."tabla->Crear2(ApiTools::$"."datosCliente))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$"."tabla->Dataset);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' ,true,null);
    }
},'POST');

ApiTools::processRequest();

ApiTools::respuestaApi();";
				$archivo .= '?>';
				
				fwrite($fh, $archivo) or die("No se pudo escribir en el archivo");
				fclose($fh);
				/////////////////////////////////////////////////////////////////////////////////////////////
				//fin crear archivo 2  //////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////
				
			}
			else
			{
				echo "\n\n
				<br />No encontro ninguna tabla con el nombre '".$txtTabla."'
				<br />";
				$mensaje2 = "Hubo un error";
				$exito=false;
			}	
			
				
				
				
				
				
				
			$servidor = $listadoApi->Dataset['direccionServidor'];
			$servidor = "http://10.168.88.230/ApiCongo";
			
			$RutaApi = $listadoApi->Dataset['rutaApi'];
			//echo $servidor .$RutaApi;
		

			require_once 'ApiHttpClient.php';
			ApiHttpClient::Init($servidor);
			ApiHttpClient::$Token = $_SESSION["Token"];
			ApiHttpClient::$MacAddress = $_SESSION["MacAddress"];
			
			//echo "AKI".$RutaApi."--".$objeto;
			$IdParametro=0;
			$IdGrupoParametro=0;
			if(trim($objeto) == "GruposEstablecimientos"){
				$IdParametro=$_SESSION["IDENTIFICADOR"];
			}
			if(trim($objeto) == "Establecimientos" || trim($objeto) == "DireccionesEstablecimientos"){
				$IdGrupoParametro=$_SESSION["IDENTIFICADOR"];
			}
			
			$parametros = json_encode(Array(
				"Token" => ApiHttpClient::$Token,
				"Id" => $IdParametro,
				"grupoID" => $IdGrupoParametro,
				"MacAddress" => ApiHttpClient::$MacAddress
			)); 
			echo $RutaApi;
			$datos = ApiHttpClient::ConsumeApi($RutaApi,'GET',$parametros);
			
			$arraydatos = json_decode($datos,true);
		   //echo "as".count($coleccion)."pp";
		   //echo $_SESSION["Token"]." MAC " . $_SESSION["MacAddress"];
			//echo $arraydatos['mensaje'];
		
			
       if(!$exito)
			$mensaje2= "Error al guardar los datos. ".$mensaje2;
        ?>