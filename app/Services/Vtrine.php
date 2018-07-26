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
        $dados = '';

        $pdo = DB::connection('oracle')->getPdo();
        $result = '';
        $stmt = $pdo->prepare("begin :result := nmlabs.pack_cockpit_admin.func_valida_acesso(:p_id_funcionario, :p_dados); end;");
        $stmt->bindParam(':result', $result, \PDO::PARAM_STR, 4000);
        $stmt->bindParam(':p_id_funcionario', $uid, \PDO::PARAM_INT);
        $stmt->bindParam(':p_dados', $dados, \PDO::PARAM_STR);
        $stmt->execute();

        return $result;
    }
}