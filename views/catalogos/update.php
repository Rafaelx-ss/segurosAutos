<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Catalogos */
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-server icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Catalogos 
				<div class="page-title-subheading">Editar registro</div>
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
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta', $url = ['catalogos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-edit" aria-hidden="true"></i> Editar', $url = ['catalogos/update&id='.$model->catalogoID], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
					
					echo Html::a('<i class="fa fa-bars" aria-hidden="true"></i> Combos', $url = ['combos/index&token='.$_GET['id']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-bars" aria-hidden="true"></i> Campos', $url = ['campos/index&token='.$_GET['id']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="fa fa-tags" aria-hidden="true"></i> Campos Grid', $url = ['camposgrid/index&token='.$_GET['id']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="pe-7s-refresh" aria-hidden="true"></i> Catálogo', $url = ['catalogos/updatecampos&id='.$model->catalogoID], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn  '.Yii::$app->globals->btnAcciones()]);	
			?>
			<!--<a class="mb-2 mr-2 btn-pill btn-transition btn btn-outline-info" href="#" onclick="confirmDelete()"><i class="fa fa-retweet" aria-hidden="true"></i> Sincronizar tabla</a>-->
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">
					<span style="font-size: 16px;">Ingresa los datos solicitados para editar la información<br><br></span>	
								
					<?php 					
						echo $this->render('_formUpdate', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>


<script type="text/javascript">

    function confirmDelete(){
        alertify.confirm('Confirmación', '¿Seguro que desea actualizar los campos del modelo?', 
            function(){
                $.ajax({
                     type: 'POST',
                     url: "index.php?r=catalogos/campos",
                     data:{id:"<?php echo $model->catalogoID; ?>"},
                     success:function(bool){
						 //console.log('success '+bool);
                        if (bool = true){
                            alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp;&nbsp;Campos actualizados con exito</span>', 2 , function (){location.reload(); }); 
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
	
</script>

