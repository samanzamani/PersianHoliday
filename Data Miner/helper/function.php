<?php

/**
 * Get number of days in a month from a persian title
 *
 * @param $number
 * @return int|false
 */
function numberToEnglish($number) : int|false{
    //convert all persian and arabic numbers to english
    $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
    //get first two characters of the number
    $number = mb_substr($number, 0, 2);
    $number = str_replace($persian, range(0, 9), $number);
    $number = str_replace($arabic, range(0, 9), $number);
    //remove all non-numeric characters
    $number = preg_replace('/[^0-9]/', '', $number);
    //check if the number is numeric
    if(!is_numeric($number)){
        return false;
    }
    return $number;
}

/**
 * Convert english numbers to persian
 *
 * @param $number
 * @return string
 */
function englishToPersian($number) : string{
    $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return str_replace(range(0, 9), $persian, $number);
}

function clearMonthNameFromEvent(String $event,int $monthIndex,int $day) : string {
    $monthNames = [
        'فروردین',
        'اردیبهشت',
        'خرداد',
        'تیر',
        'مرداد',
        'شهریور',
        'مهر',
        'آبان',
        'آذر',
        'دی',
        'بهمن',
        'اسفند'
    ];
    $persianDay = englishToPersian($day);
    //remove -holiday from the end of the event
    $event = str_replace('-holiday', '', $event);
    return str_replace($persianDay.' '.$monthNames[$monthIndex].' ', '', $event);
}