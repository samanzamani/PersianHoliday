<?php
//add autoload
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => '../persian_holiday.db',
]);
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
$capsule->bootEloquent();