<?php
namespace Samanzamani\Api;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

    protected $table = 'events';

    public static function isHoliday($dayID) : bool {
        return Event::where([['day_id',$dayID],['is_holiday',1]])->exists();
    }

    public static function getDay($dayID,$timestamp) : array {
        $isHoliday = false;
        $dayEvents = Event::whereDayId($dayID)->get();
        $result = [];
        foreach ($dayEvents as $event){
            if ($event->is_holiday === 1){
                $isHoliday = true;
                $result['cause'] = $event->event;
            }
            $result['events'][] = $event->event;
        }
        $result['day_id'] = $dayID;
        $result['date'] = date('Y-m-d',$timestamp);
        $result['holiday'] = $isHoliday;
        return $result;
    }

}