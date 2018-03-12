<?php

namespace App\Http\Controllers\Auth\Ldap;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Auth\LdapUserResource;
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

                $user = $this->extractData($info);

                if (! isset($user->name)) {
                    return response()->json($this->error('User not found'), 404);
                }

                return new LdapUserResource($user);
            }
        } catch (\Exception $e) {
            return response()->json($this->error($e->getMessage()));
        }
    }

    /**
     * Extract user data from LDAP entity
     * @param $data
     * @return array
     */
    private function extractData($data)
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

        return (object)$newData;
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

}