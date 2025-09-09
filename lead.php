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

$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = @new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['code' => '02', 'data' => 'A error occurred while connecting to the database. Please try again later.']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO website_requests (name, email, phone, service, message, form_name, source, status, city) VALUES (?, ?, ?, ?, ?, ?, ?,\'Pending\', ?)');
if (!$stmt) {
    echo json_encode(['code' => '02', 'data' => 'A error occurred while preparing the query. Please try again later.']);
    $conn->close();
    exit;
}

$stmt->bind_param('ssssssss', $name, $email, $phone, $service, $message, $formName, $source, $city);
if (!$stmt->execute()) {
    echo json_encode(['code' => '02', 'data' => 'A error occurred while sending the form. Please try again later.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();
$conn->close();

$subject = 'New Request. Service: ' . $service . ' | Email: ' . $email . ' | Date: ' . date('d/m/Y') . ' | Time: ' . date('H:i:s');
$subjectCopy = 'B&S Interior Design. Thanks for your request. ' . $service;

$replyTo = preg_replace('/[\r\n]+/', '', $email);
$from = getenv('MAIL_FROM') ?: 'info@bsglobalservices.com';
$to = getenv('MAIL_TO') ?: 'info@bsglobalservices.com';

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
