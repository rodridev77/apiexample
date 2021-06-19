<?php
namespace App\Services;

use \Exception;

class Response
{    
    private $response = [];

    public function setData(Array $data, Array $error) : Array
    {
        if (!empty($error)):
            $this->response = [
                'data' => $data,
                'error' => $error,
            ];

            return $this->response;
        endif;

        return $this->response;
    }

    public function getData()
    {
        return $this->response['data'];
    }

    public function getError()
    {
        return $this->response['error'];
    }

    public function getCode() : String
    {
        return $this->response['error']['code'];
    }
}