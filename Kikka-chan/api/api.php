<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require 'config.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$board = isset($request_uri[1]) ? $request_uri[1] : null;

// Создание поста
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $board && end($request_uri) === 'post') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Проверка временного интервала
    $stmt = $pdo->prepare("SELECT created_at FROM posts WHERE ip = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$_SERVER['REMOTE_ADDR']]);
    $last_post = $stmt->fetch();
    
    if ($last_post && time() - strtotime($last_post['created_at']) < 10) {
        http_response_code(429);
        echo json_encode(['error' => 'Подождите 10 секунд между постами']);
        exit;
    }

    // Обработка изображения
    $image_name = null;
    if (!empty($data['image'])) {
        $image_data = base64_decode(explode(',', $data['image'])[1]);
        $mime = getimagesizefromstring($image_data)['mime'];
        
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Недопустимый формат изображения']);
            exit;
        }
        
        $ext = explode('/', $mime)[1];
        $image_name = uniqid() . '.' . $ext;
        $image_path = "../img/full/{$image_name}";
        file_put_contents($image_path, $image_data);
        
        // Создание превью
        create_thumbnail($image_path, "../img/thumb/{$image_name}");
    }

    // Сохранение в БД
    $stmt = $pdo->prepare("INSERT INTO posts (board_id, message, image, ip) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        get_board_id($board),
        substr($data['message'], 0, 2000),
        $image_name,
        $_SERVER['REMOTE_ADDR']
    ]);
    
    echo json_encode(['success' => true]);
}

// Получение тредов
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $board && end($request_uri) === 'threads') {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE board_id = ? ORDER BY id DESC LIMIT 50");
    $stmt->execute([get_board_id($board)]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($posts);
}

function create_thumbnail($src, $dest) {
    list($width, $height) = getimagesize($src);
    $new_width = 250;
    $new_height = floor($height * ($new_width / $width));
    
    $image = ($width > $height) ? 
        imagecreatefromstring(file_get_contents($src)) : 
        imagerotate(imagecreatefromstring(file_get_contents($src)), 90, 0);
    
    $thumb = imagecreatetruecolor($new_width, $new_height);
    imagecopyresized($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($thumb, $dest, 85);
    imagedestroy($thumb);
}

function get_board_id($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn() ?: 1;
}
?>
