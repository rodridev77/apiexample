<?php
namespace App\Config;

use App\Database\Connection;

class Log
{
    private static $table = 'log';

    public static function storeError($message, $line, $file)
    {
        $conn = Connection::connect();

        $query = 'INSERT INTO ' . self::$table . ' (message, line, file) VALUES (:message, :line, :file)';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':message', $message);
        $stmt->bindValue(':line', $line);
        $stmt->bindValue(':file', $file);

        try {
            $stmt->execute();

            if ($stmt->rowCount() > 0):
                return true;                
            endif;

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}