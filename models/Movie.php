<?php
class Movie {
    private $conn;
    private $table_name = "movies";
    
    public $id;
    public $title;
    public $genre;    
    public $releaseDate;
    public $imageId;
    public $email; 

    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $query = "SELECT id, title, genre, releaseDate, imageId FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function create(){
        $query = "INSERT INTO " . $this->table_name . " (title, genre, releaseDate, imageId, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", $this->title, $this->genre, $this->releaseDate, $this->imageId, $this->email);
        return $stmt->execute();
    }

    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $this->id, $this->email);
        return $stmt->execute();
    }

    function generateUniqueImageId() {
        return uniqid();
    }

    public function getUserByEmail($email){
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            return true;
        }
        return false;
    }

    public function getByMovieImageId($email){
        $query = "SELECT * FROM " . $this->table_name . " WHERE imageId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            return $result->fetch_assoc();
        }
        return false;
    }

    function getByIdAndEmail($id, $email){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            return $result->fetch_assoc();
        }
        return false;
    }
}
?>