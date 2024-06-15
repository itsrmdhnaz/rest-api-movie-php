<?php
include_once '../models/Movie.php';

class MovieController {
    private $db;
    public $movie;

    public function __construct(){
        $this->db = include('../config/Database.php');
        $this->movie = new Movie($this->db);
    }

    public function read($email){
        $this->movie->email = $email;
        $result = $this->movie->read();
        $movie = array();
        while ($row = $result->fetch_assoc()){
            $movie[] = $row;
        }
        return $movie;
    }

    public function create($data, $email){
        $this->movie->email = $email;
        $this->movie->title = $data['title'];
        $this->movie->genre = $data['genre'];
        $this->movie->releaseDate = $data['releaseDate'];
        
        if (isset($_FILES['image'])) {
            $imageId = $this->movie->generateUniqueImageId();
            $fileName = $imageId . ".jpeg";
            $directory = "../images/$email/";
                        
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $targetFilePath = $directory . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $this->movie->imageId = $imageId;
                if($this->movie->create()){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public function delete($id, $email){
        $this->movie->id = $id;
        $this->movie->email = $email; 

        // Delete the memory record
        if($this->movie->delete()){
            return true;
        }
        return false;
    }     
    
}
?>

