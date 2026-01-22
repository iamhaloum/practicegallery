<?php
header('Content-Type: application/json');

// Update these with your cloud database credentials
$servername = "localhost";  // Your cloud host
$username = "webuser";
$password = "StrongPass123";
$dbname = "mydb";           // Your database name

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    
    if ($action === 'get') {
        // Get all notes
        $stmt = $db->query('SELECT name, content FROM note ORDER BY id DESC');
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notes ?: []);
    } 
    elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add new note
        $name = trim($_POST['name'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        if (empty($name) || empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and content are required']);
            exit;
        }
        
        $stmt = $db->prepare('INSERT INTO note (name, content) VALUES (?, ?)');
        $stmt->execute([$name, $content]);
        
        echo json_encode([
            'name' => $name,
            'content' => $content
        ]);
    } 
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>



