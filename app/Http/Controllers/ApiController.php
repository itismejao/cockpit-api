<?php

namespace App\Http\Controllers;

class ApiController extends Controller {

    /**
     * Format error for response
     * @param $message
     * @return array
     */
    public function error($message=NULL)
    {
        $message = $message === NULL ? 'Application Error.' : $message;
        return ['error' => $message];
    }

    /**
     * @param $data
     * @return array
     */
    public function indicatorsFormat($data)
    {
        return ['data' => $data];
    }
}