<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Exportarcatalogos */


$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}


$frmSeguridad = '';
if(isset($_GET['r'])){$frmSeguridad = explode("/", $_GET['r']);}


$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.icono,  Formularios.textoID, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
where md5(Formularios.formularioID)='".$idForm."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".Yii::$app->user->identity->usuarioID."' group by AccionesFormularios.accionFormularioID")->queryAll();

$perCons = 0;
$perAlta = 0;
$perElim = 0;
$txtID = 1;
$iconForm = 'pe-7s-lock';


foreach($permisosBtn as $dataPbtn){
	$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);	
	if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){		
		if($frmSeguridad[0] == $urlSeguridad[0]){			
			if(isset($dataPbtn['accionID'])){	
					if($dataPbtn['accionID'] == '2'){ $perCons = 1; }	
					if($dataPbtn['accionID'] == '3'){ $perAlta = 1; }	
					if($dataPbtn['accionID'] == '4'){ $perElim = 1; }
					$txtID = $dataPbtn['textoID'];
					$iconForm = $dataPbtn['icono'];
				}
			}
	}						
}
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-lock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				<?=  Yii::$app->globals->getTraductor($txtID, Yii::$app->session['idiomaId'], 'Exportarcatalogos'); ?>	 
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(4, Yii::$app->session['idiomaId'], 'Editar Registro') ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 		
			$iconAcciones = Yii::$app->db->createCommand("SELECT * FROM Acciones where regEstado='1'")->queryAll();
			$iAdd = "fa fa-plus";
			$iSeacrh = "fa fa-search";
			$iDelete = "fa fa-trash";
			$iExcel = "fa fa-file-excel";
			$iPdf = "fa fa-file-pdf";
			
			foreach($iconAcciones as $rAccion){
				if($rAccion['accionID'] == '2'){
					$iSeacrh = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '3'){
					$iAdd = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '4'){
					$iDelete = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '6'){
					$iExcel = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '7'){
					$iPdf = $rAccion['imagen'];
				}
			}
			
					//Consulta
					if($perCons == 1){
						echo Html::a('<i class="'.$iSeacrh.'"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = ['exportarcatalogos/index&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					//Alta
					if($perAlta == 1){
						echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = ['exportarcatalogos/create&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);			
					}
					//Eliminar
					if($perElim == 1){
						echo Html::a('<i class="'.$iDelete.'" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = ['exportarcatalogos/deletedata&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;"><?= Yii::$app->globals->getTraductor(7, Yii::$app->session['idiomaId'], 'Ingresa los datos solicitados para editar la información') ?><br><br></span>	
								
					<?php 					
						echo $this->render('_formUpdate', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>

