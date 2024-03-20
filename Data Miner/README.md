# Jalali Calendar Event Data Miner
This directory includes the PHP source code I used to mine event data from website time.ir.

## Where does the data come from?
The data is mined from the [time.ir](https://www.time.ir) website. On this website, there is an annual event list for each year. I store `HTML` 30 years between 1370 and 1400 in the `src` directory. The `HTML` files are named with the year number. For example, the `HTML` file for the year 1399 is named `1399.html`.

### Why Store HTML files?
The website has a lot of traffic and it is not a good idea to send a request to the website for each day. So I store the `HTML` files and use them to get the events. On the other hand, the website is protected by `CSRF` token and it is not a good idea to send a request to the website for each day.

### Can I extend the data range?
Yes, you can. Just send a request to the website [in this page](https://www.time.ir/fa/eventyear-%d8%aa%d9%82%d9%88%db%8c%d9%85-%d8%b3%d8%a7%d9%84%db%8c%d8%a7%d9%86%d9%87) and store the `HTML` file in the `src` directory. Then run the `index.php` file to get the events.
```php
php index.php
```
The `index.php` file will read the `HTML` files get the events and store them in the `persian_holiday.db` file.
Note that you need PHP `8.2` or higher and `SQLite3` to run the `index.php` file.