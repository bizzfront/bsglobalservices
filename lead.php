<?php
// Load environment variables from a local .env file if available
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if ($env !== false) {
        foreach ($env as $key => $value) {
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

header('Access-Control-Allow-Origin: ' . (getenv('ALLOW_ORIGIN') ?: 'https://bsglobalservices.com'));
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if (getenv('RESTRICT_BY_IP') === 'true' && $_SERVER['REMOTE_ADDR'] !== $_SERVER['SERVER_ADDR']) {
    echo json_encode(['code' => '05', 'data' => 'Access not allowed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => '05', 'data' => 'Access not allowed.']);
    exit;
}

function read_field(string $key, int $max = 255): string {
    $value = trim($_POST[$key] ?? '');
    return mb_substr($value, 0, $max);
}

function read_bool(string $key): bool {
    if (!isset($_POST[$key])) {
        return false;
    }
    $value = $_POST[$key];
    if (is_string($value)) {
        $value = strtolower($value);
        return !in_array($value, ['false', '0', 'no', 'off', ''], true);
    }
    return (bool)$value;
}

$name = read_field('name');
$email = read_field('email');
$phone = read_field('phone');
$zip = read_field('zip');
$service = read_field('service');
$address = read_field('address');
$service_detail = read_field('service_detail');
$message = read_field('message', 1000);
$formName = read_field('form_name');
$source = read_field('source');
$city = read_field('city');
$sessionID = read_field('sessionID', 1000);
$cartRaw = $_POST['cart'] ?? '';
$cartTotalsRaw = $_POST['cart_totals'] ?? '';
$cartItems = json_decode($cartRaw, true);
$cartTotals = json_decode($cartTotalsRaw, true);
if (is_array($cartItems) && $cartItems) {
    $message .= "\n\nCart items:\n";
    foreach ($cartItems as $it) {
        $sku = preg_replace('/[^\w-]/', '', $it['sku'] ?? '');
        $qty = intval($it['quantity'] ?? 0);
        if ($sku && $qty > 0) {
            $message .= $sku . ' x ' . $qty . "\n";
        }
    }
}

if ($name === '' || ($email === '' && $phone === '') || $service === '' || $message === '') {
    echo json_encode(['code' => '03', 'data' => 'Please complete all fields of the form.', 'fields' => [$name, $email, $phone, $service, $message]]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['code' => '04', 'data' => 'Email entered is not valid.']);
    exit;
}

if ($phone !== '' && !preg_match('/^[0-9 +()-]{7,20}$/', $phone)) {
    echo json_encode(['code' => '06', 'data' => 'Phone number not valid.']);
    exit;
}

/*if ($zip !== '' && !preg_match('/^[0-9A-Za-z -]{3,10}$/', $zip)) {
    echo json_encode(['code' => '07', 'data' => 'ZIP code not valid.']);
    exit;
}*/

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'bizz';
$dbUser = getenv('DB_USER') ?: 'postgres';
$dbPass = getenv('DB_PASS') ?: 'masterroot';

$formNameDb = $formName !== '' ? $formName : 'Formulario sin nombre';
$formPayload = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'zip' => $zip,
    'service' => $service,
    'address' => $address,
    'service_detail' => $service_detail,
    'message' => $message,
    'form_name' => $formName,
    'source' => $source,
    'city' => $city,
    'client_type' => read_field('client_type'),
    'space_type' => read_field('space_type'),
    'space_status' => read_field('space_status'),
    'floor_level' => read_field('floor_level'),
    'access_notes' => read_field('access_notes'),
    'delivery_preference' => read_field('delivery_preference'),
    'delivery_notes' => read_field('delivery_notes'),
    'start_date' => read_field('start_date'),
    'timeframe' => read_field('timeframe'),
    'area_size' => read_field('area_size'),
    'rooms' => read_field('rooms'),
    'consent_custom_quote' => read_bool('consent_custom_quote'),
    'consent_whatsapp' => read_bool('consent_whatsapp'),
    'session_id' => $sessionID,
    'cart' => is_array($cartItems) ? $cartItems : ($cartRaw !== '' ? $cartRaw : null),
    'cart_totals' => is_array($cartTotals) ? $cartTotals : ($cartTotalsRaw !== '' ? $cartTotalsRaw : null),
    'submitted_at' => gmdate('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
];

$formPayloadJson = json_encode($formPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if ($formPayloadJson === false) {
    echo json_encode(['code' => '02', 'data' => 'A error occurred while processing the form data. Please try again later.']);
    exit;
}

try {
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $dbHost, $dbPort, $dbName);
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare('INSERT INTO bizz.data_formularios (nombre_formulario, area_datos_recolectados) VALUES (:nombre_formulario, :area_datos_recolectados)');
    $stmt->bindValue(':nombre_formulario', $formNameDb, PDO::PARAM_STR);
    $stmt->bindValue(':area_datos_recolectados', $formPayloadJson, PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    $error = $e->getMessage();
    echo json_encode(['code' => '02', 'data' => 'A error occurred while saving the form. Please try again later - '+$error+'' ] );
    exit;
}

unset($pdo, $stmt);

$subject = 'New Request. Service: ' . $service . ' | Email: ' . $email . ' | Date: ' . date('d/m/Y') . ' | Time: ' . date('H:i:s');
$subjectCopy = 'B&S Interior Design. Thanks for your request. ' . $service;

$replyTo = preg_replace('/[\r\n]+/', '', $email);
$from = getenv('MAIL_FROM') ?: 'info@bsfloorsupply.com';
$to = getenv('MAIL_TO') ?: 'info@bsfloorsupply.com';

$osFamily = defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : PHP_OS;
if (stripos($osFamily, 'Windows') === 0) {
    @ini_set('sendmail_from', $from);
}

ob_start();
include __DIR__ . '/email-template.php';
$body = ob_get_clean();

$headers = 'From: "B&S Interior Design" <' . $from . ">\r\n";
$headers .= 'Reply-To: ' . $replyTo . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

if (mail($to, $subject, $body, $headers)) {
    $headersCopy = 'From: "B&S Interior Design" <' . $from . ">\r\n";
    $headersCopy .= "MIME-Version: 1.0\r\n";
    $headersCopy .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headersCopy .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
    mail($replyTo, $subjectCopy, $body, $headersCopy);
    echo json_encode(['code' => '01', 'data' => 'Request sent successfully. Thank you! We will contact you soon.']);
} else {
    echo json_encode(['code' => '02', 'data' => 'A error occurred while sending the form. Please try again later.']);
}
