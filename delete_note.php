<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $query = "UPDATE notes SET is_active = 0 WHERE id = :id";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":id", $data->id);
        
        if($stmt->execute()) {
            if($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Note deleted successfully"
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Note not found"
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                "status" => "error",
                "message" => "Unable to delete note"
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
        "message" => "Note ID is required"
    ));
}

?>
