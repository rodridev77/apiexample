<?php
namespace App\Services;

use App\Database\Connection;
use App\Config\Log;

class AuthService
{
    private static $table = 'users';
    private static $data = [];

    public static function auth($email, $password)
    {
        $conn = Connection::connect();

        $hashKey = hash('sha256', $password);

        $query = 'SELECT * FROM ' . self::$table . ' WHERE email = :email AND pssword = :password';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashKey);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                return true;
            endif;

            return false;
        } catch (\Exception $e) {
            Log::storeError($e->getMessage(), $e->getLine(), $e->getFile());
            return false;
        }
    }

    public static function userExists(int $userId)
    {
        $conn = Connection::connect();

        $query = 'SELECT id FROM ' . self::$table . ' WHERE id = :userId';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':userId', $userId);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                $conn = $stmt = null;
                return true;
            endif;

            return false;
        } catch (Exception $e) {
            Log::storeError($e->getMessage(), $e->getLine(), $e->getFile());
            return false;
        }
    }
}