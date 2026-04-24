<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class ClearRouteCache extends Migration
{
    public function up()
    {
        Artisan::call('route:clear');
        Artisan::call('config:clear');
    }

    public function down() {}
}
