<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
if(!isset($_GET['token'])){
	Yii::$app->response->redirect(['textos/index']);
}


use yii\widgets\ActiveForm;
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-chat icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Traducciones				<div class="page-title-subheading">Consulta</div>
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
			
					echo Html::a('<i class="'.$iSeacrh.'"></i> Consulta texto', $url = ['textos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta texto', $url = ['textos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
				
					echo Html::a('<i class="fa fa-edit" aria-hidden="true"></i> Editar texto', $url = ['textos/update&id='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
				    echo Html::a('<i class="fa fa-comments" aria-hidden="true"></i> Traducciones', $url = ['traducciones/index&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">	
	  <?php
		  if(isset($_GET['update'])){
				echo '<div class="alert alert-success" role="alert">
						 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro actualizado con exito!
					 </div>';
		  }
	  ?>
					<!-- inicia el grid -->	
		  <div class="col-sm-4">
      <?php
		$form = ActiveForm::begin([
			'id' => 'traducciones-form',
			'action' => ['traducciones/updatetraduccion'],
			'options' => ['method' => 'post'],
		]); 
			  
		 $traducciones = Yii::$app->db->createCommand("SELECT TextosIdiomas.*, Idiomas.*, Textos.*  FROM TextosIdiomas inner join Textos on Textos.textoID=TextosIdiomas.textoID inner join Idiomas on Idiomas.idiomaID=TextosIdiomas.idiomaID WHERE TextosIdiomas.textoID ='".$_GET['token']."' and Idiomas.regEstado=1")->queryAll();
			  if(count($traducciones) != 0){
		 
		  
		  foreach($traducciones as $row){
			  echo '<div class="form-group field-textos-nombretexto">
						<label class="control-label" for="textos-nombretexto">'.$row['nombreTexto'].' ('.$row['nombreIdioma'].')</label>
						<input type="text" class="form-control" name="textos_'.$row['textoIdiomaID'].'" value="'.$row['texto'].'">
					</div>';
		  }
	  ?>
			  <input type="hidden" value="<?php echo $_GET['token']; ?>" name="token" />
			  <button type="submit" class="btn <?php echo Yii::$app->globals->btnSave(); ?>"><i class="pe-7s-diskette"></i> &nbsp;Actualizar datos</button>
			  
		<?php
			  }
		ActiveForm::end(); 
		?>
			</div>						<!-- finaliza el grid -->	
 	</div>
</div>
