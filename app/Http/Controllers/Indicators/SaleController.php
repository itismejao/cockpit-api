<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SellerController
 * @package App\Http\Controllers\Indicators
 */
class SaleController extends ApiController
{

    /**
     * Indicador de vendas x meta Geral
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function goalPercentage(Request $request, $accessLevel, $userId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        $request->validate([
            'beginDate' => 'date',
            'endDateDate' => 'date',
            'detail' => 'required',
        ]);

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';
            $detail    = $request->has('detail') ? "'" . $request->get('detail') . "'" : 'NULL';
            $filter    = $request->has('filter') ? "'" . $request->get('filter') . "'" : 'NULL';

            DB::connection('oracle')->transaction(function () use ($beginDate, $endDate, &$indicators, $accessLevel, $userId, $detail, $filter) {

                DB::connection('oracle')->executeProcedure('nm.proc_seta_filtro_comercial_v2', ['v_id_usuario' => $userId]);

                $indicators = DB::connection('oracle')
                    ->table('nmlabs.vw_ice_ind_vend_1')
                    ->whereRaw("nmlabs.pack_ice_indicadores.func_set_venda({$accessLevel}, {$beginDate},{$endDate}, {$userId}, {$detail}, {$filter}) = 1")
                    ->get();
            });

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            report($e);
            return response()->json($this->error(), 500);
        }
    }

    /**
     * Busca informações de vendas geral setando o filtro por usuário usuário
     * @param $userId
     * @return JsonResponse
     */
    public function goalPercentageWithFilter($userId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        try {
            $indicators = DB::connection('oracle')
                ->table('nmlabs.vw_ice_ind_vend_filtro')
                ->whereRaw("nmlabs.pack_ice_venda.func_seta_filtro({$userId}) = 1")
                ->get();

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            report($e);
            return response()->json($this->error(), 500);
        }
    }

    /**
     * Busca os departamentos
     * @return JsonResponse
     */
    public function getDepartments()
    {
        try {
            $indicators = DB::connection('oracle')
                ->table('nmlabs.vw_ice_departamentos')
                ->get();

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            report($e);
            return response()->json($this->error(), 500);
        }
    }


    /**
     * Indicador de vendas x meta Geral
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function generalSales(Request $request, $accessLevel, $userId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        $request->validate([
            'beginDate' => 'date',
            'endDate' => 'date',
            'indicator' => 'int',
            'filter' => 'int',
            'dept' => 'int',
            'ld' => 'int',
        ]);

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';
            $indicator = $request->has('indicator') ? "'" . $request->get('indicator') . "'" : 'NULL';
            $filter    = $request->has('filter') ? "'" . $request->get('filter') . "'" : 'NULL';
            $dept      = $request->has('dept') ? "'" . $request->get('dept') . "'" : 'NULL';
            $ld        = $request->has('ld') ? "'" . $request->get('ld') . "'" : 'NULL';

            DB::connection('oracle')->transaction(function () use ($beginDate, $endDate, &$indicators, $accessLevel, $userId, $indicator, $filter, $dept, $ld) {

                DB::connection('oracle')->executeProcedure('nm.proc_seta_filtro_comercial_v2', ['v_id_usuario' => $userId]);

                $indicators = DB::connection('oracle')
                    ->table('nmlabs.vw_ice_venda')
                    ->whereRaw("nmlabs.pack_ice_venda.func_seta_venda({$accessLevel}, {$indicator}, {$beginDate}, {$endDate}, {$userId}, {$filter}, {$dept}, {$ld}) = 1")
                    ->get();
            });

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            report($e);
            return response()->json($this->error(), 500);
        }
    }
    
    /**
     * Indicador de vendas x meta Geral de Servicos
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function generalSalesServices(Request $request, $accessLevel, $userId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        $request->validate([
            'beginDate' => 'date',
            'endDate' => 'date',
            'indicator' => 'int',
            'filter' => 'int',
            'dept' => 'int',
        ]);

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';
            $indicator = $request->has('indicator') ? "'" . $request->get('indicator') . "'" : 'NULL';
            $filter    = $request->has('filter') ? "'" . $request->get('filter') . "'" : 'NULL';
            $dept      = $request->has('dept') ? "'" . $request->get('dept') . "'" : 'NULL';
            $ld        = 'NULL';

            DB::connection('oracle')->transaction(function () use ($beginDate, $endDate, &$indicators, $accessLevel, $userId, $indicator, $filter, $dept, $ld) {

                DB::connection('oracle')->executeProcedure('nm.proc_seta_filtro_comercial_v2', ['v_id_usuario' => $userId]);

                $indicators = DB::connection('oracle')
                    ->table('nmlabs.vw_ice_venda_serv')
                    ->whereRaw("nmlabs.pack_ice_venda.func_seta_venda({$accessLevel}, {$indicator}, {$beginDate}, {$endDate}, {$userId}, {$filter}, {$dept}, {$ld}) = 1")
                    ->get();
            });

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            report($e);
            return response()->json($this->error(), 500);
        }
    }

}
