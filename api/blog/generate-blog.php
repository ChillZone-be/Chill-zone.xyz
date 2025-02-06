<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://chill-zone.xyz');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nur POST-Anfragen sind erlaubt']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$topic = $data['topic'] ?? '';

if (empty($topic)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kein Thema angegeben']);
    exit();
}

// Load environment variables from .env file
$env_file = __DIR__ . '/../../server/.env';
if (file_exists($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Get API key from environment variable
$api_key = $_ENV['OPENAI_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'API-Schlüssel nicht konfiguriert']);
    exit();
}

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$payload = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'Du bist ein erfahrener Blogger, der Blogbeiträge in deutscher Sprache schreibt. 
                         Dein Stil ist freundlich, persönlich und unterhaltsam, jedoch immer informativ und gut strukturiert. 
                         Du verwendest eine klare, leicht verständliche Sprache und achtest auf Rechtschreibung und Grammatik.'
        ],
        [
            'role' => 'user',
            'content' => 'Schreibe einen kurzen deutschen Blogpost über "' . $topic . '". 
                         Gliedere den Text in maximal drei Absätze. 
                         Der Beitrag soll:
                         1. Mit einem kurzen, ansprechenden Einleitungssatz starten.
                         2. Im Hauptteil wesentliche Informationen oder Tipps zum Thema enthalten.
                         3. Mit einem Mini-Fazit oder einer persönlichen Note enden.
                         
                         Halte den Text locker, aber informativ. 
                         Vermeide zu viele Fachbegriffe und bleibe bei insgesamt maximal 3 Absätzen.'
        ]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

try {
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($status_code !== 200) {
        throw new Exception('OpenAI API Fehler: HTTP ' . $status_code);
    }
    
    $result = json_decode($response, true);
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Ungültige Antwort von OpenAI');
    }
    
    $generated_content = trim($result['choices'][0]['message']['content']);
    echo json_encode([
        'success' => true,
        'post' => [
            'title' => $topic,
            'content' => $generated_content
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Fehler beim Generieren des Blogposts: ' . $e->getMessage()
    ]);
}

curl_close($ch);
