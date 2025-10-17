<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->content)) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO notes (content) VALUES (:content)";
        $stmt = $db->prepare($query);
        $content = htmlspecialchars(strip_tags($data->content));
        
        $stmt->bindParam(":content", $content);
        
        if($stmt->execute()) {
            $lastId = $db->lastInsertId();
            $query = "SELECT id, content, timestamp FROM notes WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $lastId);
            $stmt->execute();
            
            $note = $stmt->fetch(PDO::FETCH_ASSOC);
            
            http_response_code(201);
            echo json_encode(array(
                "status" => "success",
                "message" => "Note created successfully",
                "data" => $note
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "status" => "error",
                "message" => "Unable to create note"
            ));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "status" => "error",
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "status" => "error", 
        "message" => "Note content is required"
    ));
}
?>
