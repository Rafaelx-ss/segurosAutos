<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ExportarCatalogos */


$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}
$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario FROM Acciones
inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
where md5(Formularios.formularioID)='".$idForm."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".Yii::$app->user->identity->usuarioID."' group by AccionesFormularios.accionFormularioID")->queryAll();

$perCons = 0;
$perAlta = 0;
$perElim = 0;

foreach($permisosBtn as $dataPbtn){
	if($dataPbtn['accionID'] == '2'){ $perCons = 1; }	
	if($dataPbtn['accionID'] == '3'){ $perAlta = 1; }	
	if($dataPbtn['accionID'] == '4'){ $perElim = 1; }
}
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-lock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Exportar-catalogos 
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(4, Yii::$app->session['idiomaId'], 'Editar Registro') ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 		
			
					//Consulta
					if($perCons == 1){
						echo Html::a('<i class="fa fa-search"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = ['exportar-catalogos/index&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);
					}
					//Alta
					if($perAlta == 1){
						echo Html::a('<i class="fa fa-plus" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = ['exportar-catalogos/create&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);			
					}
					//Eliminar
					if($perElim == 1){
						echo Html::a('<i class="fa fa-trash" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = ['exportar-catalogos/deletedata&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);
					}
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;"><?= Yii::$app->globals->getTraductor(7, Yii::$app->session['idiomaId'], 'Ingresa los datos solicitados para editar la información') ?><br><br></span>	
								
					<?php 					
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>

