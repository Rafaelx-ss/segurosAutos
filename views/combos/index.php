<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;


$catalogo = Yii::$app->db->createCommand('SELECT * FROM Catalogos where catalogoID="'.$_GET['token'].'"')->queryOne();
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-lock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Combos	<div class="page-title-subheading">Consulta - <?php echo $catalogo['nombreCatalogo']; ?></div>
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
			
					echo Html::a('<i class="'.$iSeacrh.'"></i> Consulta', $url = ['catalogos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta', $url = ['catalogos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-edit" aria-hidden="true"></i> Editar', $url = ['catalogos/update&id='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					
					echo Html::a('<i class="fa fa-bars" aria-hidden="true"></i> Combos', $url = ['combos/index&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-bars" aria-hidden="true"></i> Campos', $url = ['campos/index&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-tags" aria-hidden="true"></i> Campos Grid', $url = ['camposgrid/index&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			?>
		</div>
     </div>
</div>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="fa fa-search"></i> Consulta</span>', $url = ['combos/index&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones().' active']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="fa fa-plus" aria-hidden="true"></i> Alta</span>', $url = ['combos/create&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones().'']);
		?>
    </li>
</ul>
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
					'summary' => Html::a('<i class="fa fa-minus-square" aria-hidden="true"></i> Limpiar filtro', $url = ['combos/index&clear=true'], $options = ['style'=>'color:#003e59;']).' | Viendo {count} de {totalCount} resultados.', 
					'filterModel' => $searchModel,       
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],		  				
						
							//'comboAnidadoID',
							//'catalogoID',
							//'campoIDPadre',
							//'campoIDdependiente',
							[
								'attribute'=>'campoIDPadre',
								'value'=> 'idPadre.nombreCampo',
								'format' => 'raw',						
							],
							[
								'attribute'=>'campoIDdependiente',
								'value'=> 'idDependiente.nombreCampo',
								'format' => 'raw',						
							],
							//'controlQuery:ntext',
							'queryValue',
							'queryText',
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
											$url_group = 'index.php?r=combos/update&token='.$_GET['token'].'&id='.$model->comboAnidadoID;
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
											$url_group = 'javascript:confirmDelete('.$model->comboAnidadoID.')';
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

