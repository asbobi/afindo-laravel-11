<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    private $akses;


    public function getAkses(){
        $allAkses = cache()->get('akses_user');
        if ($allAkses == null) {
            return [];
        }
        foreach ($allAkses as $akses) {
            $currentUrl = request()->url();
            if (strpos($currentUrl, $akses->Slug) !== false) {
                return $akses;
            }
        }
    }
}
