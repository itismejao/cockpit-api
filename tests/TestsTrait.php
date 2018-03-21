<?php

namespace Tests;

trait TestsTrait {

    /**
     * @return string
     */
    public function getToken()
    {
        $userUid = env('LDAP_USER_UID', null);

        if ($userUid === null) {
            return '';
        }

        $user = \App\User::where('uid', $userUid)->first();

        if (! $user) {
            $user = \App\User::create([
                'uid'   => $userUid,
                'Name'  => 'Fake User',
                'email' => 'teste@teste.com.br',
                'password' => bcrypt('test'),
                'api_token' => str_random()
            ]);
        }

        $user->generateToken();

        return $user->api_token;
    }
}