<?php

namespace App\Http\Resources\Auth;

use Facades\App\Http\Services\Vtrine as VtrineService;
use Illuminate\Http\Resources\Json\Resource;

class LdapUserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uid'   => $this->uid,
            'name'  => $this->name,
            'email' => $this->email,
            'token' => $this->api_token,
            'menu'  => VtrineService::getMenuList($this->uid)
        ];
    }
}
