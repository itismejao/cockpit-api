<?php

namespace App\Http\Controllers\Auth\Ldap;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Auth\LdapUserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * LDAP Authentication Adapter
 * Class AuthController
 * @package App\Http\Controllers\Admin\AuthLdap
 */
class LdapController extends ApiController
{

    /**
     * Perform request login
     * @param Request $request
     * @return $this
     */
    public function login(Request $request)
    {
        try {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json($this->error($validator->errors()), 401);
            }

            $uid = $request->get('uid');
            $password = $request->get('password');

            $ds = ldap_connect(config('ldap.hostname'), config('ldap.port'));

            if ($ds) {
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, config('ldap.protocol_version'));

                $auth = ldap_bind($ds, $this->getRDN($uid), $password);

                if (! $auth) {
                    return response()->json($this->error('Invalid Credentials'), 401);
                }

                if (! ($search=@ldap_search($ds, config('ldap.base_dn'), $this->formatKeySearch($uid))) ) {
                    return response()->json($this->error('User not found'), 404);
                }

                $info = ldap_get_entries($ds, $search);

                $user = $this->getUser($info, $request->ip());

                if (! isset($user->name)) {
                    return response()->json($this->error('User not found'), 404);
                }

                return new LdapUserResource($user);
            }
        } catch (\Exception $e) {
            Log::info($e->getTraceAsString());
            return response()->json($this->error($e->getMessage()), 401);
        }
    }

    /**
     * Extract user data from LDAP entity
     * @param $data
     * @return array
     */
    private function getUser($data, $ip)
    {
        $newData = [];

        for ($i=0; $i<$data['count']; $i++) {

            if(array_key_exists('displayname',$data[$i])) {
                $newData['name'] = $this->formatName($data[$i]['displayname'][0]);
            }

            if(array_key_exists('mail',$data[$i])) {
                $newData['email'] = $data[$i]['mail'][0];
            }

            if(array_key_exists('employeenumber',$data[$i])) {
                $newData['uid'] = $data[$i]['employeenumber'][0];
            }
        }

        if (isset($newData['email'])) {
            $user = User::where('email', $newData['email'])->first();

            if (! $user) {
                $user = User::create([
                    'uid'  => $newData['uid'],
                    'name' => $newData['name'],
                    'email' => $newData['email'],
                    'password' => bcrypt(str_random()),
                    'api_token' => str_random()
                ]);
            }

            $user->generateToken($ip);

            return $user;
        }

        return (Object)$newData;
    }

    /**
     * @param $name
     * @return string
     */
    private function formatName($name)
    {
        return implode(' ', array_map(function ($string) {
            return ucfirst(strtolower($string));
        }, array_filter(explode(" ", $name))));
    }

    /**
     * Validate Data
     * @param array $data
     * @return mixed
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'uid'       => 'required',
            'password'  => 'required',
        ]);
    }

    /**
     * Get LDAP RDN
     * @param $value
     * @return mixed
     */
    private function getRDN($value)
    {
        return str_replace(':uid', $value, config('ldap.rdn'));
    }

    /**
     * @param $value
     * @param string $key
     * @return string
     */
    private function formatKeySearch($value, $key='cn')
    {
        return "{$key}={$value}";
    }

    /**
     * Obtem o menu inicial do usuário
     * @param $uid
     * @return mixed
     */
    private function rootMenu($uid)
    {
        $menus = DB::connection('oracle')
            ->table('nmlabs.vw_ice_menu')
            ->whereRaw("pack_ice_admin.func_set_menu({$uid}) = 1")
            ->get();

        return $menus;
    }

}