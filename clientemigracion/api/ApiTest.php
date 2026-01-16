<?php
// required headers
require_once '../libs/apitools.php';

ApiTools::generaCabeceras();

// get database connection
$database = new Database('mysql');
$db = $database->getConnection();
 
// instantiate product object
$user = new Usuario($database);
 
// submitted data will be here
// get posted data

 
 
// use the create() method here
// create the user
if( $user->find(1)){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array(
        "resultado" => true,
        "mensaje" => "usuario encontrado",
        "datos" =>  $user->Dataset
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