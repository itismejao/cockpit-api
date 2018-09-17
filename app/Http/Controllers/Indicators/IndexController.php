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
            $result = '';

            $pdo = DB::connection('oracle')->getPdo();

            $data = $json;

            $stmt = $pdo->prepare("declare v_id_funcionario number(10) := :uid; v_dados clob := :data; begin :result := nmlabs.pack_cockpit_admin.func_api_cockpit(v_id_funcionario, v_dados); :json := v_dados; end;");
            $stmt->bindParam(':result', $result, \PDO::PARAM_INT);
            $stmt->bindParam(':uid', $uid, \PDO::PARAM_INT);
            $stmt->bindParam(':data', $data, \PDO::PARAM_STR);
            $stmt->bindParam(':json', $json, SQLT_CLOB);
            $stmt->execute();

            return response()->json(json_decode(print $json->load()), ($result === 1) ? 200 : 400);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => 'Aplication error'], 500);
        }
    }

}