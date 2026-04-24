<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePrimaryMenuPeopleNews extends Migration
{
    public function up()
    {
        $setting = DB::table('settings')->where('name', 'menus')->first();
        if (!$setting || !$setting->value) {
            return;
        }

        $menus = json_decode($setting->value, true);
        if (!is_array($menus)) {
            return;
        }

        foreach ($menus as &$menu) {
            if (!isset($menu['items'])) {
                continue;
            }
            foreach ($menu['items'] as &$item) {
                if (isset($item['action']) && $item['action'] === 'people') {
                    $item['label']  = 'Creators';
                    $item['action'] = 'creators';
                }
                if (isset($item['action']) && $item['action'] === 'news') {
                    $item['label']  = 'Community';
                    $item['action'] = 'community';
                }
            }
            unset($item);
        }
        unset($menu);

        DB::table('settings')
            ->where('name', 'menus')
            ->update(['value' => json_encode($menus)]);
    }

    public function down()
    {
        // Intentionally left blank — menu edits are user-managed
    }
}
