<?php
//header ('Content-type: text/html; charset=utf-8');
header('Content-Type: text/html; charset=iso-8859-1');
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
/* @var $this yii\web\View */
//Yii::$app->response->redirect(['siniestros/index']);
//$this->title = 'My Yii Application';

if (isset($_POST["TipoSolicitud"])){
	//echo "998".$_POST["TipoSolicitud"];
	Yii::$app->session->set('TIPOSOLICITUD', $_POST["TipoSolicitud"]);
	Yii::$app->session->set('IDENTIFICADOR', $_POST["txtIdentificador"]);
	Yii::$app->session->set('APLICACION_ID', $_POST["txtAplicacion"]);
	Yii::$app->session->set('USUARIOSAPILISTA', $_POST["UsuarioApiLista"]);
}

if(isset(Yii::$app->session['logMigracion'])){
	Yii::$app->session->remove('logMigracion');	
}
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-menu icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Migración				<div class="page-title-subheading">Importar datos</div>
            </div>
        </div>
       
     </div>
</div>

<div class="main-card mb-3 card">
	<div class="card-body">	
		<?php
		$apiBase = Yii::$app->basePath;
		
        $mensaje ='';
		$IdentificadorMaster="";
		
		if ((isset($_SERVER["REQUEST_METHOD"])) && ($_SERVER["REQUEST_METHOD"] == 'POST')){
			
			require_once($apiBase."/clientemigracion/lib/database.php");
			require_once($apiBase."/clientemigracion/modelos/ModeloApiConexion.php");
			require_once($apiBase."/clientemigracion/modelos/ModeloApiListado.php");
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);

            $UserName = $_POST['UsuarioApiLista'];
            $Password = $_POST['Password'];
            $RutaApiLista = $_POST['RutaApiLista'];
            $TipoSolicitud = $_POST['TipoSolicitud'];
			$Identificador = $_POST['txtIdentificador'];//Grupo o Establecimiento
			$Aplicacion = $_POST['txtAplicacion'];
            $parametros =   Array(
                "UsuarioApiLista" => $UserName,
                "PassApiLista" => $Password,
                "RutaApiLista" => $RutaApiLista,
                "TipoSolicitud" => $TipoSolicitud,
				"identificadorEstacion" => $Identificador
            );
			
			Yii::$app->session->set('UserName', $UserName);
			
			
			$NombreAplicacion= "";
			$NombreAplicacion= "";
			
			if($conexion->Update($parametros))
            {
				$form = ActiveForm::begin([
					'id' => 'reg_form',
					'action' => ['site/descargaapis&f='.$_GET['f']],
					'options' => ['method' => 'post'],
				]);
					$OpcionGET="";
					if ($UserName=="masterBrentec"){
						$selectedPOST="";
						if($Identificador=="0"){
							$selectedPOST="selected";
						}elseif($Aplicacion<>"1"){
						}
						$OpcionGET="<option value='GET' >GET</option>";
						
						$combo="
						<div class='form-group' style='max-width:50%;'>
							<label for='TipoSolicitud'>Seleccione el Tipo de Método que desea utilizar:</label>
							<select name='cmbSeleccionaTipoMetodo' id='cmbSeleccionaTipoMetodo' class='form-control' required>
								".$OpcionGET."
								<option value='POST' ".$selectedPOST.">POST</option>
							</select>
						</duv>
						<br /> <br />";
						echo $combo;
					}
					echo "<button type='submit' class='btn btn-primary'> Paso 1. Importar datos</button>";
					//echo Html::a(' Paso 1. Importar datos', $url = ['site/descargaapis&f='.$_GET['f']."&tm="], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
					if (isset($_POST["PASO_1"])){
						echo Html::a(' Consultar datos', $url = ['menus/showdata&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);		
					}
				ActiveForm::end();
				//$mensaje='Se actualizó el registro';			
            }
            else
            {
                echo $mensaje='<br>Ocurrio un error: <br>';
				print_r($parametros);
            }
			
			
		}else{
			///////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////// LEER DATOS DE INICIO //////////////////////////////////
			///////////////////////////////////////////////////////////////////////////////////////////
			$UsernameInicio="";
			$PasswordInicio="";
			$RutaApiInicio="";
			$identificadorEstacion="";
			try{
				require_once($apiBase."/clientemigracion/lib/database.php");
				require_once($apiBase."/clientemigracion/modelos/ModeloApiConexion.php");
				require_once($apiBase."/clientemigracion/modelos/ModeloApiListado.php");
				$database = new Database();
				$conn = $database->getConnection();
				$conexion = new ConexionApi($database);
				if($conexion->ObtenerDatos(1))
				{
					$UsernameInicio = $conexion->Dataset['UsuarioApiLista'];
					$PasswordInicio = $conexion->Dataset['PassApiLista'];
					$RutaApiInicio = $conexion->Dataset['RutaApiLista'];
					$RutaApiInicio = $conexion->Dataset['RutaApiLista'];
					
					$IdentificadorMaster =  0;
					if(isset($conexion->Dataset['identificadorEstacion'])){
						$IdentificadorMaster = $conexion->Dataset['identificadorEstacion'];
					}
					
				}
			}
			catch(Exception $e){
				echo '<div class="alert alert-warning" role="alert">
						  '.$e->getMessage().'
						</div>';
			}
			
			
			///////////////////////////////////////////////////////////////////////////////////////////
			///////////////////////////////// FIN LEER DATOS DE INICIO ////////////////////////////////
			///////////////////////////////////////////////////////////////////////////////////////////
			
			if ($UsernameInicio=="masterBrentec"){
				$IdentificadorMaster="0";
			}
            echo "<h2>Registrar</h2>";
			$form = ActiveForm::begin([
				'id' => 'reg_form',
				'action' => ['site/migracion&f='.$_GET['f']],
				'options' => ['method' => 'post'],
			]);
			
			$contenidoComboAplicacion="";
			try{
				$datosTXT="";
				$idTXT="";
				$nombreTXT="";
				$fp = fopen($apiBase."/clientemigracion/aplicacion.txt", "r");
				$datosTXT = fgets($fp);
				fclose($fp);
				$arrayTXT = explode("-", $datosTXT);
				$idTXT = $arrayTXT[0];
				$nombreTXT = $arrayTXT[1];
				$contenidoComboAplicacion="<option value='".$idTXT."'>".$nombreTXT."</option>";
            }
			catch (Exception $e){
				echo $e->getMessage();
				$contenidoComboAplicacion="<option value='2'>Bonobo</option>";
			}
			echo "<div class='form-group'>
                    <label for='UsuarioApiLista'>Nombre</label>
                    <input type='text' class='form-control' value='".$UsernameInicio."' id='UsuarioApiLista' name='UsuarioApiLista' 
					onBlur='agregarMaster(this.value);'
					placeholder='Nombre' required>
                </div>

                <div class='form-group'>
                    <label for='Password'>Password</label>
                    <input type='password' class='form-control' value='".$PasswordInicio ."' id='Password' name='Password' placeholder='Password' required aria-invalid='false'>
                </div>

                <div class='form-group'>
                    <label for='PasswordVerificar'>Repetir Password</label>
                    <input type='password' class='form-control' value='".$PasswordInicio."' id='PasswordVerificar' name='PasswordVerificar' placeholder='Password' required aria-invalid='false'>
                </div>

                <div class='form-group'>
                    <label for='RutaApiLista'>Ruta</label>
                    <input type='text' class='form-control' value='".$RutaApiInicio."'  id='RutaApiLista' name='RutaApiLista' placeholder='Ruta' required>
                </div>

                <div class='form-group'>
                    <label for='txtIdentificador'>Identificador</label>
                    <input type='text' class='form-control' value='".$IdentificadorMaster."'  id='txtIdentificador' name='txtIdentificador' placeholder='Grupo o Establecimiento' required>
                </div>

                <div class='form-group'>
                    <label for='TipoSolicitud'>Tipo</label>
                    <select name='TipoSolicitud' id='TipoSolicitud' class='form-control' required>
                        <option value='E'>Estación</option>
                        <option value='G' selected>Grupo</option>
                    </select>
                </div>

                <div class='form-group'>
                    <label for='Aplicacion'>Aplicación</label>
                    <select name='txtAplicacion' id='txtAplicacion' class='form-control' required>
                        ".$contenidoComboAplicacion."
                    </select>
                </div>

                <button type='submit' class='btn btn-primary'>Enviar</button>";
			ActiveForm::end();
		}
			
		?>
 	</div>
</div>


<script>

function agregarMaster(valor){
	if(valor=="masterBrentec"){
		var sel = document.getElementById("TipoSolicitud"); 
		var existe= false;
		for (var i = 0; i < sel.length; i++) {
			var opt = sel[i];
			if(opt.value == "M"){
				existe= true;
			}
		}
		if(existe == false){
			var x = document.getElementById("TipoSolicitud");
			var option = document.createElement("option");
			option.text = "Master";
			option.value = "M";
			x.add(option);
		}
				
	}
	else{
		var x = document.getElementById("TipoSolicitud");
		x.remove(2);
	}
}
 
</Script>
<script>
var password = document.getElementById("Password")
  , confirm_password = document.getElementById("PasswordVerificar");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Las contraseñas no coinciden");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>
