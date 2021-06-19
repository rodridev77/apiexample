<?php
namespace App\Models;

use App\Database\Connection;
use App\Config\Log;
use App\Services\Response;

class User
{
    private static $table = 'users';
    private static $data = [];
    private static $code = ['code' => 200];

    public static function all() : Response 
    {
        $conn = Connection::connect();
        $response = new Response();

        $query = 'SELECT * FROM ' . self::$table;
        $stmt = $conn->query($query);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                self::$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            else:
                self::$code = ['code' => 204];
            endif;

            $conn = $stmt = null;
            $response->setData(self::$data, self::$code);
            return $response;
        } catch (Exception $e) {
            $response->setData(self::$data, ['code' => 500, 'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return $response;
        }
    }

    public static function save(array $request, int $userId = 0) : Response
    {
        $conn = Connection::connect();
        $response = new Response();

        if (!empty($request)):
            extract($request);

            if ($userId == 0):
                $code = ['code' => 201];
                $query = 'INSERT INTO ' . self::$table . ' (name, email, pssword) VALUES (:name, :email, :password)';
            else:
                $code = ['code' => 200];
                $query = 'UPDATE ' . self::$table . ' SET name = :name, email = :email, pssword = :password WHERE id = :userId';
            endif;

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $password);

            if ($userId > 0):
                $stmt->bindValue(':userId', $userId);
            endif;
        endif;

        try {
            $stmt->execute();

            $conn = $stmt = null;
            $response->setData(self::$data, $code);

            return $response;
        } catch (Exception $e) {
            $response->setData(self::$data, ['code' => 500, 'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return $response;
        }
    }

    public static function show(int $userId)
    {
        $conn = Connection::connect();

        $query = 'SELECT * FROM ' . self::$table . ' WHERE id = :userId';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':userId', $userId);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                self::$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $conn = $stmt = null;
            endif;

            return self::$data;
        } catch (Exception $e) {
            Log::storeError($e->getMessage(), $e->getLine(), $e->getFile());
            return 500;
        }
    }

    public static function destroy(int $userId)
    {
        $conn = Connection::connect();

        $query = 'DELETE FROM ' . self::$table . ' WHERE id = :userId';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':userId', $userId);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                $conn = $stmt = null;
                return 200;
            endif;

            return 204;
        } catch (Exception $e) {
            Log::storeError($e->getMessage(), $e->getLine(), $e->getFile());
            return 500;
        }
    }
}
