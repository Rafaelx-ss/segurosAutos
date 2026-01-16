<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Stylos */
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-paint-bucket icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Estilo 
				<div class="page-title-subheading">Editar registro</div>
            </div>
        </div>
        <div class="page-title-actions">
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;">Ingresa los datos solicitados para editar la información<br><br></span>	
								
					<?php 					
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>

