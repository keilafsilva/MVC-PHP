<?php
namespace App\Core;

class Database
{
    private ?\PDO $connection = null;
    private static ?self $instance = null;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    function connect() : bool 
    {
        $databaseConfig = config('database');

        $dsn = "mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']};charset={$databaseConfig['charset']}";
        $username = $databaseConfig['username'];
        $password = $databaseConfig['password'];

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];

        try {
            $this->connection = new \PDO($dsn, $username, $password, $options);
            return true;
        } catch (\PDOException $e) {
            throw new \PDOException('Database connection failed: ' . $e->getMessage());
        }
        return false;
    }
    // retorna o unico resultado da consulta
    public function fetch(string $sql, array $params = []): array|false
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Retorna um array com os dados da consulta
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // retorna o rowCount 
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    // Retorna o ultimo ID inserido
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new \PDOException('Database query failed: ' . $e->getMessage());
        }
    }
}
?>