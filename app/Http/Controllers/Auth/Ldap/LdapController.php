<?php

namespace App\Http\Controllers\Auth\Ldap;

use App\AppAnalytic;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Auth\LdapUserResource;
use Facades\App\Http\Services\Vtrine as VtrineService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * LDAP Authentication Adapter
 * Class AuthController
 * @package App\Http\Controllers\Admin\AuthLdap
 */
class LdapController extends ApiController
{

    /**
     * Just Authenticate on ldap
     *
     * @param Request $request
     * @return void
     */
    public function simpleLogin(Request $request)
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
                    return response()->json(
                        ['error' => 'User not found'],
                        401
                    );
                }

                $info = ldap_get_entries($ds, $search);

                $user = $this->getUserSimple($info);

                return response()->json($user);
            }

        } catch (\Exception $e) {
            if ( strpos($e->getMessage(), 'Unable to bind to server: Invalid credentials')) {
                return response()->json(
                    ['error' => 'Invalid Credentials'],
                    401
                );
            }

            return response()->json(
                ['error' => 'Application error'],
                500
            );
        }
    }
    

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

            $this->insertAppAnalytics($request);

            $ds = ldap_connect(config('ldap.hostname'), config('ldap.port'));

            if ($ds) {
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, config('ldap.protocol_version'));

                $auth = ldap_bind($ds, $this->getRDN($uid), $password);

                if (! $auth) {
                    return response()->json($this->error('Invalid Credentials'), 401);
                }

                if (! ($search=@ldap_search($ds, config('ldap.base_dn'), $this->formatKeySearch($uid))) ) {
                    return response()->json(
                        ['code' => 1, 'error' => 'User not found', 'msg' => 'Crendencias inv??lidas, tente novamente'],
                        401
                    );
                }

                $info = ldap_get_entries($ds, $search);

                $user = $this->getUser($info, $request->ip());

                if (! isset($user->name)) {
                    return response()->json(
                        ['code' => 1, 'error' => 'User not found', 'msg' => 'Crendencias inv??lidas, tente novamente'],
                        401
                    );
                }

                /** Validate if user can access at this time */
                $msgAccess = VtrineService::collaboratorCanAccess($user->uid);

                if ($msgAccess) {
                    return response()->json(
                        ['code' => 2, 'error' => 'Access not allowed at this time', 'msg' => 'Acesso negado neste momento'],
                        401
                    );
                }

                $this->verifiedAppAnalytics($request);

                return new LdapUserResource($user);
            }

        } catch (\Exception $e) {
            if ( strpos($e->getMessage(), 'Unable to bind to server: Invalid credentials')) {
                return response()->json(
                    ['code' => 3, 'error' => 'Ldap error', 'msg' => 'Credenciais inv??lidas, tente novamente'],
                    401
                );
            }

            report($e);

            return response()->json(
                ['code' => 6, 'error' => 'Application error', 'msg' => 'Ocorreu uma falha, tente novamente'],
                401
            );
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
            } else if ($user->uid != $newData['uid']) {
                $user->uid = $newData['uid'];
                $user->save();
            }

            $user->generateToken($ip);

            return $user;
        }

        return (Object)$newData;
    }

    /**
     * Return User info without entity format, just ldap data 
     * @param Array $data
     * @return void
     */
    public function getUserSimple($data)
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

        return $newData;
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
     * Obtem o menu inicial do usu??rio
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

    /**
     * Insert app analytics
     * @param Request $request
     * @return bool
     */
    private function insertAppAnalytics(Request $request) {

        if ($request->hasHeader('x-cockpit-platform') and ($request->hasHeader('x-cockpit-version'))) {
            $platform = $request->header('x-cockpit-platform');
            $version = $request->header('x-cockpit-version');

            if (!empty($platform) and !empty($version)) {
                AppAnalytic::firstOrCreate(
                    [
                        'uid' => $request->get('uid'),
                        'platform' => $platform,
                        'version' => $version,
                    ],
                    ['verified' => false]
                );
            }
        }

        return false;
    }

    /**
     * Set 'verified' equal true to the app analytics
     * @param Request $request
     * @return bool
     */
    private function verifiedAppAnalytics(Request $request) {

        if ($request->hasHeader('x-cockpit-platform') and ($request->hasHeader('x-cockpit-version'))) {
            $platform = $request->header('x-cockpit-platform');
            $version = $request->header('x-cockpit-version');

            if (!empty($platform) and !empty($version)) {
                AppAnalytic::where('uid', $request->get('uid'))
                    ->where('platform', $platform)
                    ->where('version', $version)
                    ->where('verified', false)
                    ->update(['verified' => true]);
            }
        }

        return false;
    }

}