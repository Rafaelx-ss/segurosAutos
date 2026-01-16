<?php
use Yii;
	Yii::$app->session->set('RESULTADO_BOTON', "");
	$RegistrosErrores= "";
	$ErrorDetenerCiclo= "";
	try{
		$i=0;
		$TOTAL= Yii::$app->session['REGISTROS_VISIBLES'];
		echo "Total registros visibles enviados: " . Yii::$app->session['REGISTROS_VISIBLES']."<BR />";
		for ($i=1; $i<=$TOTAL; $i++){
			$cadenaSeleccionado="";
			if(isset($_POST["ck".$i])){
				$cadenaSeleccionado= $_POST["ck".$i];
				////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////// DESCARGAS /////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////
				$mensaje2="";
				require_once(dirname(__DIR__).'/clientemigracion/modelos/ModeloApiListado.php');
				$incluir= dirname(__DIR__).'/clientemigracion/modelos/Modelo' . trim($txtBanderaApi) . '.php';
				
				try{
					//echo "INSERTA NORMAL";
					$validaEjecucion = $migracion->Inserta($coleccion);
				}
				catch (Exception $e){
					echo $e->getMessage();//.'<br /> <br />Consulta: <br />';
					$RegistrosErrores = $e->getMessage();
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////// FIN DESCARGAS ///////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
		}
	}  
    catch (Exception $e){
        echo $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		$RegistrosErrores = $e->getMessage();
    }
	
	Yii::$app->session->set('RESULTADO_BOTON', $RegistrosErrores.$ErrorDetenerCiclo);
?>