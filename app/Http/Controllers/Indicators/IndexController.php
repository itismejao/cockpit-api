<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function run(Request $request, $uid)
    {
        $request->validate([
            'json' => 'required|json'
        ]);

        try {
            $json = $request->get('json');

            $result = $response = '';

            $pdo = DB::connection('oracle')->getPdo();

            $stmt = $pdo->prepare("begin :result := nmlabs.pack_cockpit_admin.func_api_cockpit(:uid, :json); end;");
            $stmt->bindParam(':result', $result, \PDO::PARAM_INT);
            $stmt->bindParam(':uid', $uid, \PDO::PARAM_INT);
            $stmt->bindParam(':json', $json, \PDO::PARAM_STR, 1000000);
            $stmt->execute();

            return response()->json(json_decode($json));
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => 'Aplication error'], 500);
        }
    }

}