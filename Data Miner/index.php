<?php
//load autoload
require_once 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

//version of database
$version = 10;

// URL to fetch
//$url = 'https://www.time.ir/fa/eventyear-%D8%AA%D9%82%D9%88%DB%8C%D9%85-%D8%B3%D8%A7%D9%84%DB%8C%D8%A7%D9%86%D9%87';
//route protected by CSRF token, so it is not possible to fetch data from this URL. I store the HTML content in a file and use it.

//get list files in the src folder
$files = scandir('src');
//filter files and just get html files
$htmlFiles = array_filter($files, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) == 'html';
});
foreach ($htmlFiles as $file) {
    //get year from file name
    $i = (int)pathinfo($file, PATHINFO_FILENAME);
    //get html content
    $file = file_get_contents('src/' . $file);
    // Create a new Symfony DomCrawler instance
    $crawler = new Crawler($file);

    //get all months in a year
    $count = $crawler->filter('ul.list-unstyled')->count();

    $result = [];
    if ($count == 12) {
        $months = $crawler->filter('ul.list-unstyled')->each(function (Crawler $node, $i) {
            $month = $node->filter('li')->each(function (Crawler $node, $i) {
                //check li has eventHoliday class
                if (str_contains($node->attr('class'), 'eventHoliday')) {
                    return $node->text() . '-holiday';
                }
                return $node->text();
            });
            return $month;
        });
        //print all months
        foreach ($months as $month_index => $month) {
            foreach ($month as $day) {
                $dayNumber = numberToEnglish($day);
                //check if the day is holiday
                $isHoliday = str_contains($day, '-holiday');
                $dayEvent = clearMonthNameFromEvent($day, $month_index, $dayNumber);
                $result[$month_index][] = [
                    'ID' => $i.str_pad((string) ($month_index+1),2,'0',STR_PAD_LEFT).str_pad((string) $dayNumber,2,'0',STR_PAD_LEFT),
                    'day' => $dayNumber,
                    'event' => $dayEvent,
                    'isHoliday' => $isHoliday,
                    'date' => date('Y-m-d',jmktime(0, 0, 0, $month_index + 1, $dayNumber, $i))//get date from persian date
                ];
            }
        }
        $finalYear[$i] = $result;
    } else {
        $finalYear[$i] = false;
    }
}
//remove old database if exists
if (file_exists('../persian_holiday.db')) {
    unlink('../persian_holiday.db');
}
//store final finalYear in a sqlite database
$db = new SQLite3('../persian_holiday.db');
//create a table for store version, Start and end date
$db->exec('CREATE TABLE IF NOT EXISTS version (version INTEGER PRIMARY KEY, start_jalali TEXT, end_jalali TEXT,start_date DATE,end_date DATE)');
$startDate = date('Y-m-d', jmktime(0, 0, 0, 1, 1, min(array_keys($finalYear))));
$endDate = date('Y-m-d', jmktime(0, 0, 0, 12, 29, max(array_keys($finalYear))));
$stmt = $db->prepare('INSERT INTO version (version, start_jalali, end_jalali,start_date,end_date) VALUES (:version, :start_jalali, :end_jalali,:start_date,:end_date)');
$stmt->bindValue(':version', $version, SQLITE3_INTEGER);
$stmt->bindValue(':start_jalali', min(array_keys($finalYear))."-01-01");
$stmt->bindValue(':end_jalali', max(array_keys($finalYear))."-12-29");
$stmt->bindValue(':start_date', $startDate);
$stmt->bindValue(':end_date', $endDate);
$stmt->execute();
//create a table for store events
$db->exec('CREATE TABLE IF NOT EXISTS events (id INTEGER PRIMARY KEY, day_id TEXT, year INTEGER, month INTEGER, day INTEGER, event TEXT, is_holiday INTEGER, date DATE)');

foreach ($finalYear as $year => $months) {
    if ($months) {
        foreach ($months as $month_index => $days) {
            foreach ($days as $day) {
                $stmt = $db->prepare('INSERT INTO events (day_id,year, month, day, event, is_holiday, date) VALUES (:day_id,:year, :month, :day, :event, :is_holiday, :date)');
                $stmt->bindValue(':day_id', $day['ID']);
                $stmt->bindValue(':year', $year, SQLITE3_INTEGER);
                $stmt->bindValue(':month', $month_index + 1, SQLITE3_INTEGER);
                $stmt->bindValue(':day', $day['day'], SQLITE3_INTEGER);
                $stmt->bindValue(':event', $day['event']);
                $stmt->bindValue(':is_holiday', $day['isHoliday'], SQLITE3_INTEGER);
                $stmt->bindValue(':date', $day['date']);
                $stmt->execute();
            }
        }
    }
}
$recordCount = $db->querySingle('SELECT COUNT(*) FROM events');
echo "--------------------------------\n
   ___              _                           _ _     _             
  / _ \___ _ __ ___(_) __ _ _ __     /\  /\___ | (_) __| | __ _ _   _ 
 / /_)/ _ \ '__/ __| |/ _` | '_ \   / /_/ / _ \| | |/ _` |/ _` | | | |
/ ___/  __/ |  \__ \ | (_| | | | | / __  / (_) | | | (_| | (_| | |_| |
\/    \___|_|  |___/_|\__,_|_| |_| \/ /_/ \___/|_|_|\__,_|\__,_|\__, |
                                                                |___/ 
";
echo "--------------------------------\n";
//show success message in terminal
echo "Database Created Successfully\n";
//show version and start and end date
echo "Version: $version\n";
echo "Start Date: $startDate\n";
echo "End Date: $endDate\n";
echo "Number of records: $recordCount\n";
exit(0);