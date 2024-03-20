# Jalali Calendar Event API
This part is a PHP API which is used to implement persianholiday.site website. You can clone the project and use it on your server or use the API that is provided on the persianholiday.site website.

## Note
The `perssianholiday.site` website is a free service and I do not guarantee the availability of the service. You can use the API on your server or use the SQLite file that is available in the repository. I highly recommend using the SQLite file because it is easy to use and you can use it on almost any platform and development project. Since keeping the API online is not free I do not guarantee the availability of the service.

## Requirements
* PHP 8.2 or higher
* SQLite3
* Composer

## Data structure
The `JSON` data structure is like below:
```json
{
  "cause": "جشن نوروز/جشن سال نو",
  "events": [
    "جشن نوروز/جشن سال نو",
    "روز جهانی شادی [ 20 March ]"
  ],
  "day_id": "14030101",
  "date": "2024-03-20",
  "holiday": true
}
```
| Key | type             | Description                 | example   |
| ------------- |------------------|-----------------------------|----------- |
| cause  | `TEXT`  | The event cause the holiday | جشن نوروز/جشن سال نو     |
| events | `ARRAY`          |  An array of events on that day    | ["جشن نوروز/جشن سال نو", "روز جهانی شادی [ 20 March ]"] |
| day_id | `TEXT`           |  an ID for day which build with 4 digit of year + 2 digits of months and 2 digits of day  | 14030101 |
| date | `DATE`           |  Date in format YYYY-mm-dd    | 2024-03-20 |
| holiday | `BOOL`           |  0 or 1 value shows it is a holiday or not   | 1 |

<abbr title="Hyper Text Markup Language">NOTE:</abbr>  If the day is not a holiday the `cause` not exists.


## How to use
All routes respond to `GET` and `POST` requests.
### Routes
* `/api/v1/today` : Get today's events
* `/api/v1/yesterday` : Get yesterday's events
* `/api/v1/tomorrow` : Get tomorrow's events

These routes return a `JSON` event according to the [data structure](#data-structure).
You can set the `raw` parameter to these routes to get just a `BOOL` 0 or 1 value that shows if the day is a holiday or not.
#### example
If you want to check if today holiday or not just send a `GET` request to `/api/v1/today` and check the response.

* `/api/v1/day` : Get events of a specific date
  This route gets a parameter date with the format `YYYYMMDD` and response to the events of that day.
#### example
If you want to get `1402/02/26` events a request to `/api/v1/day?date=14020226` and check the response (You can send `POST` request too),

* `/api/v1/range` : Get events of a specific date range
  This route gets two parameters `start_date` and `end_date` with the format `YYYYMMDD` and responds to the events of that range. The data range should be less than 365 days.
#### example
If you want to get events of `1402/02/26` to `1402/03/01` a request to `/api/v1/range?start_date=14020226&end_date=14020301` and check the response.(You can send `POST` request too),

### persianholiday.site routes list
Description  | URL
------------- | -------------
Today events  | [https://persianholiday.site/api/v1/today](https://persianholiday.site/api/v1/today)
Is today a holiday?  | [https://persianholiday.site/api/v1/today?raw](https://persianholiday.site/api/v1/today?raw)
Yesterday events  | [https://persianholiday.site/api/v1/yesterday](https://persianholiday.site/api/v1/yesterday)
Is yesterday a holiday?  | [https://persianholiday.site/api/v1/yesterday?raw](https://persianholiday.site/api/v1/yesterday?raw)
Today events  | [https://persianholiday.site/api/v1/tomorrow](https://persianholiday.site/api/v1/tomorrow)
Is tomorrow a holiday?   | [https://persianholiday.site/api/v1/tomorrow?raw](https://persianholiday.site/api/v1/tomorrow?raw)
Events of a specific date  | [https://persianholiday.site/api/v1/day?date=14020226](https://persianholiday.site/api/v1/day?date=14020226)
Events of a specific date range  | [https://persianholiday.site/api/v1/range?start_date=14020226&end_date=14020301](https://persianholiday.site/api/v1/range?start_date=14020226&end_date=14020301)
