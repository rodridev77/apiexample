<?php
namespace App\Api\Controllers;

use App\Models\User;
use App\Services\Request;
use App\Services\Response;
use App\Services\AuthService;

class UsersController
{

    public function index()
    {
        if (!AuthController::check()):
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        endif;

        $response = new Response();
        $response = User::all();
        
        if ($response->getCode() == 500):
            return json_encode(['success' => false, 'message' => 'Ops, houve um erro.',$response->getData(), $response->getError()], http_response_code($response->getCode()), JSON_UNESCAPED_UNICODE);
        endif;

        if ($response->getCode() == 204):
            return json_encode(['success' => true, 'message' => 'Nenhum usuário encontrado.',$response->getData(), $response->getError()], http_response_code($response->getCode()), JSON_UNESCAPED_UNICODE);
        endif;

        return json_encode(['success' => true, 'data' => $response->getData(), 'error' => $response->getError()], http_response_code($response->getCode()), JSON_UNESCAPED_UNICODE);

        $data = [
            'data' => User::all(),
        ];

        if (!empty($data)):
            return json_encode(['success' => true, $data], http_response_code(200), JSON_UNESCAPED_UNICODE);
        else:
            return json_encode(['success' => false, 'data' => $data, 'message' => 'Nenhum usuário encontrado'], http_response_code(200), JSON_UNESCAPED_UNICODE);
        endif;
    }

    public function store()
    {
        if (!AuthController::check()):
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        endif;

        extract(Request::all());

        $data = [
            'name' => $name ?? '',
            'email' => $email ?? '',
            'password' => $password ? hash('sha256', $password) : '',
        ];

        foreach ($data as $key => $value) {
            if (empty($value)):
                return json_encode(['success' => false, 'message' => 'Preencha o campo ' . $key], http_response_code(422), JSON_UNESCAPED_UNICODE);
            endif;
        }

        if (User::save($data)):
            return json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso.'], http_response_code(201), JSON_UNESCAPED_UNICODE);
        else:
            return json_encode(['success' => false, 'message' => 'Erro ao cadastrar o usuário.'], http_response_code(500), JSON_UNESCAPED_UNICODE);
        endif;
    }

    public function update($userId)
    { 
        if (!AuthController::check()):
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        endif;

        extract(Request::all());

        $data = [
            'name' => $name ?? '',
            'email' => $email ?? '',
            'password' => $password ? hash('sha256', $password) : '',
        ];

        foreach ($data as $key => $value) {
            if (empty($value)):
                return json_encode(['success' => false, 'message' => 'Preencha o campo ' . $key], http_response_code(422), JSON_UNESCAPED_UNICODE);
            endif;
        }

        if (!empty($userId)) {
            if (is_numeric($userId) && ($userId > 0)) {

                if (!AuthService::userExists($userId)):
                    return json_encode(['success' => false, 'message' => 'Usuário não encontrado'], http_response_code(400), JSON_UNESCAPED_UNICODE);
                endif;

                if (User::save($data, $userId)):
                    return json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso.'], http_response_code(200), JSON_UNESCAPED_UNICODE); else:
                    return json_encode(['success' => false, 'message' => 'Erro ao editar o usuário.'], http_response_code(500), JSON_UNESCAPED_UNICODE);
                endif;
            }
        } else {
            return json_encode(['success' => false, 'message' => 'Not Found'], http_response_code(404));
        }
    }

    public function delete($userId)
    {      
        if (!AuthController::check()):
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        endif;

        if (!empty($userId)) {
            if (is_numeric($userId) && ($userId > 0)) {

                if (!AuthService::userExists($userId)):
                    return json_encode(['success' => false, 'message' => 'Usuário não encontrado'], http_response_code(400), JSON_UNESCAPED_UNICODE);
                endif;

                if (User::destroy($userId)):
                    return json_encode(['success' => true, 'message' => 'Usuário deletado com sucesso.'], http_response_code(200), JSON_UNESCAPED_UNICODE);
                else:
                    return json_encode(['success' => false, 'message' => 'Não foi possível deletar o usuário'], http_response_code(200), JSON_UNESCAPED_UNICODE);
                endif;
            }
        } else {
            return json_encode(['success' => false, 'message' => 'Not Found'], http_response_code(404));
        }
    }

    public function show($userId)
    {     
        if (!AuthController::check()):
            return json_encode(['success' => false, 'message' => 'Usuário não autenticado'], http_response_code(401), JSON_UNESCAPED_UNICODE);
        endif;
         
        if (!empty($userId)) {
            if (is_numeric($userId) && ($userId > 0)) {
                
                if (!AuthService::userExists($userId)):
                    return json_encode(['success' => false, 'message' => 'Usuário não encontrado'], http_response_code(400), JSON_UNESCAPED_UNICODE);
                endif;

                $data = [
                    'data' => User::show($userId),
                ];

                if (!empty($data['data'])):
                    return json_encode(['success' => true, $data], http_response_code(200), JSON_UNESCAPED_UNICODE);
                else:
                    return json_encode(['success' => false, $data, 'message' => 'Nenhum usuário encontrado'], http_response_code(200), JSON_UNESCAPED_UNICODE);
                endif;
            }
        } else {
            return json_encode(['success' => false, 'message' => 'Not Found'], http_response_code(404));
        }
    }

}
