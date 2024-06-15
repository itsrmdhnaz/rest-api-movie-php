<?php
include('../config/Database.php');
header("Content-Type: application/json; charset=UTF-8");
$headers = getallheaders();
$email = isset($headers['authorization']) ? $headers['authorization'] : '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method != "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

try {
    if (empty($email)) {
        http_response_code(401);
        throw new Exception('Unauthorized');
    }

    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (empty($id)) {
        http_response_code(400);
        throw new Exception('Invalid request: ID is required');
    }

    $email = $mysqli->real_escape_string($email);
    $movieId = $mysqli->real_escape_string($id);

    $selectSql = "SELECT imageId FROM movies WHERE id = '$movieId' AND email = '$email'";
    $movieResult = $mysqli->query($selectSql);
    if ($movieResult && $movieResult->num_rows > 0) {
        $movie = $movieResult->fetch_assoc();
        $imageId = $movie['imageId'];

        $fileName = $imageId . ".jpeg";
        $directory = "../images/$email/";
        $targetFilePath = $directory . $fileName;

        if (file_exists($targetFilePath)) {
            unlink($targetFilePath);
        }

        $deleteSql = "DELETE FROM movies WHERE id = '$movieId' AND email = '$email'";
        if ($mysqli->query($deleteSql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "movie deleted successfully"]);
        } else {
            http_response_code(500);
            throw new Exception("Error: " . $deleteSql . "<br>" . $mysqli->error);
        }
    } else {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to delete this movie data']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$mysqli->close();