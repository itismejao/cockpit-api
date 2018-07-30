<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class IndexController
 * @package App\Http\Controllers\Indicators
 */
class IndexController extends ApiController
{

    /**
     * @param Request $request
     * @param $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(Request $request)
    {
        $uid = Auth::user()->uid;

        $json = $request->getContent();

        Validator::make(
            ['json' => $json],
            ['json' => 'json']
        )->validate();

        try {
            $result = $response = '';

            $pdo = DB::connection('oracle')->getPdo();

            $stmt = $pdo->prepare("begin :result := nmlabs.pack_cockpit_admin.func_api_cockpit(:uid, :json); end;");
            $stmt->bindParam(':result', $result, \PDO::PARAM_INT);
            $stmt->bindParam(':uid', $uid, \PDO::PARAM_INT);
            $stmt->bindParam(':json', $json, \PDO::PARAM_STR, 1000000);
            $stmt->execute();

            return response()->json(json_decode($json), ($result === 1) ? 200 : 400);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => 'Aplication error'], 500);
        }
    }

}