<?php

namespace App\Services;
use App\Models\ServerFitur;


class RouteService
{
    public function getDynamicRoutes()
    {
        return ServerFitur::whereHas('fiturLevel', function ($query) {
            $query->where('KodeLevel', 1)
                  ->where('ViewData', 1)
                  ->whereHas('aksesLevel', function ($q) {
                      $q->where('IsAktif', 1);
                  });
        })
        ->where('IsAktif', 1)
        ->get();
    }
}
