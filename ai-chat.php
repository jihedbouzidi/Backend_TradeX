<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Configuration OpenRouter
$openRouterApiKey = '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';
$model = 'mistralai/mistral-7b-instruct'; // Modèle gratuit et performant

$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

if (empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message requis']);
    exit;
}

try {
    // Construction des messages pour l'API
    $messages = [
        ['role' => 'system', 'content' => 'Tu es TradeX Bot, assistant technique spécialisé en matériel informatique. Tes réponses doivent être précises et techniques.']
    ];

    if (!empty($input['conversation']) && is_array($input['conversation'])) {
        foreach ($input['conversation'] as $msg) {
            if (!empty($msg['sender']) && !empty($msg['text'])) {
                $role = ($msg['sender'] === 'user') ? 'user' : 'assistant';
                $messages[] = ['role' => $role, 'content' => trim($msg['text'])];
            }
        }
    }
    
    $messages[] = ['role' => 'user', 'content' => trim($input['message'])];

    // Appel à l'API OpenRouter
    $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    $postData = [
        'model' => $model,
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 1000
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openRouterApiKey,
            'HTTP-Referer: http://localhost', // Obligatoire pour OpenRouter
            'X-Title: TradeX Assistant' // Identifie votre application
        ],
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        throw new Exception("Erreur cURL: " . $error);
    }

    if ($httpCode !== 200) {
        throw new Exception("Erreur API (HTTP $httpCode): " . $response);
    }

    $data = json_decode($response, true);
    if (!isset($data['choices'][0]['message']['content'])) {
        throw new Exception("Réponse API mal formatée: " . $response);
    }

    // Réponse réussie
    echo json_encode([
        'success' => true,
        'response' => $data['choices'][0]['message']['content']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $response ?? null
    ]);
}