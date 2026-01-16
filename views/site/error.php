<?php
use app\widgets\Alert;
use yii\helpers\Html;
use app\assets\AppAsset;
use app\models\Sitio;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl; 
?>
<div class="site-error">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <div class="alert alert-danger text-center">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
	<img src="<?= $baseUrl ?>/images/page_error.png" alt="error" style="width: 20%;" />
	
	</div>

</div>
<div style="clear: both;"></div><br><br>
