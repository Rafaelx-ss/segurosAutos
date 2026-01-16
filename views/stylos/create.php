<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-lock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Stylos				<div class="page-title-subheading">Alta de registro</div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 	 				
					echo Html::a('<i class="fa fa-search"></i> Consulta', $url = ['stylos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);
			
					echo Html::a('<i class="fa fa-plus" aria-hidden="true"></i> Alta', $url = ['stylos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);
				?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;">Ingresa los datos solicitados<br><br></span>	
					
					<?php 						
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>
	</div>
</div>