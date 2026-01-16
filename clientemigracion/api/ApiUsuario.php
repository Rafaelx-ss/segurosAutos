<?php
// required headers
require_once '../libs/apitools.php';

ApiTools::generaCabeceras();

// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate product object
$user = new Usuario($database);
 
// submitted data will be here
// get posted data
$user->Campos = json_decode(file_get_contents("php://input"));
 
 
// use the create() method here
// create the user
if(
    !empty($user->Campos->usuarioID) &&
    !empty($user->Campos->correoUsuario) &&
    !empty($user->Campos->nombreUsuario) &&
    !empty($user->Campos->passw) 
     && $user->Crear()
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array(
        "resultado" => true,
        "mensaje" => "usuario creado"
    ));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array(
        "resultado" => false,
        "message" => "No fue posible crear el usuario."
    ));
}
?>