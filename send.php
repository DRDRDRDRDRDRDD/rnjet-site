<?php
// Обработчик формы заявки. Нужен хостинг с PHP и работающей функцией mail().
// Куда приходят заявки (при необходимости замените адрес):
$to = 'rn@insoftretail.ru';

function clean($s) { return trim(str_replace(["\r", "\n", "%0a", "%0d"], ' ', strip_tags((string)$s))); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.html'); exit; }
if (!empty($_POST['website'])) { header('Location: spasibo.html'); exit; } // защита от спам-ботов

$name    = clean($_POST['name'] ?? '');
$company = clean($_POST['company'] ?? '');
$contact = clean($_POST['contact'] ?? '');
$message = trim(strip_tags((string)($_POST['message'] ?? '')));

if ($name === '' || $contact === '' || empty($_POST['consent'])) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Заполните обязательные поля и дайте согласие на обработку данных.';
    exit;
}

$host    = preg_replace('/[^a-z0-9.\-]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
$page    = clean($_SERVER['HTTP_REFERER'] ?? '');
$subject = '=?UTF-8?B?' . base64_encode('Заявка с сайта ' . $host) . '?=';
$body    = "Имя: $name\nКомпания: $company\nКонтакт: $contact\n\nСообщение:\n$message\n\n"
         . "Согласие на обработку персональных данных: да\n"
         . "Страница: $page\nДата: " . date('Y-m-d H:i:s') . "\n";
$headers = "From: noreply@$host\r\nContent-Type: text/plain; charset=UTF-8\r\n";

header('Location: ' . (mail($to, $subject, $body, $headers) ? 'spasibo.html' : 'index.html?error=1#contact'));
exit;
