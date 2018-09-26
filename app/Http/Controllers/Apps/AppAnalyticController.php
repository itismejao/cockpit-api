<?php

namespace App\Http\Controllers\Apps;

use App\AppAnalytic;
use App\Http\Controllers\Controller;

class AppAnalyticController extends Controller
{
    
    public function index() {

        $data = [
            'count_ios_total' => AppAnalytic::where('platform', 'ios')->count(),
            'count_ios_verified' => AppAnalytic::where('platform', 'ios')->where('verified', true)->count(),
            'count_android_total' => AppAnalytic::where('platform', 'android')->count(),
            'count_android_verified' => AppAnalytic::where('platform', 'android')->where('verified', true)->count()
        ];

        return view('apps.analytics', compact('data'));
    }

}
