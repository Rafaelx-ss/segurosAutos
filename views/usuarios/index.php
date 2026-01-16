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
            	<i class="pe-7s-add-user icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Usuarios				<div class="page-title-subheading">Consulta</div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 				
					echo Html::a(' Perfiles', $url = ['perfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);		
					echo Html::a(' Usuarios', $url = ['usuarios/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3 active']);
					echo Html::a(' Perfil Compuesto', $url = ['pcompuestos/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);
					echo Html::a(' Permisos', $url = ['paccion/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);
			
					echo Html::a(' Menus', $url = ['pmenus/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);			
					echo Html::a(' Formularios', $url = ['formulariosperfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);	
			
			?>			
		</div>
     </div>
</div>
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
?>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iSeacrh.'"></i> Consulta</span>', $url = ['usuarios/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones().' active']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iAdd.'" aria-hidden="true"></i> Alta</span>', $url = ['usuarios/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
    <li class="nav-item">
		<?php
		echo  Html::button('<span><i class="'.$iDelete.'" aria-hidden="true"></i> Eliminar</span>',  $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'onclick' => 'getRows()']);
		?>
    </li>
	
	<li class="nav-item">
		<?php
		echo  Html::button('<span><i class="fas fa-file-alt" aria-hidden="true"></i> &nbsp;Carta responsiva </span>',  $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'onclick' => 'getUser()']);
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
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> Limpiar filtro', $url = ['usuarios/index&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
					'filterModel' => $searchModel,       
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],
		  				['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
						 		return ['value' => $data->usuarioID];
						 	},
						],
						//'usuarioID',
						'nombreUsuario',
						//'passw',
						'usuario',
						//'activoUsuario',
						'correoUsuario',
						//'codigoRecuperacionPassw',
						//'fechaGeneracionCodigoRecuperacionPassw',
						'intentosValidos',
						//'versionRegistro',
						//'regEstado',
						//'regFechaUltimaModificacion',
						//'regUsuarioUltimaModificacion',
						//'regFormularioUltimaModificacion',
						//'regVersionUltimaModificacion',
						//'AuthKey',

						['class' => 'yii\grid\ActionColumn',
							'options' => ['style' => 'width:50px;'],
									'template' => '{update}',
									'buttons' => [
										'update' => function ($url, $model) {
											$url_group = 'index.php?r=usuarios/update&id='.$model->usuarioID;
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
											$url_group = 'javascript:confirmDelete('.$model->usuarioID.')';
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
                     url: "index.php?r=usuarios/delete",
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
                     url: "index.php?r=usuarios/delete",
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
	
	
	function getUser(){
        var user_id; 
         var list = []; 
        $('input[name="selection[]"]:checked').each(function(){
            user_id = this.value;
             list.push(user_id); 
        });
		
		var elementos = list.length
		
		if(elementos == 1){		
			//console.log(list[0]);	
			var url = 'index.php?r=usuarios/cartaresponsiva&id=' + list[0];
			//window.open(url , '_blank');
			window.open(url);
		}else{
			
			alertify
			  .alert('Mensaje: ', "Debes de seleccionar un usuario de la lista.", function(){
				alertify.message('OK');
			});
			
		}
		//console.log(elementos);
    }
</script>
