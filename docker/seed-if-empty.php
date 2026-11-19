<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

if (DB::table('users')->count() === 0) {
    echo "Seeding database...\n";
    Artisan::call('db:seed', ['--force' => true]);
    echo Artisan::output();
} else {
    echo "Database sudah berisi data, skip seeding.\n";
}
