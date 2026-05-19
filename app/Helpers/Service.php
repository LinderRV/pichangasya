<?php

namespace App\Helpers;

class Service  {
    static function  responseSuccess( $mensaje =null,$data=null)
    {
        $response = [
            'status' => 200,
            'message' => $mensaje,
            'data' => $data
        ];
        return $response;
    }
    static function  responseError( $mensaje =null,$data=null,$status = 400)
    {
        $response = [
            'status' => $status,
            'message' => $mensaje,
            'data' => $data
        ];
        return $response;
    }


}
