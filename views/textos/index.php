<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-chat icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Textos				<div class="page-title-subheading">Consulta </div>
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
			
			
					echo Html::a('<i class="'.$iSeacrh.'"></i> Consulta', $url = ['textos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta', $url = ['textos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo  Html::button('<i class="'.$iDelete.'" aria-hidden="true"></i> Eliminar', ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'onclick' => 'getRows()']);
			?>
		</div>
     </div>
</div>

<?php
$btnActivecat = '';
$btnActivebtn = '';
$btnActivemsj = '';
$btnActivemnu = '';
if(isset($_GET['tipo'])){
	if($_GET['tipo'] == 'Botones'){
		$btnActivebtn = 'active';
	}elseif($_GET['tipo'] == 'Mensajes'){
		$btnActivemsj = 'active';
	}elseif($_GET['tipo'] == 'Catalogos'){
		$btnActivecat = 'active';
	}elseif($_GET['tipo'] == 'Menus'){
		$btnActivemnu = 'active';
	}else{
		$btnActivecat = 'active';
	}
}else{
	$btnActivecat = 'active';
}
?>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span>Catálogos</span>', $url = ['textos/index&tipo=Catalogos'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' '.$btnActivecat.' mr-3']);
		?>
    </li>
    <li class="nav-item">
		<?php
		echo Html::a('<span>Botones</span>', $url = ['textos/index&tipo=Botones'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' '.$btnActivebtn.' mr-3']);
		?>
    </li>
    <li class="nav-item">
		<?php
		echo Html::a('<span>Mensajes</span>', $url = ['textos/index&tipo=Mensajes'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' '.$btnActivemsj.' mr-3']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span>Menús</span>', $url = ['textos/index&tipo=Menus'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' '.$btnActivemnu.' mr-3']);
		?>
    </li>
</ul>
<div class="main-card mb-3 card">
      <div class="card-body">				
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
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> Limpiar filtro', $url = ['textos/index&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
					'filterModel' => $searchModel,       
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],		  				
						['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
										return ['value' => $data->textoID];
								},
						],
						//'textoID',
						'nombreTexto',
						'activoTexto:boolean',
						//'versionRegistro',
						//'regEstado:boolean',
						//'regFechaUltimaModificacion',
						//'regUsuarioUltimaModificacion',
						//'regFormularioUltimaModificacion',
						//'regVersionUltimaModificacion',

						['class' => 'yii\grid\ActionColumn',
							'options' => ['style' => 'width:50px;'],
									'template' => '{update}',
									'buttons' => [
										'update' => function ($url, $model) {
											$url_group = 'index.php?r=textos/update&id='.$model->textoID;
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
											$url_group = 'javascript:confirmDelete('.$model->textoID.')';
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

						],
					],
				]); ?>
				
									<!-- finaliza el grid -->	
 	</div>
</div>



<script type="text/javascript">

    function confirmDelete(id){
        alertify.confirm('Confirmación', '¿Seguro que desea eliminar el registro?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=textos/delete",
                     data:{id:id},
                     success:function(bool){
						 //console.log('success '+bool);
                        if (bool = true){
                            alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp;&nbsp;Registro eliminado</span>', 2 , function (){location.reload(); }); 
                        }else{
							alertify.error('<span style="color: #FFFFFF;">Ocurrio un error, intenta de nuevo</span>', 2 , function (){location.reload(); }); 
						}
                     },
                     error: function(data){ 
                        // console.log('error '+data);
						alertify.error('<span style="color: #FFFFFF;">Ocurrio un error, intenta de nuevo</span>', 2 , function (){location.reload(); }); 
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
		console.log(list);
		var elementos = list.length
		
		if(elementos == 0){
			alertify
			  .alert('Mensaje: ', "Debes de seleccionar un elemento de la lista.", function(){
				alertify.message('OK');
			});
		}else{
			
			alertify.confirm('Confirmación', '¿Seguro que desea eliminar los registros seleccionados?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=textos/delete",
                     data:{selection:list},
                     success:function(bool){
						 //console.log('success '+bool);
                        if (bool = true){
                            alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp;&nbsp;Registro eliminado</span>', 2 , function (){location.reload(); }); 
                        }else{
							alertify.error('<span style="color: #FFFFFF;">Ocurrio un error, intenta de nuevo</span>', 2 , function (){location.reload(); }); 
						}
                     },
                     error: function(data){ 
                        // console.log('error '+data);
						alertify.error('<span style="color: #FFFFFF;">Ocurrio un error, intenta de nuevo</span>', 2 , function (){location.reload(); }); 
                     },
                });
            },
            function(){
            });
			
		}
		//console.log(elementos);
    }
</script>
