<?php

use app\widgets\Alert;
use yii\helpers\Html;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl; 

$this->beginPage(); 
$configData = Yii::$app->db->createCommand("SELECT logoLogin, favIcon, titlePagina, footerPagina, logoFooter FROM ConfiguracionesSistema where configuracionesSistemaID='1'")->queryOne();

$db = Yii::$app->getDb();
$dbName = getDsnAttribute('dbname', $db->dsn);
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    
    <!-- Favicon -->
   <link rel="icon" type="image/png" href="../web/logos/<?= trim($configData['favIcon']) ?>" />
	<?php $this->registerCsrfMetaTags() ?>
    <title><?= $configData['titlePagina'] ?> :: Login</title>
	<!-- plugins css -->
    <?php $this->head() ?>    
</head>
<body>
	<?php $this->beginBody() ?>
	
	<div class="app-container app-theme-white body-tabs-shadow">
        <div class="app-container">
            <div class="h-100">
                <div class="h-100 no-gutters row">
                    <div class="d-none d-lg-block col-lg-4">
                        <div class="slider-light">
							<?php
							$slider = Yii::$app->db->createCommand("SELECT * FROM ConfiguracionesSlider order by ordenSlider ASC")->queryAll();
		 
		 
		 					
							?>
                            <div class="slick-slider">
								<?php
								foreach($slider as $rslide){
									echo '<div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark" tabindex="-1">
                                        <div class="slide-img-bg" style="background-image: url(\'../slider/'.$rslide['imagenSlider'].'\');"></div>
                                        <div class="slider-content"><h3>'.$rslide['tituloSlider'].'</h3>
                                            <p>'.$rslide['contenidoSlider'].'</p></div>
                                    	</div>
                                	</div>';
								}
								?>                               
                            </div>
                        </div>
                    </div>
                    <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
                        <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                            <div>
								<img class="img-fluid inline-block" src="../web/logos/<?= $configData['logoLogin']; ?>" alt="Logo">
							</div>
                            <h4 class="mb-0">
                                <br>
                                <span>Ingresa los datos para ingresar a tu cuenta.</span></h4>
                            <div class="divider row"></div>
                            <div>
								<?php
								$dato = Yii::$app->db->createCommand("SHOW COLUMNS FROM Usuarios")->queryAll();
								$e1 = 0;
								$e2 = 0;
								foreach($dato as $row){
									if($row['Field'] == 'cambioPass'){
										$e1 = 1;
									}
									if($row['Field'] == 'fechaActualizaPass'){
										$e2 = 1;
									}
								}

								if($e1 == 0){
									Yii::$app->db->createCommand("ALTER TABLE Usuarios ADD COLUMN cambioPass BIT(1) NULL DEFAULT 1 AFTER AuthKey")->query();
								}

								if($e2 == 0){
									Yii::$app->db->createCommand("ALTER TABLE Usuarios ADD COLUMN fechaActualizaPass DATE NULL AFTER cambioPass")->query();
								}
								?>
                                 <?= $content; ?>
                            </div>							
							<div class="text-center" style="margin-top: 10%;">
								<img class="img-fluid inline-block" src="../web/logos/<?= $configData['logoFooter']; ?>" alt="LogoFooter">
								<br>
								&copy; <?php  
								$vfile = Yii::$app->basePath."/version";
								$vtxt = "";
								if(file_exists($vfile)){
									$vdata = file($vfile);
									if(isset($vdata[0])){
										$vtxt = $vdata[0];
									}

								}
								// echo date('Y').",  Brentec V. ".$vtxt;
								echo date('Y').", ".$configData['footerPagina']."".$vtxt; 
								
								?>								
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
	
	<script src="<?= $baseUrl; ?>/require/js/adicionales/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="></script>
	<script src="<?= $baseUrl; ?>/require/js/bootstrap.bundle.min.js"></script>
	<script src="<?= $baseUrl; ?>/require/js/adicionales/metismenu.js"></script>
	<script src="<?= $baseUrl; ?>/require/js/scripts-init/app.js"></script>
	<script src="<?= $baseUrl; ?>/require/js/scripts-init/demo.js"></script>
	
		<!--Slick Carousel -->
	<script src="<?= $baseUrl; ?>/require/js/vendors/carousel-slider.js"></script>
	<script src="<?= $baseUrl; ?>/require/js/scripts-init/carousel-slider.js"></script>

	

	<!--TABLES -->
	<!--DataTables-->
	<script src="<?= $baseUrl; ?>/require/js/adicionales/jquery.dataTables.min.js" crossorigin="anonymous"></script>
	<script src="<?= $baseUrl; ?>/require/js/adicionales/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
	<script src="<?= $baseUrl; ?>/require/js/adicionales/dataTables.responsive.min.js" crossorigin="anonymous"></script>
	<script src="<?= $baseUrl; ?>/require/js/adicionales/responsive.bootstrap.min.js" crossorigin="anonymous"></script>

	<!--Bootstrap Tables-->
	<script src="<?= $baseUrl; ?>/require/js/vendors/tables.js"></script>

	<!--Tables Init-->
	<script src="<?= $baseUrl; ?>/require/js/scripts-init/tables.js"></script>
	<?php $this->endBody() ?>
</body>
</html>
<?php 
	$this->endPage();

function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
?>

