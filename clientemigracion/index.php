<?php
  session_start();
	if (isset($_POST["TipoSolicitud"])){
		$_SESSION["TIPOSOLICITUD"]=$_POST["TipoSolicitud"];
		$_SESSION["IDENTIFICADOR"]=$_POST["txtIdentificador"];
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page - WebApiClient</title>
        <!-- Bootstrap 4 CSS and custom CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
        <link rel="stylesheet" type="text/css" href="site.css" />

        <!DOCTYPE html> 
<html> 
	<head> 
		<script> 
		
			// Function to check Whether both passwords 
			// is same or not. 
			function checkPassword(form) { 
				password1 = form.Password.value; 
				password2 = form.PasswordVerificar.value; 

				// If password not entered 
				if (password1 == '') 
					alert ("Debe escribir una contraseña"); 
					
				// If confirm password not entered 
				else if (password2 == '') 
					alert ("Debe repetir la contraseña"); 
					
				// If Not same return False.	 
				else if (password1 != password2) { 
					alert ("\nLa contraseña no coincide...") 
					return false; 
				} 

				// If same return True. 
				else{  
					return true; 
				} 
			} 
		</script> 
	
</head>
<body>
    <div class="container">
        <main role="main" class="pb-3">
            

    

    
        <?php
			echo Yii::app()->basePath . '/extensions/folder_name/file_name.php';
        $mensaje ='';
        if ((isset($_SERVER["REQUEST_METHOD"])) && ($_SERVER["REQUEST_METHOD"] == 'POST')) 
        {
			//echo "entro<br>";
			
            require 'lib/database.php';
            require 'modelos/ModeloApiConexion.php';
			require 'modelos/ModeloApiListado.php';
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);

            $UserName = $_POST['UsuarioApiLista'];
            $Password = $_POST['Password'];
            $RutaApiLista = $_POST['RutaApiLista'];
            $TipoSolicitud = $_POST['TipoSolicitud'];
			$Identificador = $_POST['txtIdentificador'];//Grupo o Establecimiento
            $parametros =   Array(
                "UsuarioApiLista" => $UserName,
                "PassApiLista" => $Password,
                "RutaApiLista" => $RutaApiLista,
                "TipoSolicitud" => $TipoSolicitud
            );
			
            if($conexion->Update($parametros))
            {
            ?>
				<h2>Migración</h2>
				
				<a class="btn btn-primary" href="descargaapis.php">Paso 1. Importar datos</a>
				<?php
				if (isset($_POST["PASO_1"])){
				?>
					<a class="btn btn-primary" href="showdata.php">Consultar datos</a>
					
				<?php
				}
				//$mensaje='Se actualizó el registro';			
            }
            else
            {
                echo $mensaje='Hubo un error' . $parametros;
            }
            
            
        }
        else
        {
            echo "<h2>Registrar</h2>
            <form id='reg_form' method='post' onSubmit = 'return checkPassword(this)'>
                <div class='form-group'>
                    <label for='UsuarioApiLista'>Nombre</label>
                    <input type='text' class='form-control' value='masterBrentec' id='UsuarioApiLista' name='UsuarioApiLista' placeholder='Nombre' required>
                </div>

                <div class='form-group'>
                    <label for='Password'>Password</label>
                    <input type='password' class='form-control' value='Brentec2020' id='Password' name='Password' placeholder='Password' required>
                </div>

                <div class='form-group'>
                    <label for='PasswordVerificar'>Repetir Password</label>
                    <input type='password' class='form-control' value='Brentec2020' id='PasswordVerificar' name='PasswordVerificar' placeholder='Password' required>
                </div>

                <div class='form-group'>
                    <label for='RutaApiLista'>Ruta</label>
                    <input type='text' class='form-control' value='http://10.168.88.230/ApiCongo'  id='RutaApiLista' name='RutaApiLista' placeholder='Ruta'>
                </div>

                <div class='form-group'>
                    <label for='txtIdentificador'>Identificador</label>
                    <input type='text' class='form-control' value='4'  id='txtIdentificador' name='txtIdentificador' placeholder='Grupo o Establecimiento'>
                </div>

                <div class='form-group'>
                    <label for='TipoSolicitud'>Tipo</label>
                    <select name='TipoSolicitud' id='TipoSolicitud' class='form-control'>
                        <option value='E'>Estacion</option>
                        <option value='G' selected>Grupo</option>
                    </select>
                </div>

                <button type='submit' class='btn btn-primary'>Enviar</button>
            </form>";
        }
        
       
        ?>
        <p><?php echo $mensaje;  ?></p>
    


        </main>
    </div>

    
</body>
</html>
    
    