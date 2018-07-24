<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class SellerController
 * @package App\Http\Controllers\Indicators
 */
class ProfileController extends ApiController
{

    /**
     * Obtem o menu do App Android do usuário
     * @param Request $request
     * @param $sellerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function androidMenu(Request $request, $sellerId)
    {
        DB::setDateFormat('dd/mm/rrrr');

        try {
            $menu = DB::table('nmlabs.vw_ice_menu')
                ->where(function ($query) use ($sellerId, $request) {

                    if ($request->has('menu_id') and ctype_digit($request->get('menu_id'))) {
                        $query->whereRaw("nmlabs.pack_ice_admin.func_set_menu({$sellerId}, {$request->get('menu_id')}) = 1");
                    } else {
                        $query->whereRaw("nmlabs.pack_ice_admin.func_set_menu({$sellerId}) = 1");
                    }
                })
                ->get();

            return response()->json($this->dataFormat($menu));

        } catch (\Exception $e) {
            return response()->json($this->error(), 500);
        }
    }

}
