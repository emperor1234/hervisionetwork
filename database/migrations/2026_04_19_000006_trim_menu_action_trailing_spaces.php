<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TrimMenuActionTrailingSpaces extends Migration
{
    public function up()
    {
        $row = DB::table('settings')->where('name', 'menus')->first();
        if (!$row) return;

        $menus = json_decode($row->value, true);
        if (!is_array($menus)) return;

        $changed = false;
        foreach ($menus as &$menu) {
            if (!isset($menu['items'])) continue;
            foreach ($menu['items'] as &$item) {
                if (isset($item['action'])) {
                    $clean = rtrim($item['action']);
                    if ($clean !== $item['action']) {
                        $item['action'] = $clean;
                        $changed = true;
                    }
                }
            }
        }

        if ($changed) {
            DB::table('settings')
                ->where('name', 'menus')
                ->update(['value' => json_encode($menus)]);
        }
    }

    public function down()
    {
        // intentionally irreversible — trailing spaces were bugs
    }
}
