# Jalali Calendar Event
This project includes all Jalali calendar events from 1991 (1370-01-01) until 2051 (1430-12-29) which are mined from time.ir website.
## Project structure
The repository includes three sections:

Section  | Description
------------- | -------------
persian_holiday.db file  | This file includes all events and holidays
Api  | It contains a PHP API which is used to implement persianholiday.site website. you can read API documentation from [HERE](https://github.com/samanzamani/PersianHoliday/tree/main/Api "here") 
Data Miner  | This directory includes the PHP source code I used to mine event data from website time.ir. You can read more about this project from [HERE](https://github.com/samanzamani/PersianHoliday/tree/main/Data%20Miner "HERE")

## How to use (Recommended)
* If you work on a real project and you want to release the project use SQLite file.
* If you work on a small development project you can use the API that is provided on the persianholiday.site website.

## Why SQLite
It is easy to use and you can use it on almost any platform and development project. Plus you can easily delete the part you do not need and reduce SQL file size. You can use online tools to check the data.

## Database structure
The SQL file contains two tables: `version` and `events`.

### version table
This table contains one row that shows a numeric version and data range. Right now the version is 10 and data is available from 1991 (1370-01-01) until 2051 (1430-12-29).

### events table
This table has all events with the below columns:

| Column | type | Description  | example   |
| ------------- | ----------- |--------------------------------------|----------- |
| day_id  |   `TEXT` | an ID for day which build with 4 digit of year + 2 digits of months and 2 digits of day  | 14000204     |
| year | `INTEGER`  |  Jalali year with 4 digits    | 1402 |
| month | `INTEGER`  |  Jalali month with 1 or 2 digits    | 1 |
| day | `INTEGER`  |  Jalali day with 1 or 2 digits    | 5 |
| event | `TEXT`  |  event day description    | سال نو |
| is_holiday | `INTEGER`  |  0 or 1 value shows it is a holiday or not   | 1 |
| date | `DATE`  |  Date in formay YYYY-mm-dd    | 2024-02-24 |

<abbr title="Hyper Text Markup Language">NOTE:</abbr>  `day_id` is not a unique value because a day could have more than one event.

#### example
If you want to check if `1402/02/26` holiday or not just check if is there any row with `day_id=14020226` AND `is_holiday=1`.
