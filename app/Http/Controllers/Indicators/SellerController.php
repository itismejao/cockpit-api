<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class SellerController
 * @package App\Http\Controllers\Indicators
 */
class SellerController extends ApiController
{

    /**
     * Indicador de vendas x meta do vendedor
     * @param Request $request
     * @param $sellerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function goalPercentage(Request $request, $sellerId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';

            $indicators = DB::connection('oracle')
                ->table('vw_ice_indicador_1_vend')
                ->whereRaw("pack_ice_indicadores.func_set_venda({$beginDate},{$endDate}, {$sellerId}, 0) = 1")
                ->get();

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json($this->error(), 500);
        }
    }

    /**
     * Indicador de vendas x meta do vendedor diário
     * @param Request $request
     * @param $sellerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function goalPercentageDaily(Request $request, $sellerId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';

            $indicators = DB::connection('oracle')
                ->table('vw_ice_indicador_1_vend')
                ->whereRaw("pack_ice_indicadores.func_set_venda_detalhe({$beginDate},{$endDate}, {$sellerId}) = 1")
                ->get();

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            return response()->json($this->error(), 500);
        }
    }

    /**
     * Indicadores de venda de serviços do vendedor
     * @param Request $request
     * @param $sellerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function services(Request $request, $accessLevel, $sellerId)
    {
        DB::connection('oracle')->setDateFormat('dd/mm/rrrr');

        try {
            $beginDate = $request->has('beginDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('beginDate'))->format('d/m/Y') . "'" : 'NULL';
            $endDate   = $request->has('endDate') ? "'" . Carbon::createFromFormat('Y-m-d', $request->get('endDate'))->format('d/m/Y') . "'" : 'NULL';
            $detail    = $request->has('detail') ? "'" . $request->get('detail') . "'" : 'NULL';
            $filter    = $request->has('filter') ? "'" . $request->get('filter') . "'" : 'NULL';

            DB::connection('oracle')->transaction(function () use ($beginDate, $endDate, &$indicators, $accessLevel, $sellerId, $detail, $filter) {

                DB::connection('oracle')->executeProcedure('proc_seta_filtro_comercial_v2', ['v_id_usuario' => $sellerId]);

                $indicators = DB::connection('oracle')
                    ->table('vw_ice_indicador_2_vend')
                    ->whereRaw("pack_ice_indicadores.func_set_venda({$accessLevel}, {$beginDate},{$endDate}, {$sellerId}, {$detail}, {$filter}) = 1")
                    ->get();

            });

            return response()->json($this->dataFormat($indicators));

        } catch (\Exception $e) {
            return response()->json($this->error(), 500);
        }
    }
}
