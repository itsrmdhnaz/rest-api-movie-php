<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

include_once '../controllers/MovieController.php';

$movieController = new MovieController();

$request_method = $_SERVER["REQUEST_METHOD"];
$headers = getallheaders();

if (!isset($headers['authorization'])) {
    echo json_encode(array("status" => "error", "message" => "Authorization header not provided."));
    exit();
}

$email = $headers['authorization'];

switch($request_method) {
    case 'GET':       
        $movie = $movieController->read($email);
        echo json_encode(array("status" => "success", "message" => "data retrived successfully.", "data" => $movie));        
        break;

    case 'POST':
        $data = $_POST;
        if($movieController->create($data, $email)){
        echo json_encode(array("status" => "success", "message" => "movie created successfully."));
        } else {
        echo json_encode(array("status" => "error", "message" => "Unable to create movie."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $id = $data['id'];
            if($movieController->delete($id, $email)){
                echo json_encode(array("status" => "success", "message" => "movie deleted successfully."));
            } else {
                echo json_encode(array("status" => "error", "message" => "Unable to delete movie."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID not provided."));
        }
        break;

    default:
        echo json_encode(array("status" => "success", "message" => "movie deleted successfully."));
        break;
}
?>
