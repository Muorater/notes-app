<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = 'localhost';
$db   = 'notes-db';
$user = 'root'; 
$pass = '';    
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$action = $request[0] ?? '';

switch ($method) {
    case 'GET':
        // Fetch all notes with their checklist items
        $stmt = $pdo->query("SELECT * FROM notes ORDER BY id DESC");
        $notes = $stmt->fetchAll();

        foreach ($notes as &$note) {
            if ($note['type'] === 'checklist') {
                $stmtItems = $pdo->prepare("SELECT id, item_text, is_completed FROM checklist_items WHERE note_id = ?");
                $stmtItems->execute([$note['id']]);
                $note['items'] = $stmtItems->fetchAll();
            } else {
                $note['items'] = [];
            }
        }
        echo json_encode($notes);
        break;

    case 'POST':
        // Create a new note
        $data = json_decode(file_get_contents("php://input"), true);
        $title = $data['title'] ?? '';
        $body = $data['body'] ?? '';
        $type = $data['type'] ?? 'text';
        $color = $data['color'] ?? '#ffffff';
        $items = $data['items'] ?? [];

        $stmt = $pdo->prepare("INSERT INTO notes (title, body, type, color) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $body, $type, $color]);
        $noteId = $pdo->lastInsertId();

        if ($type === 'checklist' && !empty($items)) {
            $stmtItem = $pdo->prepare("INSERT INTO checklist_items (note_id, item_text, is_completed) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$noteId, $item['text'] ?? '', $item['completed'] ?? 0]);
            }
        }

        echo json_encode(["success" => true, "id" => $noteId]);
        break;

    case 'PUT':
        // Update an existing note
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing note ID"]);
            exit();
        }

        $title = $data['title'] ?? '';
        $body = $data['body'] ?? '';
        $color = $data['color'] ?? '#ffffff';
        $items = $data['items'] ?? [];

        $stmt = $pdo->prepare("UPDATE notes SET title = ?, body = ?, color = ? WHERE id = ?");
        $stmt->execute([$title, $body, $color, $id]);

        // Update checklist items: clear old and insert updated ones
        $stmtCheck = $pdo->prepare("SELECT type FROM notes WHERE id = ?");
        $stmtCheck->execute([$id]);
        $noteType = $stmtCheck->fetchColumn();

        if ($noteType === 'checklist') {
            $pdo->prepare("DELETE FROM checklist_items WHERE note_id = ?")->execute([$id]);
            $stmtItem = $pdo->prepare("INSERT INTO checklist_items (note_id, item_text, is_completed) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$id, $item['text'] ?? '', $item['completed'] ?? 0]);
            }
        }

        echo json_encode(["success" => true]);
        break;

    case 'DELETE':
        // Delete a note
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing note ID"]);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["success" => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>