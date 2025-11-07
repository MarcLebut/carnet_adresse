<?php

class DBConnect
{
    private string $host = "localhost";
    private string $dbname = "carnet_adresses";
    private string $username = "root";
    private string $password = "";
    private ?PDO $pdo = null;

    /**
     * Retourne l'objet PDO (connexion à la BDD).
     * Si déjà instancié, retourne la même connexion.
     */
    public function getPDO(): ?PDO
    {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
                $this->pdo = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage() . "\n";
                return null;
            }
        }
        return $this->pdo;
    }
}

// Test
$db = new DBConnect();
var_dump($db->getPDO());
