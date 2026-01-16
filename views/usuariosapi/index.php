<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

//usar modelo

$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}

$frmSeguridad = '';
if(isset($_GET['r'])){
	$frmSeguridad = explode("/", $_GET['r']);
}


$menuBotones = Yii::$app->globals->getActionButton($idForm, 'usuariosapi', 'Usuariosapi', $frmSeguridad, 'index', '1');
$perElimckh = $menuBotones['visible'];
$perEdit = $menuBotones['perEdit'];
echo $menuBotones['botones'];

$catalogoIndex = Yii::$app->globals->getCatalogo('UsuariosAPI');
$arrayGrid = array();
$showSearch = 0;

if(isset($catalogoIndex['catalogoID'])){	
	$camposIndex = Yii::$app->globals->getCamposGrid($catalogoIndex['catalogoID']);	
	
	$arrayGrid = array(['class' => 'yii\grid\SerialColumn'],		  				
						['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
										return ['value' => $data->usuarioApiID];
								}, 
						 		'visible' => $perElimckh,
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
				$catalogoIndexRef = Yii::$app->globals->getCatalogo($indexCampos['catalogoReferenciaID']); 				
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
											$url_group = 'index.php?r=usuariosapi/update&f='.$_GET['f'].'&id='.$model->usuarioApiID;
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
											$url_group = 'javascript:confirmDelete('.$model->usuarioApiID.')';
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
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(5, Yii::$app->session['idiomaId'], 'Limpiar filtros'), $url = ['usuariosapi/index&f='.$_GET['f'].'&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
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
                     url: "index.php?r=usuariosapi/delete",
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
                     url: "index.php?r=usuariosapi/delete",
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
