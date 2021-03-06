<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'email', 'api_token', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function generateToken($ip)
    {
        $this->api_token = bcrypt(str_random(60) . $this->email . Carbon::now()->format('Y-m-d H:i'));
        $this->last_ip   = $ip;

        $this->newLastRequest();
    }

    public function newLastRequest()
    {
        $this->last_request = Carbon::now();
        $this->save();
    }
}
