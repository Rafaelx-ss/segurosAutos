<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

use app\widgets\Alert;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;



$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

use  yii\web\Session;

$session = Yii::$app->session;


$db  = "";
$dbName = "";
$menuGeneral = "";

//Yii::$app->user->authTimeout = Yii::$app->globals->getTimeSession();

//Yii::$app->user->logout();
if (isset(Yii::$app->user->identity->nombreUsuario)) {
    if (is_null(Yii::$app->user->identity->nombreUsuario) or empty(Yii::$app->user->identity->nombreUsuario)) {
        Yii::$app->response->redirect(['site/timeout']);
    }

    $db = Yii::$app->getDb();
    $dbName = getDsnAttribute('dbname', $db->dsn);


    if (!isset(Yii::$app->session['token_id' . $dbName])) {
        Yii::$app->response->redirect(['site/timeout']);
    } else {
        if (Yii::$app->session['token_id' . $dbName] == '') {
            Yii::$app->response->redirect(['site/timeout']);
        }
    }

    if (!isset(Yii::$app->session['idiomaId'])) {
        Yii::$app->session->set('idiomaId', 1);
        Yii::$app->session->set('idiomaFlag', 'MX');
    }



    if (isset(Yii::$app->session['getMenu_' . Yii::$app->user->identity->usuarioID])) {
        $menuGeneral = Yii::$app->session['getMenu_' . Yii::$app->user->identity->usuarioID];
    } else {
        Yii::$app->session->set('getMenu_' . Yii::$app->user->identity->usuarioID, Yii::$app->globals->getMenu(Yii::$app->user->identity->usuarioID));
        $menuGeneral = Yii::$app->session['getMenu_' . Yii::$app->user->identity->usuarioID];
    }
?>
    <?php $this->beginPage() ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="iso-8859-1">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="<?= $baseUrl; ?>/logos/<?php echo $session['favIcon' . $dbName]; ?>" />
        <?php $this->registerCsrfMetaTags() ?>
        <title><?php echo $session['titlePagina' . $dbName]; ?> :: <?= Yii::$app->user->identity->nombreUsuario; ?></title>
        <link rel="stylesheet" href="<?= $baseUrl; ?>/require/css/fontlinear.css" />
        <?php $this->head() ?>
        <link rel="stylesheet" href="<?= $baseUrl; ?>/require/css/alertif.min.css" />


    </head>

    <body>
        <?php $this->beginBody() ?>
        <div
            class="app-container <?php echo $session['temaContenido' . $dbName]; ?> body-tabs-shadow fixed-header fixed-sidebar">
            <!--Header START-->
            <div class="app-header header-shadow <?php echo $session['temaBanner' . $dbName]; ?> header-text-light">
                <div class="app-header__logo">
                    <div style="padding: 10px;">
                        <img class="inline-block img-fluid"
                            src="<?= $baseUrl; ?>/logos/<?php echo $session['logoBanner' . $dbName]; ?>" alt="Logo">
                    </div>
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button"
                            class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>
                <div class="app-header__content">
                    <div class="app-header-left">
                        <div class="search-wrapper">
                        </div>
                    </div>
                    <div class="app-header-right">
                        <div class="header-dots">
                            <div class="dropdown">
                                <button type="button" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                                    <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                                        <span class="icon-wrapper-bg bg-focus"></span>
                                        <span
                                            class="language-icon opacity-8 flag large <?php echo Yii::$app->session['idiomaFlag']; ?>"></span>
                                    </span>
                                </button>
                                <div tabindex="-1" role="menu" aria-hidden="true"
                                    class="rm-pointers dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-menu-header">
                                        <div class="dropdown-menu-header-inner pt-4 pb-4 bg-focus">
                                            <div class="menu-header-image opacity-05"
                                                style="background-image: url('<?= $baseUrl; ?>/require/images/dropdown-header/city2.jpg');">
                                            </div>
                                            <div class="menu-header-content text-center text-white">
                                                <h6 class="menu-header-subtitle mt-0">
                                                    Cambiar lenguaje
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    //Yii::$app->db->createCommand('SET SESSION wait_timeout = 288000000;')->execute();
                                    try {
                                        $idiomas = $rindicador = Yii::$app->db->createCommand('SELECT * FROM Idiomas where activoIdioma = 1')->queryAll();
                                        foreach ($idiomas as $ridioma) {
                                            echo '<button type="button" tabindex="0" class="dropdown-item" onclick="changeIdioma(\'' . $ridioma['idiomaID'] . '\', \'' . $ridioma['iconIdioma'] . '\')">
											<span class="mr-3 opacity-8 flag large ' . $ridioma['iconIdioma'] . '"></span>
											' . $ridioma['nombreIdioma'] . '
										</button>';
                                        }
                                    } catch (Exception $e) {
                                        Yii::$app->db->close();
                                        Yii::$app->db->open();
                                        //Yii::$app->response->redirect(['site/index']);
                                    }


                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="header-btn-lg pr-0">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left">
                                        <div class="btn-group">
                                            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                class="p-0 btn">
                                                <img width="42" class="rounded-circle"
                                                    src="<?= $baseUrl; ?>/logos/<?php echo $session['icoBanner' . $dbName]; ?>"
                                                    alt="">
                                                <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                            </a>
                                            <div tabindex="-1" role="menu" aria-hidden="true"
                                                class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-menu-header">
                                                    <div
                                                        class="dropdown-menu-header-inner <?php echo $session['temaBanner' . $dbName]; ?>">
                                                        <div class="menu-header-image opacity-2"
                                                            style="background-image: url('<?= $baseUrl; ?>/assets/images/dropdown-header/city3.jpg');">
                                                        </div>
                                                        <div class="menu-header-content text-left">
                                                            <div class="widget-content p-0">
                                                                <div class="widget-content-wrapper">
                                                                    <div class="widget-content-left mr-3">
                                                                        <img width="42" class="rounded-circle"
                                                                            src="../logos/<?php echo $session['icoBanner' . $dbName]; ?>"
                                                                            alt="">
                                                                    </div>
                                                                    <div class="widget-content-left">
                                                                        <div class="widget-heading"> Bienvenido
                                                                        </div>
                                                                        <div class="widget-subheading opacity-8">
                                                                            <?= Yii::$app->user->identity->nombreUsuario; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="widget-content-right mr-2">

                                                                        <?php
                                                                        echo Html::beginForm(['/site/logout'], 'post')
                                                                            . Html::submitButton(
                                                                                '<i class="ti-power-off pdd-right-10"></i> Salir',
                                                                                ['class' => 'btn-pill btn-shadow btn-shine btn btn-focus']
                                                                            )
                                                                            . Html::endForm();

                                                                        ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid-menu grid-menu-2col">
                                                    <div class="no-gutters row">
                                                        <div class="col-sm-12">
                                                            <div class="no-gutters row">
                                                                <div class="col-sm-12" style="border-right-width:0px;">
                                                                    <h5>Perfile(s) del usuario</h5>
                                                                    <?php
                                                                    try {
                                                                        $rwPerfil = Yii::$app->db->createCommand('SELECT nombrePerfil FROM PerfilesCompuestos inner join Perfiles on Perfiles.perfilID = PerfilesCompuestos.perfilID where usuarioID = "' . Yii::$app->user->identity->usuarioID . '" and Perfiles.regEstado=1 and PerfilesCompuestos.regEstado=1')->queryAll();
                                                                        foreach ($rwPerfil as $row) {
                                                                            echo "- " . $row['nombrePerfil'] . "<br>";
                                                                        }
                                                                    } catch (Exception $e) {
                                                                        echo "No disponible";
                                                                    }

                                                                    ?>

                                                                </div>
                                                                <div class="col-sm-6" style="border-right-width:0px;">
                                                                    <button
                                                                        class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn <?php echo $session['temaBtnAccion']; ?>">
                                                                        <i
                                                                            class="pe-7s-headphones icon-gradient bg-amy-crisp btn-icon-wrapper mb-2"></i>
                                                                        <strong>Soporte:</strong>
                                                                    </button>
                                                                </div>
                                                                <div class="col-sm-6" style="border-right-width:0px;">
                                                                    <button
                                                                        class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn <?php echo $session['temaBtnAccion']; ?>"
                                                                        data-toggle="modal" data-target="#modalversionApp">
                                                                        <i
                                                                            class="pe-7s-ticket icon-gradient bg-amy-crisp btn-icon-wrapper mb-2"></i>
                                                                        <strong>Acerca De:</strong>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-content-left  ml-3 header-user-info">
                                        <div class="widget-heading">
                                            Bienvenido
                                        </div>
                                        <div class="widget-subheading">
                                            <?= Yii::$app->user->identity->nombreUsuario; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Header END-->



            <div class="app-main">
                <div class="app-sidebar sidebar-shadow  <?php echo $session['temaMenu' . $dbName]; ?>  sidebar-text-light">
                    <div class="app-header__logo">
                        <div class="logo-src"></div>
                        <div class="header__pane ml-auto">
                            <div>
                                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                    data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="app-header__mobile-menu">
                        <div>
                            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">

                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="app-header__menu">
                        <span>
                            <button type="button"
                                class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
                    </div>
                    <div class="scrollbar-sidebar">
                        <div class="app-sidebar__inner">
                            <ul class="vertical-nav-menu">
                                <?php
                                //PermisosMenus
                                echo $menuGeneral;
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="app-main__outer">
                    <div class="app-main__inner">

                        <?= $content ?>
                    </div>
                    <div class="scrollbar-container"></div>
                    <div class="app-wrapper-footer" style=" background: #FFFFFF;">
                        <div class="app-footer">
                            <div style="float: left; width: 300px; padding-top: 10px; padding-left:10px;">
                                <img class="inline-block img-fluid"
                                    src="<?= $baseUrl; ?>/logos/<?php echo $session['logoFooter' . $dbName]; ?>" alt="Logo">
                            </div>

                            <div style="text-align: right; padding: 20px; background: #FFFFFF;">
                                &copy; <?php echo date('Y') . ", " . $session['footerPagina' . $dbName]; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal versiones -->
        <div class="modal fade" id="modalversionApp" tabindex="-1" role="dialog"
            aria-labelledby="modalversionAppCenterTitle" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLongTitleVersion">Acerca De : </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        &copy; <?php echo date('Y'); ?> , Brentec Soluciones Tecnol&oacute;gicas SA de CV <br>
                        Calle 20 no 235 Int 1009 x 15 y 7<br>
                        M&eacute;rida Yucat&aacute;n M&eacute;xico<br>
                        CP. 97130<br>
                        Tel: 9999121484<br>
                        soporte@brentec.mx<br>
                        ventas@brentec.mx<br><br>

                        <?php
                        $vfile1 = Yii::$app->basePath . "/version";
                        $vtxt1 = "FND";
                        if (file_exists($vfile1)) {
                            $vdata1 = file($vfile1);
                            if (isset($vdata1[0])) {
                                $vtxt1 = $vdata1[0];
                            }
                        }


                        $apliVersion = Yii::$app->db->createCommand("SELECT * FROM Aplicaciones limit 1")->queryOne();

                        ?>

                        Versi&oacute;n Distribuci&oacute;n V <strong><?php echo $vtxt1; ?></strong><br>
                        Aplicaci&oacute;n <strong><?php echo $apliVersion['nombreAplicacion']; ?></strong><br>
                        BD: <strong><?php echo $dbName; ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalFormAdd" tabindex="-1" role="dialog" aria-labelledby="modalFormAddCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Agregar Registro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="contenidoFormAdd">

                    </div>
                </div>
            </div>
        </div>

        <style>
            .seccionRedonda {
                border: solid 1px #C7C3C3;
                border-radius: 15px;
                padding: 15px;
                vertical-align: top;
            }

            .encabezado {
                border: solid 0px;
                background-color: #C4C1C1;
                font-weight: bold;
            }

            .encabezado td {
                padding: 5px;
                font-size: 11px;
            }
        </style>
        <!--Modal preview-->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="width: 60% !important;">
                <div class="modal-content ">
                    <div class="modal-header">
                        <div class="modal-title" id="previewModalLongTitle">Previsualización</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-preview"
                            onClick="closepreview()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="previewModalContenido" style="padding: 0px;">
                        <?php

                        $imagenEmpresa4 = '<div align="center" ><img src="logos/footer_1_20240625055043.png" alt="Logo" style="align-items: center;"></div>';
                        ?>
                        <table width="100%" align="center" class="factura" style="font-size:11px;">
                            <tr>
                                <td>
                                    <div style="position: absolute; top:20px; left: 20px;">
                                        <div style="border: solid 0px;"><b>Datos del emisor:</b></div>
                                        <div class="spaceLineNormal"><span id="empresaModal"></span></div>
                                        <div class="spaceLineNormal"><span id="rfcModal"></span></div>
                                        <div class="spaceLineNormal"><span id="correoModal"></span></div>
                                        <div class="spaceLineNormal"><span id="telefonoModal"></span></div>
                                        <br />
                                    </div>
                                </td>
                                <td align="left" class="pt-3">
                                    <?= $imagenEmpresa4 ?>
                                </td>
                                <td align="left">
                                    <div style="position: absolute; top:20px; right: 20px;">
                                        <div style="margin-right: 13px;">
                                            <strong>Folio:</strong> <span id="folioModal"></span>
                                        </div>
                                        <div>
                                            <strong>Fecha de emisión:</strong> <span id="fechaEmisionModal"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table width="95%" align="center" class="factura" style=" border:solid 0px; font-size:11px;">
                            <tr>
                                <td colspan="2" align="left"
                                    style="height:50px; padding:0px 25px 0 25px; color:#000; line-height:22px; font-size:15px;border:solid 0px;">
                                    <table width="100%" class="factura">
                                        <tr>
                                            <td width="30">
                                                <!--imagen-->
                                            </td>
                                            <td align="center" style="padding-right:80px;">
                                                <!-- aki -->
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr style="line-height:20px;">
                                <td colspan="1" valign="top">
                                    <div class="seccionRedonda" style="margin-right:25px;">
                                        <strong id="datosReceptor">Datos del receptor</strong>
                                        <hr style="margin: 5px 0;border-top:dotted 1px">
                                        <table width="100%" class="factura">
                                            <tr>
                                                <td width="50%">
                                                    <div class="spaceLineNormal"><b>RFC:</b> <span id="rfc"></span></div>
                                                    <div class="spaceLineNormal"><b>Nombre del receptor: </b> <span
                                                            id="razonSocialModal"></span></div>
                                                    <div class="spaceLineNormal" id="divusoCFDI"><b>Uso CFDI: </b><span id="usoCFDI"></span>
                                                    </div>
                                                    <div class="spaceLineNormal"><b>CP: </b><span id="cp"></span></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td colspan="1" valign="top">

                                    <div class="seccionRedonda" style="margin-right:0px;height: 145px;">
                                        <strong id="datosFactura">Datos de la factura</strong>
                                        <hr style="margin: 5px 0;border-top:dotted 1px">
                                        <table width="100%" class="factura">
                                            <tr>
                                                <td valign="top">
                                                    <!-- <div class="spaceLineNormal"><b>Fecha Cotización:</b> <span id="dateModal"></span> 
                            <div class="spaceLineNormal"><b>Vigencia:</b> <span id="vigenciaModal"></span>  -->
                                                    <div class="spaceLineNormal"><b id="tipoComprobanteText">Tipo de comprobante:</b> <span
                                                            id="tipoComprobante"></span>
                                                        <div class="spaceLineNormal" id="divmetodoPago"><b id="metodoPagoText">Metodo de pago:</b> <span
                                                                id="metodoPago"></span>
                                                            <div class="spaceLineNormal"><b>Forma de pago:</b> <span
                                                                    id="formaPago"></span>
                                                                <!-- <b>Vigencia al:</b> <span id="vigenciaModal"></span></div>
                            <div class="spaceLineNormal"><b>Observaciones:</b></div>
                            <span id="comentarioModal"></span> -->
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="seccionConceptos">
                                    <br>
                                    <strong>Conceptos</strong>
                                    <hr style="margin: 5px 0;border-top:dotted 1px">
                                    <table class="conceptos" style="border:solid 0px;width:100%" id="conceptos">
                                        <tr class="encabezado">
                                            <td>
                                                Descripción
                                            </td>
                                            <td width="35">
                                                Cantidad
                                            </td>
                                            <td width="60">
                                                Unidad
                                            </td>
                                            <td width="70" align="right">
                                                PU
                                            </td>
                                            <td width="70" align="right">
                                                Importe
                                            </td>
                                        </tr>
                                        <!--Productos-->
                                        <tr>
                                            <td colspan="3">
                                            </td>
                                            <td colspan="2">
                                                <hr>
                                            </td>
                                        </tr>
                                        <div id="tbody">
                                        </div>
                                        <tr class="totales">
                                            <td colspan="3">
                                            </td>
                                            <td><strong>Subtotal</strong></td>
                                            <td align="right">$<span id="total_sub"><span id="subtotalModal"></span></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="left">
                                                <span id="tipoRelacionModal"></span>
                                            </td>
                                            <td align="right"><strong>Iva</strong></td>
                                            <td align="right">$<span id="total_iva"><span id="ivaModal"></span></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="left">
                                            </td>
                                            <td align="right" valign="top"><strong>Total</strong></td>
                                            <td align="right" valign="top">$<span id="totalModal"></span>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="conceptosdetalles d-none table table-bordered table-striped" style="width:100%" id="conceptosdetalles">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 45%;">Folio fiscal</th>
                                                <th style="width: 15%;">Serie y Folio</th>
                                                <th style="width: 10%;">Moneda</th>
                                                <th style="width: 35%;">Método de pago</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">
                                            <tr>
                                                <td>
                                                    <span id="folioFiscalConcepto"></span>
                                                </td>
                                                <td>
                                                    <span id="serieYFolioConcepto"></span>
                                                </td>
                                                <td>
                                                    <span id="monedaConcepto"></span>
                                                </td>
                                                <td>
                                                    <span id="metodoPagoConcepto"></span>
                                                </td>
                                                <td class="snbutton-column">

                                                </td>
                                            </tr>
                                            <tr id="new_prod">
                                                <td colspan="5">
                                                    <div class="row mx-0">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Parcialidad:</label>
                                                                <div id="parcialidad"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Saldo anterior:</label>
                                                                <div id="saldoAnterior"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Importe pagado:</label>
                                                                <div id="importePagado"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Saldo insoluto:</label>
                                                                <div id="saldoInsoluto"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <hr />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="left" style="padding-top: 5px; padding-left:45px;"
                                    class="seccionConceptos">
                                    <!--'.$observaciones.'-->
                                    <span id="observacionesModal"></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center" style="padding-top: 20px;">

                                </td>
                            </tr>
                        </table>
                        <div align="right" class="m-3">
                            <button type="button" id="btnGuardar"
                                class="btn <?= Yii::$app->globals->btnSave() ?> submitFormBtn"
                                onClick="guardarSinFacturar();">Guadar Sin facturar</button>
                            <button type="button" id="btnTimbrar"
                                class="btn <?= Yii::$app->globals->btnSave() ?> submitFormBtn"
                                onClick="timbrar(); ">Timbrar</button>
                            <span id='spanTimbrar' style="color:red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal detalle movimientos bancos -->
        <div class="modal fade" id="modalDetalleMovBancos" tabindex="-1" role="dialog" aria-labelledby="modalDetalleMovBancosCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="width: 70% !important;">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetalleMovBancosLongTitle">Detalle del movimiento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-preview"
                            onClick="closepreview()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalDetalleMovBancosContenido">
                        <table class="table">
                            <tr>
                                <td width="20%"><b>Cuenta:</b></td>
                                <td width="30%" align="left"><span id="spancuenta"></span></td>
                                <td width="20%"><b>Fecha Pago:</b></td>
                                <td align="left"><span id="spanfechaPago"></span></td>
                            </tr>
                            <tr>
                                <td><b>Tipo Pago:</b></td>
                                <td><span id="spantipoPago"></span></td>
                                <td><b>Importe Abono:</b></td>
                                <td><span id="spanimporteAbono"></span></td>
                            </tr>
                            <tr>
                                <td><b>Importe Cargo:</b></td>
                                <td><span id="spanimporteCargo"></span></td>
                                <td><b>Referencia Banco:</b></td>
                                <td><span id="spanreferenciaBanco"></span></td>
                            </tr>
                            <tr>
                                <td><b>Concepto:</b></td>
                                <td><span id="spanconcepto"></span></td>
                                <td><b>Ordenate Pago:</b></td>
                                <td><span id="spanordenatePago"></span></td>
                            </tr>
                            <tr>
                                <td><b>Beneficiario:</b></td>
                                <td><span id="span" beneficiario></span></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <b>Facturas asignadas:</b>
                        <span id="spanTablaFacturasAsignadas"></span>
                        <div align="right">
                            <!-- <button type="button" id="btnTimbrar"
                            class="btn <?= Yii::$app->globals->btnSave() ?> submitFormBtn"
                            onClick="timbrar(); ">Timbrar</button>
                        <span id='spanTimbrar' style="color:red;"></span> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Asignar Factura al movimiento de bancos -->
        <div class="modal fade" id="modalAsignarFactura" tabindex="-1" role="dialog" aria-labelledby="modalAsignarFacturaCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="width: 70% !important;">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAsignarFacturaLongTitle">Asignar Factura</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-preview"
                            onClick="closepreview()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalAsignarFacturaContenido">
                        <div class=" ">
                            <div class="row">
                                <div class="col-sm-4">
                                    Serie:
                                    <input type="text" id="txtSerie" name="txtSerie" class="form-control" />
                                    <input type="hidden" id="hddMovimientoBancoID" name="hddMovimientoBancoID" />
                                </div>
                                <div class="col-sm-4">
                                    Folio:
                                    <input type="text" id="txtFolio" name="txtFolio" class="form-control" />
                                </div>
                                <div class="col-sm-4">
                                    <br />
                                    <button type="button" id="btnBuscarFacturas" class="btn <?= Yii::$app->globals->btnSave() ?> submitFormBtn">Buscar</button>
                                </div>
                                <div class="col-sm-12">
                                    <div id="facturaResultado"></div>
                                </div>
                                <div class="col-sm-12">
                                    <div id="asignarFacturaResultado"></div>
                                </div>
                            </div>
                        </div>
                        <div align="right" style="display: none;" id="divAsignarFact">
                            <button type="button" id="btnAsignarFactura"
                                class="btn <?= Yii::$app->globals->btnSave() ?> submitFormBtn">Asignar factura</button>
                            <span id='spanAsignarFacturaBtn' style="color:red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- inicia modal iconos -->
        <div class="modal fade bd-icons-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="iconsModalLongTitle">Iconos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo Yii::$app->globals->getIcons(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- finaliza modal iconos -->


        <!-- inicia modal iconos linear -->
        <div class="modal fade linearIconModal" tabindex="-1" role="dialog" aria-labelledby="linearIcon" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="iconsModalLongTitle">Iconos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo Yii::$app->globals->getIconsLinear(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- finaliza modal iconos linear-->


        <!-- INICIA MODAL Lista Factuas en complemento pago -->
        <div class="modal fade" id="modalListaFactEnPagos" tabindex="-1" role="dialog" aria-labelledby="previewModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="width: 70% !important;">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLongTitle">Lista de facturas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-preview"
                            onClick="closepreview()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="previewModalContenido">
                        <div id="divListaFacturasEnPagos"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- finaliza MODAL Lista Factuas en complemento pago -->

        <script src="<?= $baseUrl; ?>/require/js/adicionales/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
        <script src="<?= $baseUrl; ?>/require/js/bootstrap.bundle.min.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/adicionales/metismenu.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/app.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/demo.js"></script>

        <!--FORMS-->

        <!--Clipboard-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/clipboard.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/clipboard.js"></script>

        <!--Datepickers-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/datepicker.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/daterangepicker.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/moment.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/datepicker.js"></script>

        <!--Multiselect-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/bootstrap-multiselect.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/adicionales/select2.min.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/input-select.js"></script>


        <!--Form Wizard-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/form-wizard.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/form-wizard.js"></script>

        <!--Input Mask-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/input-mask.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/input-mask.js"></script>

        <!--RangeSlider-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/wnumb.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/range-slider.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/range-slider.js"></script>

        <!--Textarea Autosize-->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/textarea-autosize.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/form-components/textarea-autosize.js"></script>

        <!--Toggle Switch -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/form-components/toggle-switch.js"></script>


        <!--COMPONENTS-->

        <!--BlockUI -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/blockui.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/blockui.js"></script>

        <!--Calendar -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/calendar.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/calendar.js"></script>

        <!--Slick Carousel -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/carousel-slider.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/carousel-slider.js"></script>

        <!--Circle Progress -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/circle-progress.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/circle-progress.js"></script>


        <!--Ladda Loading Buttons -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/ladda-loading.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/vendors/spin.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/ladda-loading.js"></script>


        <!--Perfect Scrollbar -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/scrollbar.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/scrollbar.js"></script>

        <!--Toastr-->
        <script src="<?= $baseUrl; ?>/require/js/adicionales/toastr.min.js" crossorigin="anonymous"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/toastr.js"></script>

        <!--SweetAlert2-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/sweet-alerts.js"></script>

        <!--Tree View -->
        <script src="<?= $baseUrl; ?>/require/js/vendors/treeview.js"></script>
        <script src="<?= $baseUrl; ?>/require/js/scripts-init/treeview.js"></script>


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
        <script src="<?= $baseUrl; ?>/require/js/alertif.min.js"></script>

        <script>
            function changeIdioma(id, flag) {
                $.ajax({
                    url: '<?php echo Url::to(['idiomas/changeidioma']); ?>',
                    type: "POST",
                    data: "id=" + id + "&flag=" + flag,
                    success: function(response) {
                        location.reload();
                    }
                });
            }

            //para que se vea la modal en primer plano
            //$('.modal-dialog').parent().on('show.bs.modal', function(e){ $(e.relatedTarget.attributes['data-target'].value).appendTo('body'); });
        </script>
        <?php $this->endBody() ?>
        <script>
            window.jsPDF = window.jspdf.jsPDF;
            applyPlugin(window.jsPDF);
        </script>

    </body>

    </html>
<?php
    $this->endPage();
} else {
    Yii::$app->response->redirect(['site/timeout']);
}

function getDsnAttribute($name, $dsn)
{
    if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
        return $match[1];
    } else {
        return null;
    }
}
