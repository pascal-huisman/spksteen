<?php
declare(strict_types=1);

require_once __DIR__ . '/settings.php';

header('Content-Type: application/json');

// Contact is uitgeschakeld
if (!$contact_enabled) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Contact is tijdelijk niet beschikbaar']);
    exit;
}

// Alleen POST accepteren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── Configuratie ────────────────────────────────────────────────────────────
$NOTIFY_URL      = getenv('NOTIFY_URL')      ?: 'http://aims_notify:80/notify';
$NOTIFY_API_KEY  = getenv('NOTIFY_API_KEY')  ?: '';
$NOTIFY_TEMPLATE = getenv('NOTIFY_TEMPLATE') ?: 'speksteen_contact';

// ── Input ophalen & valideren ───────────────────────────────────────────────
$naam      = trim($_POST['naam']      ?? '');
$email     = trim($_POST['email']     ?? '');
$onderwerp = trim($_POST['onderwerp'] ?? '');
$bericht   = trim($_POST['bericht']   ?? '');

$errors = [];
if ($naam === '')                                    $errors[] = 'Naam is verplicht';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = 'Ongeldig e-mailadres';
if ($bericht === '')                                 $errors[] = 'Bericht is verplicht';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

// ── Notificatie versturen via aims_notify ───────────────────────────────────
$payload = [
    'api_key'   => $NOTIFY_API_KEY,
    'template'  => $NOTIFY_TEMPLATE,
    'variables' => [
        'naam'      => $naam,
        'email'     => $email,
        'onderwerp' => $onderwerp ?: 'Geen onderwerp opgegeven',
        'bericht'   => nl2br(htmlspecialchars($bericht)),
        'datum'     => date('d-m-Y H:i'),
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'onbekend',
    ],
];

$ch = curl_init($NOTIFY_URL);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);

$response   = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError  = curl_error($ch);
curl_close($ch);

// ── Resultaat teruggeven ────────────────────────────────────────────────────
if ($curlError) {
    error_log("[speksteen/contact] cURL fout: $curlError");
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Kan notificatie niet versturen']);
    exit;
}

if ($httpStatus < 200 || $httpStatus >= 300) {
    error_log("[speksteen/contact] aims_notify HTTP $httpStatus: $response");
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Notificatie mislukt', 'detail' => $response]);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Bericht ontvangen']);
