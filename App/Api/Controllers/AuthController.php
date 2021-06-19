<?php
namespace App\Api\Controllers;

use App\Services\AuthService;
use App\Services\Request;

class AuthController
{
    private static $KEY = KEY;

    public function index()
    {
        return self::auth();
    }

    public static function auth()
    {
        extract(Request::all());

        if (empty($email) || empty($password)) {
            return json_encode(['success' => false, 'message' => 'Preencha todos os campos'], http_response_code(422), JSON_UNESCAPED_UNICODE);
        }
        //return json_encode(['email' => $email, 'password' => $password], http_response_code(422), JSON_UNESCAPED_UNICODE);
        if (AuthService::auth($email, $password)) {

            $header = [
                "typ" => "JWT",
                "alg" => "HS256",
            ];

            $payload = [
                "name" => "Markus",
                "email" => "markus@email.com",
            ];

            $header = json_encode($header);
            $payload = json_encode($payload);

            $header = base64_encode($header);
            $payload = base64_encode($payload);

            $sign = hash_hmac('sha256', $header . "." . $payload, self::$KEY, true);
            $sign = base64_encode($sign);

            $user_token = $header . '.' . $payload . '.' . $sign;

            return json_encode(['success' => true, 'message' => 'Usuário autenticado com sucesso', 'user_token' => $user_token], http_response_code(200), JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        }
    }

    public static function check()
    {
        $headers = apache_request_headers();
        
        if (isset($headers['Authorization'])) {
            $bearer = explode(' ', $headers['Authorization']);
            $user_token = $bearer[1] ?? '';
            $jwt = explode('.', array_pop($bearer));
            
            if (count($jwt) == 3) {

                $header = $jwt[0];
                $payload = $jwt[1];

                $valid = hash_hmac('sha256', $header . "." . $payload, self::$KEY, true);
                $valid = base64_encode($valid);

                $valid = $header . '.' . $payload . '.' . $valid;
                
                if ($valid == $user_token):
                    return true;
                endif;
            }
        }

        return false;
    }
}
