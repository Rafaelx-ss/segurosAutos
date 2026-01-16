<?php
use yii\helpers\Html;
$activeEdit="";
$activeAlmacen="";
$activePrecio="";
$activeIva="";
$activeSerie="";
$activePaquete="";
$prID= $_GET["id"]??0;
if (isset($_GET["pr"])) {
    $prID= $_GET["pr"]??0;
}
if (isset($_GET["r"])) {
    $arrOption = explode("/", $_GET["r"]);
    switch ($arrOption[0]) {
        case "productosinventarioalmacenes":
            $activeAlmacen= "active";
        break;
        default:
            $activeEdit="active";
        break;
    }
}
?>
<!--<div class="clsTab d-flex fz-4">
  <div class="px-4 py-1 <?=$activeEdit?>"><a href="index.php?r=productos/update&f=<?=md5(136)?>&id=<?=$prID?>">Editar producto</a></div>
  <div class="px-4 py-1 <?=$activeAlmacen?>""><a href="index.php?r=productosinventarioalmacenes/index&f=<?=md5(136)?>&id=<?=$prID?>">Almacenes</a></div>
  <div class="px-4 py-1 <?=$activePrecio?>""><a href="javascript:;">Precios</a></div>
  <div class="px-4 py-1 <?=$activeIva?>""><a href="javascript:;">Impuestos</a></div>
  <div class="px-4 py-1 <?=$activeSerie?>""><a href="javascript:;">Series</a></div>
  <div class="px-4 py-1 <?=$activePaquete?>""><a href="javascript:;">Paquetes</a></div>
</div>-->

<?php
$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='".$idForm."' and regEstado=1 and estadoFormulario=1")->queryOne();
$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='".$formMenu['formID']."' and regEstado=1 and estadoFormulario=1")->queryAll();
?>
<div class="clsTab d-flex fz-4">
<?php
foreach($formSubmenus as $rsubMenu){
	$active="";
	if(md5($rsubMenu['formularioID'])==$idForm) {
		$active="active";
	}
	echo '<div class="px-4 py-1 '.$active.'">' . Html::a('<i class="'.$rsubMenu['icono'].'"></i> '.Yii::$app->globals->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'].'&f='.md5($rsubMenu['formularioID'])]);
	echo "</div>";
}	
?>
</div>