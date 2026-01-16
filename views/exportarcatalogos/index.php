<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;


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
$perEdit = false;
$perElimckh = false;
$perExcel = 0;
$perPdf = 0;
$txtID = 1;
$iconForm = 'pe-7s-lock';

foreach($permisosBtn as $dataPbtn){
	$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);	
	if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){		
		if($frmSeguridad[0] == $urlSeguridad[0]){			
			if(isset($dataPbtn['accionID'])){	
					if($dataPbtn['accionID'] == '2'){ $perCons = 1; }	
					if($dataPbtn['accionID'] == '3'){ $perAlta = 1; }	
					if($dataPbtn['accionID'] == '4'){ $perElim = 1; $perElimckh = true; }
					if($dataPbtn['accionID'] == '5'){ $perEdit = true; }
					if($dataPbtn['accionID'] == '6'){ $perExcel = 1; }
					if($dataPbtn['accionID'] == '7'){ $perPdf = 1; }
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
            	<i class="<?=  $iconForm ?> icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				<?=  Yii::$app->globals->getTraductor($txtID, Yii::$app->session['idiomaId'], 'Exportarcatalogos'); ?>				
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta') ?></div>
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
						echo Html::a('<i class="'.$iSeacrh.'"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = ['exportarcatalogos/index&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					//Alta
					if($perAlta == 1){
						echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = ['exportarcatalogos/create&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					//Eliminar
					if($perElim == 1){
						echo  Html::button('<i class="'.$iDelete.'" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'onclick' => 'getRows()']);
					}
			
					if($perExcel == 1){
						echo Html::a('<i class="'.$iExcel.'"></i> '.Yii::$app->globals->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = ['exportarcatalogos/xportexcel&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					
					if($perPdf == 1){
						echo Html::a('<i class="'.$iPdf.'"></i> '.Yii::$app->globals->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = ['exportarcatalogos/xportpdf&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'target'=>'_blank']);
					}
				
			?>
		</div>
     </div>
</div>

<?php 
$catalogoIndex = Yii::$app->db->createCommand("SELECT * FROM Catalogos where regEstado='1' and nombreCatalogo='ExportarCatalogos'")->queryOne();
$arrayGrid = array();
$showSearch = 0;

if(isset($catalogoIndex['catalogoID'])){	
	$camposIndex = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where regEstado='1' and catalogoID='".$catalogoIndex['catalogoID']."' order by orden ASC")->queryAll();	
	
	
	$arrayGrid = array(['class' => 'yii\grid\SerialColumn'],		  				
						['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
										return ['value' => $data->exportarCatalogosID];
								}, 'visible' => $perElimckh,
						]);
	foreach($camposIndex as $indexCampos){
		if($indexCampos['searchVisible'] == 1){
			$showSearch++;
		}
		
		if($indexCampos['visible'] == 1){	
			
			if($indexCampos['tipoControl'] == 'checkbox'){
				$arrayGrid[] = ['attribute'=>$indexCampos['nombreCampo'],
				'label' => Yii::$app->globals->getTraductor($indexCampos['textoID'], Yii::$app->session['idiomaId'], $indexCampos['nombreCampo']),
				'filter' => [0=>'No',1=>'Si'],
				'format' => 'boolean'];
			}elseif($indexCampos['tipoControl'] == 'select'){
				$catalogoIndexRef = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$indexCampos['catalogoReferenciaID']."'")->queryOne();
				
				$arrayGrid[] = ['attribute'=>$indexCampos['nombreCampo'],
				'label' => Yii::$app->globals->getTraductor($indexCampos['textoID'], Yii::$app->session['idiomaId'], $indexCampos['nombreCampo']),
				'value'=> 'id'.$catalogoIndexRef['nombreModelo'].'.'.$indexCampos['textField'],
				'format' => 'raw'];
			}else{
				$arrayGrid[] = ['attribute'=>$indexCampos['nombreCampo'],
				'label' => Yii::$app->globals->getTraductor($indexCampos['textoID'], Yii::$app->session['idiomaId'], $indexCampos['nombreCampo']),
				'value'=> $indexCampos['nombreCampo'],
				'format' => 'raw'];
			}
		}
	}
	
	
	$arrayGrid[] = ['class' => 'yii\grid\ActionColumn',
							'options' => ['style' => 'width:50px;'],
									'visible' =>$perEdit,
									'template' => '{update}',
									'buttons' => [
										'update' => function ($url, $model) {
											$url_group = 'index.php?r=exportarcatalogos/update&f='.$_GET['f'].'&id='.$model->exportarCatalogosID;
											return Html::a(
												'<img src="'.Yii::$app->request->BaseUrl.'/images/icon-edit.png" width="25px"  alt="editar"/>',
												$url_group, 
												[
													'title' => 'Editar',
													'data-pjax' => '0',
												]
											);
										},

										'delete' => function ($url, $model) {
											$url_group = 'javascript:confirmDelete('.$model->exportarCatalogosID.')';
											return Html::a(
												'<img src="'.Yii::$app->request->BaseUrl.'/images/icon-del1.png" width="25px"  alt="eliminar"/>',
												$url_group, 
												[
													'title' => 'Eliminar',
													'data-pjax' => '1',
												]
											);
										},
									],

						];
}

if($showSearch != 0){
	echo $this->render('_search', ['model' => $searchModel, 'f'=>$_GET['f']]);
}
?>

<div class="main-card mb-3 card">
      <div class="card-body">	
	<?php 	if(isset($_GET['delete'])){
		if($_GET['delete'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(11, Yii::$app->session['idiomaId'], 'Registro eliminado con exito').'!
				 </div>';
		}else{
			echo '<div class="alert alert-danger" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(12, Yii::$app->session['idiomaId'], 'Ocurrio un error al tratar de eliminar el registro').'!
				 </div>';
		}
	}
	?>
					<!-- inicia el grid -->
		               							<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'options' => ['class' => 'grid-view table-responsive'],
					'pager' => [
						'firstPageLabel' => 'Primero',
						'lastPageLabel'  => 'Ultimo',
						'activePageCssClass' => 'paginate_button page-item active',
						'linkOptions' => ['class' => 'page-link'],
						'options' => ['class' => 'pagination', 'style'=>'margin-top:0px;'],
					],
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(5, Yii::$app->session['idiomaId'], 'Limpiar filtros'), $url = ['exportarcatalogos/index&f='.$_GET['f'].'&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
					'filterModel' => $searchModel,       
					'columns' => $arrayGrid,				]); ?>
				
									<!-- finaliza el grid -->	
 	</div>
</div>



<script type="text/javascript">

    function confirmDelete(id){
        alertify.confirm('<?= Yii::$app->globals->getTraductor(17, Yii::$app->session['idiomaId'], 'Confirmación') ?>', '¿<?= Yii::$app->globals->getTraductor(9, Yii::$app->session['idiomaId'], 'Seguro que desea eliminar el registro') ?>?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=exportarcatalogos/delete",
                     data:{id:id},
                     success:function(bool){
						 //console.log('success '+bool);
                        if (bool = true){
                            alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp;&nbsp;Registro eliminado</span>', 2 , function (){location.reload(); }); 
                        }else{
							alertify.error('<span style="color: #FFFFFF;"><?= Yii::$app->globals->getTraductor(13, Yii::$app->session['idiomaId'], 'Ocurrio un error, intenta de nuevo') ?></span>', 2 , function (){location.reload(); }); 
						}
                     },
                     error: function(data){ 
                        // console.log('error '+data);
						alertify.error('<span style="color: #FFFFFF;"><?= Yii::$app->globals->getTraductor(13, Yii::$app->session['idiomaId'], 'Ocurrio un error, intenta de nuevo') ?></span>', 2 , function (){location.reload(); }); 
                     },
                });
            },
            function(){
            });
    }
	
	
	function getRows(){
        var user_id; 
         var list = []; 
        $('input[name="selection[]"]:checked').each(function(){
            user_id = this.value;
             list.push(user_id); 
        });
		
		var elementos = list.length
		
		if(elementos == 0){
			alertify
			  .alert('<?= Yii::$app->globals->getTraductor(16, Yii::$app->session['idiomaId'], 'Mensaje') ?>: ', "<?= Yii::$app->globals->getTraductor(10, Yii::$app->session['idiomaId'], 'Debes de seleccionar un elemento de la lista.') ?>", function(){
				//alertify.message('OK');
			});
		}else{
			
			alertify.confirm('<?= Yii::$app->globals->getTraductor(17, Yii::$app->session['idiomaId'], 'Confirmación') ?>', '¿<?= Yii::$app->globals->getTraductor(9, Yii::$app->session['idiomaId'], 'Seguro que desea eliminar los registros seleccionados') ?>?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=exportarcatalogos/delete",
                     data:{selection:list},
                     success:function(bool){
						 //console.log('success '+bool);
                        if (bool = true){
                            alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp;&nbsp;<?= Yii::$app->globals->getTraductor(11, Yii::$app->session['idiomaId'], 'Registro eliminado con exito') ?></span>', 2 , function (){location.reload(); }); 
                        }else{
							alertify.error('<span style="color: #FFFFFF;"><?= Yii::$app->globals->getTraductor(13, Yii::$app->session['idiomaId'], 'Ocurrio un error, intenta de nuevo') ?></span>', 2 , function (){location.reload(); }); 
						}
                     },
                     error: function(data){ 
                        // console.log('error '+data);
						alertify.error('<span style="color: #FFFFFF;"><?= Yii::$app->globals->getTraductor(13, Yii::$app->session['idiomaId'], 'Ocurrio un error, intenta de nuevo') ?></span>', 2 , function (){location.reload(); }); 
                     },
                });
            },
            function(){
            });
			
		}
		//console.log(elementos);
    }
</script>
