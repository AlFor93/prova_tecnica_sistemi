<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new User($db);

// set ID property of record to read
$user->user_id = isset($_GET['user_id']) ? $_GET['uesr_id'] : die();

// read the details of user to be edited
$user->readOne();

if($user->firstname!=null){
  // create array
  $user_arr = array(
    "user_id" =>  $user->user_id,
    "firstname" => $user->firstname,
    "lastname" => $user->lastname,
    "email" => $user->email,
    "customer_id" => $user->customer_id,
    "customer_name" => $user->customer_name

  );

  // set response code - 200 OK
  http_response_code(200);

  // make it json format
  echo json_encode($user_arr);
}

else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user user does not exist
    echo json_encode(array("message" => "user does not exist."));
}
?>
