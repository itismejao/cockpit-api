<?php

namespace App\Http\Resources\Auth;

use App\Models\AppVersion;
use Illuminate\Http\Resources\Json\Resource;

class UserCombatResource extends Resource
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
            'apps'  => [
                'android' => AppVersion::select('version', 'app_url')->where('platform', 'android_combat')->first(),
                'ios' => AppVersion::select('version', 'app_url')->where('platform', 'ios_combat')->first()
            ]
        ];
    }
}
