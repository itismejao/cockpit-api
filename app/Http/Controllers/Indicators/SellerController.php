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

            $indicators = DB::table('VW_ICE_INDICADOR_1_VEND')
                ->whereRaw("PACK_ICE_INDICADORES.FUNC_SET_VENDA_VENDEDOR({$beginDate},{$endDate}, {$sellerId}) = 1")
                ->get();

            return response()->json($this->indicatorsFormat($indicators));

        } catch (\Exception $e) {
            return response()->json($this->error(), 500);
        }

    }

}
