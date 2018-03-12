<?php

return [

    'hostname' => env('LDAP_HOSTNAME', null),

    'port' => env('LDAP_PORT', 389),

    'protocol_version' => env('LDAP_PROTOCOL_VERSION', 3),

    'base_dn' => env('LDAP_BASE_DN', ''),

    'rdn' => env('LDAP_RDN', ''),

];
