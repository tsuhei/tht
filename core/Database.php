<?php
// core/Database.php
namespace App\Core;
use PDO;
use PDOException;

class Database
{
    private $host   = DB_HOST;
    private $dbname = DB_NAME;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbh;
    private $stmt;

    public function __construct()
    {
        // DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
        $options = [
            PDO::ATTR_PERSISTENT         => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    // Prepara la consulta
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Vincula un valor a un parámetro
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):   $type = PDO::PARAM_INT; break;
                case is_bool($value):  $type = PDO::PARAM_BOOL; break;
                case is_null($value):  $type = PDO::PARAM_NULL; break;
                default:               $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Ejecuta la consulta
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Retorna múltiples registros
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Retorna un único registro
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Retorna el número de filas afectadas
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}
