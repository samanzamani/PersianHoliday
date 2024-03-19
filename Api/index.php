<?php

use JetBrains\PhpStorm\NoReturn;

require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_uri = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($request_uri);
    $path = $parsed_url['path'];

    if ($path === '/api/v1/today') {
        today();
    }
    if ($path === '/api/v1/yesterday') {
        yesterday();
    }
    if ($path === '/api/v1/tomorrow') {
        tomorrow();
    }
    if ($path === '/api/v1/day') {
        day();
    }
    if ($path === '/api/v1/range') {
        date_range();
    }
}

#[NoReturn] function yesterday(): void
{
    $yesterday = \Carbon\Carbon::yesterday()->timestamp;
    $dayID = jdate('Ymd', $yesterday, '', '', 'en');
    if (isset($_REQUEST['raw'])) {
        echo \Samanzamani\Api\Event::isHoliday($dayID) ? '1' : '0';
        exit(0);
    }
    response(\Samanzamani\Api\Event::getDay($dayID, $yesterday));
}

#[NoReturn] function tomorrow(): void
{
    $tomorrow = \Carbon\Carbon::tomorrow()->timestamp;
    $dayID = jdate('Ymd', $tomorrow, '', '', 'en');
    if (isset($_REQUEST['raw'])) {
        echo \Samanzamani\Api\Event::isHoliday($dayID) ? '1' : '0';
        exit(0);
    }
    response(\Samanzamani\Api\Event::getDay($dayID, $tomorrow));
}

#[NoReturn] function today(): void
{
    $today = \Carbon\Carbon::today()->timestamp;
    $dayID = jdate('Ymd', $today, '', '', 'en');
    if (isset($_REQUEST['raw'])) {
        echo \Samanzamani\Api\Event::isHoliday($dayID) ? '1' : '0';
        exit(0);
    }
    response(\Samanzamani\Api\Event::getDay($dayID, $today));
}

#[NoReturn] function day(): void
{
    $day = (int)$_REQUEST['date'];
    if (strlen((string)$day) != 8) {
        error("تاریخ درخواستی باید به صورت سال‌ماه‌روز به شکل یک عدد ۸ رقمی می‌بایست ارسال شود(مثال 14050505)");
    }
    if ($day > 14301229 || $day < 13700101) {
        error("اطلاعات درخواستی خارج از محدوده تاریخ سال ۱۳۷۰ تا ۱۴۳۰ می‌باشد");
    }
    $year = substr($day, 0, 4);
    $month = substr($day, 4, 2);
    $dayItem = substr($day, 6, 2);
    $timestamp = jmktime(0, 0, 0, $month, $dayItem, $year);

    response(\Samanzamani\Api\Event::getDay($day, $timestamp));

}


function date_range()
{
    $startDate = (int)$_REQUEST['start_date'];
    $endDate = (int)$_REQUEST['end_date'];
    if (strlen((string)$startDate) != 8) {
        error("تاریخ شروع باید به صورت سال‌ماه‌روز به شکل یک عدد ۸ رقمی می‌بایست ارسال شود(مثال 14050505)");
    }
    if ($startDate > 14301229 || $startDate < 13700101) {
        error("تاریخ شروع خارج از محدوده تاریخ سال ۱۳۷۰ تا ۱۴۳۰ می‌باشد");
    }
    if (strlen((string)$endDate) != 8) {
        error("تاریخ پایان باید به صورت سال‌ماه‌روز به شکل یک عدد ۸ رقمی می‌بایست ارسال شود(مثال 14050505)");
    }
    if ($endDate > 14301229 || $endDate < 13700101) {
        error("تاریخ پایان خارج از محدوده تاریخ سال ۱۳۷۰ تا ۱۴۳۰ می‌باشد");
    }
    if($startDate > $endDate){
        error("تاریخ شروع از تاریخ پایان بزرگتر است");
    }
    $year = substr($startDate, 0, 4);
    $month = substr($startDate, 4, 2);
    $dayItem = substr($startDate, 6, 2);
    $startDateTimestamp = jmktime(0, 0, 0, $month, $dayItem, $year);
    $year = substr($endDate, 0, 4);
    $month = substr($endDate, 4, 2);
    $dayItem = substr($endDate, 6, 2);
    $endDateTimestamp = jmktime(0, 0, 0, $month, $dayItem, $year);
    $startDateCarbon = \Carbon\Carbon::createFromTimestamp($startDateTimestamp);
    $endDateCarbon = \Carbon\Carbon::createFromTimestamp($endDateTimestamp);
    $ret = [];
    $diffInDays = $startDateCarbon->diffInDays($endDateCarbon);
    if($diffInDays > 365){
        error("حداکثر اطلاعاتی درخواستی در یک درخواست، یکسال می‌باشد.");
    }

    while ($endDateCarbon->gt($startDateCarbon)){
        $dayID = jdate('Ymd', $startDateCarbon->timestamp, '', '', 'en');
        $ret[$dayID] = \Samanzamani\Api\Event::getDay($dayID, $startDateCarbon->timestamp);
        $startDateCarbon->addDay();
    }

    response($ret);
}

#[NoReturn]
function response(array $data): void
{
    header('Content-Type: application/json');
    $json_response = json_encode($data);
    echo $json_response;
    exit(0);
}

#[NoReturn] function error($message): void
{
    http_response_code(400); // Set HTTP response code to 400 (Bad Request)

    // Prepare error response as an associative array
    $response = array(
        'error' => true,
        'message' => $message
    );

    // Encode the error response as JSON
    $json_response = json_encode($response);

    // Return the JSON response
    echo $json_response;
    exit(0);
}