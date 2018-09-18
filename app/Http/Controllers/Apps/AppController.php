<?php

namespace App\Http\Controllers\Apps;

use App\Models\AppVersion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('apps.list', ['apps' => AppVersion::all()]);
    }

    /**
     * @param Request $request
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|int',
                'version' => 'required|string',
                'app_url' => 'required|string'
            ]);

            $app = AppVersion::find($request->get('id'));

            if (! $app) {
                throw new \Exception('App not found');
            }

            $app->version = $request->get('version');
            $app->app_url = $request->get('app_url');

            $app->save();

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json([], 400);
        }

    }


}
