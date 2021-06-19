<?php

/**
 * @author M Paulo
 *
 * <b>Connection:</b> Implementa uma conexão sob o pattern singleton.A classe retorna apenas uma instância de conexão com o banco de dados.
 */

namespace App\Database;

use \PDO;

class Connection {

    private static $DBHOST = DBHOST;
    private static $DBNAME = DBNAME;
    private static $DBUSER = DBUSER;
    private static $DBPASS = DBPASS;
    private static $DBDRIVE = DBDRIVE;
    private static $DBCHARSET = DBCHARSET;

    /** @var PDO */
    private static $conn = null;

    // Construtor privado - permite que a classe seja instanciada apenas internamente.
    private function __construct() {

    }

    /**
     * Método estático - acessível sem instanciação.Retorna uma instância única de conexão por vez.
     *
     * @return PDO
     */
    public static function connect() {
        // Garante uma única instância.Se não existe uma conexão, uma nova será criada.
        if (self::$conn === null) {
            try {

                self::$conn = new PDO(self::$DBDRIVE . ":host=" . self::$DBHOST . ";dbname=" . self::$DBNAME . ";charset=" . self::$DBCHARSET, self::$DBUSER, self::$DBPASS);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection Error: " . $e->getMessage());
            }
        }

        // Retorna a conexão.
        return self::$conn;
    }

}
