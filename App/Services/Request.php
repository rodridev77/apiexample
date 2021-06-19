<?php
namespace App\Services;

class Request
{
    public static function all()
    {
        $request = file_get_contents('php://input');

        if (!empty($request)):
            $request = json_decode($request, true);
            return $request;
        endif;

        return $request;
    }
}