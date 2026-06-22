<?php
// ICCT MIS Survey - Form Handler
// Stores responses as JSON files in a data directory

$dataDir = __DIR__ . '/survey-data';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Determine content type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
} else {
    $input = $_POST;
}

if (empty($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

// Add metadata
$input['_submitted_at'] = date('Y-m-d H:i:s');
$input['_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Generate unique ID
$id = date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 8);
$filename = $dataDir . '/response_' . $id . '.json';

file_put_contents($filename, json_encode($input, JSON_PRETTY_PRINT));

// Also append to master CSV
$csvFile = $dataDir . '/all_responses.csv';
$isNew = !file_exists($csvFile);
$fp = fopen($csvFile, 'a');
if ($isNew) {
    fputcsv($fp, array_keys($input));
}
fputcsv($fp, array_values($input));
fclose($fp);

// Return success
$redirect = $_GET['redirect'] ?? '';
if ($redirect) {
    header('Location: ' . $redirect);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'id' => $id]);
