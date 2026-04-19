<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixCommunityMenuActions extends Migration
{
    // Map label keywords → correct action paths for HVN pages
    private $fixes = [
        'community' => '/community',
        'creators'  => '/creators',
        'creator'   => '/creators',
    ];

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
                $label = strtolower($item['label'] ?? '');
                foreach ($this->fixes as $keyword => $correctPath) {
                    if (str_contains($label, $keyword)) {
                        if (($item['action'] ?? '') !== $correctPath) {
                            $item['action'] = $correctPath;
                            $item['type']   = 'link';
                            $changed = true;
                        }
                        break;
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
        // intentionally irreversible
    }
}
