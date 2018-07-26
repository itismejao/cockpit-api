<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

class Vtrine
{
    /**
     * @param $uid
     * @return mixed
     */
    public function collaboratorCanAccess($uid)
    {
        $result = $dados = '';

        $pdo = DB::connection('oracle')->getPdo();

        $stmt = $pdo->prepare("begin :result := nmlabs.pack_cockpit_admin.func_valida_acesso(:p_id_funcionario, :p_dados); end;");
        $stmt->bindParam(':result', $result, \PDO::PARAM_STR, 4000);
        $stmt->bindParam(':p_id_funcionario', $uid, \PDO::PARAM_INT);
        $stmt->bindParam(':p_dados', $dados, \PDO::PARAM_STR);
        $stmt->execute();

        return $result;
    }

    public function getMenuList($uid) {
        $json = json_encode(['tipo' => 'menu']);

        $result = $response = '';

        $pdo = DB::connection('oracle')->getPdo();

        $stmt = $pdo->prepare("begin :result := nmlabs.pack_cockpit_admin.func_api_cockpit(:uid, :json); end;");
        $stmt->bindParam(':result', $result, \PDO::PARAM_INT);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_INT);
        $stmt->bindParam(':json', $json, \PDO::PARAM_STR, 1000000);
        $stmt->execute();

        return json_decode($json);
    }
}