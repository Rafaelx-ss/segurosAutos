<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

use app\assets\AppAsset;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);

$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}

$frmSeguridad = '';
if(isset($_GET['r'])){
	$frmSeguridad = explode("/", $_GET['r']);
}

$perElimckh = false;
$menuBotones = Yii::$app->globals->getActionButton($idForm, 'clientes', 'Clientes', $frmSeguridad, 'index', '1');

$perElimckh = $menuBotones['visible'];
echo $menuBotones['botones'];

if(Yii::$app->globals->getIndexSearch('Clientes') != 0){
	echo $this->render('_searchEliminados', ['model' => $searchModel, 'f'=>$idForm]);
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
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(5, Yii::$app->session['idiomaId'], 'Limpiar filtros'), $url = ['clientes/eliminados&f='.$idForm.'&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
					'filterModel' => $searchModel,       
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],
		  						  				['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
										return ['value' => $data->clienteID];
								}, 'visible' => $perElimckh,
						],
		  						  				
						['attribute' => 'nombreComercial','label' => Yii::$app->globals->getTraductor('365', Yii::$app->session['idiomaId'], 'nombreComercial'),'value' => 'nombreComercial','format'=> 'raw'],
['attribute' => 'clienteRazonSocial','label' => Yii::$app->globals->getTraductor('42', Yii::$app->session['idiomaId'], 'clienteRazonSocial'),'value' => 'clienteRazonSocial','format'=> 'raw'],
['attribute' => 'clienteRFC','label' => Yii::$app->globals->getTraductor('43', Yii::$app->session['idiomaId'], 'clienteRFC'),'value' => 'clienteRFC','format'=> 'raw'],
['attribute' => 'tipoClienteID','label' => Yii::$app->globals->getTraductor('637', Yii::$app->session['idiomaId'], 'tipoClienteID'),'value' => 'idTiposclientes.tipoClienteDescripcion','format'=> 'raw'],
['attribute' => 'metodoPagoID','label' => Yii::$app->globals->getTraductor('555', Yii::$app->session['idiomaId'], 'metodoPagoID'),'value' => 'metodoPagoID','format'=> 'raw'],
['attribute' => 'formaPagoID','label' => Yii::$app->globals->getTraductor('294', Yii::$app->session['idiomaId'], 'formaPagoID'),'value' => 'formaPagoID','format'=> 'raw'],
['attribute' => 'UsoCFDIID','label' => Yii::$app->globals->getTraductor('556', Yii::$app->session['idiomaId'], 'UsoCFDIID'),'value' => 'UsoCFDIID','format'=> 'raw'],
['attribute' => 'regimenFiscalID','label' => Yii::$app->globals->getTraductor('585', Yii::$app->session['idiomaId'], 'regimenFiscalID'),'value' => 'idFeregimenfiscal.nombreRegimenFiscal','format'=> 'raw'],
['attribute' => 'codigoPostalCliente','label' => Yii::$app->globals->getTraductor('56', Yii::$app->session['idiomaId'], 'codigoPostalCliente'),'value' => 'codigoPostalCliente','format'=> 'raw'],
['attribute' => 'clienteTipoPersona','label' => Yii::$app->globals->getTraductor('586', Yii::$app->session['idiomaId'], 'clienteTipoPersona'),'value' => 'clienteTipoPersona','format'=> 'raw'],
['attribute' => 'estadoCliente','label' => Yii::$app->globals->getTraductor('34', Yii::$app->session['idiomaId'], 'estadoCliente'),'value' => 'estadoCliente','filter'=> [0=>'No',1=>'Si'],'format'=> 'boolean'],

						['class' => 'yii\grid\ActionColumn',
							'options' => ['style' => 'width:50px;'],
									'template' => '{update}',
									'buttons' => [
										'update' => function ($url, $model) {
											$url_group = 'index.php?r=clientes/update&f='.$_GET['f'].'&id='.$model->clienteID;
											return Html::a(
												'<img src="'.Yii::$app->request->BaseUrl.'/images/icon-edit.png" width="25px"  alt="editar"/>',
												$url_group, 
												[
													'title' => 'Editar',
													'data-pjax' => '0',
												]
											);
										},
									],

						],
					],
				]); ?>
				
									<!-- finaliza el grid -->	
 	</div>
</div>



<script type="text/javascript">

    function confirmDelete(id){
        alertify.confirm('<?= Yii::$app->globals->getTraductor(17, Yii::$app->session['idiomaId'], 'Confirmación') ?>', '¿<?= Yii::$app->globals->getTraductor(9, Yii::$app->session['idiomaId'], 'Seguro que desea eliminar el registro') ?>?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=clientes/delete",
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
                     type: 'get',
                     url: "index.php?r=clientes/delete",
                     data:{selection:list,f:"<?= $idForm ?>"},
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

