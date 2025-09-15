<?php
/**
 * Universal CRUD Library (MySQL / PostgreSQL)
 * Author: Your Name
 * 
 * Usage:
 *   $crud = new CRUD("pgsql"); // or "mysql"
 *   $crud->create("users", ["name" => "John", "email" => "john@example.com"]);
 *   $rows = $crud->read("users");
 */

class CRUD {
    private $pdo;

    // Database config
    private $dbtype = "pgsql";     // "mysql" or "pgsql"
    private $host   = "localhost";
    private $port   = "5432";      // default: MySQL=3306, PostgreSQL=5432
    private $db     = "sso_main";
    private $user   = "postgres";
    private $pass   = "padmin";
    private $charset = "utf8";  // Only used for MySQL

    public function __construct($dbtype = "pgsql") {
        $this->dbtype = strtolower($dbtype);

        if ($this->dbtype === "pgsql") {
            // PostgreSQL DSN
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db}";
        } else {
            // MySQL DSN
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }

    /** CREATE */
    public function create($table, $data) {
        $fields = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /** READ */
    // public function read($table, $where = "1=1", $fields = "*") {
    //     $sql = "SELECT $fields FROM $table WHERE $where";
    //     $stmt = $this->pdo->query($sql);
    //     return $stmt->fetchAll();
    // }

    public function read($table, $where = "1=1", $fields = "*", $joins = []) {
        $sql = "SELECT $fields FROM $table";

        // Add joins
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
            }
        }

        // Handle WHERE
        if (is_array($where) && !empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "$col = :$col";
            }
            $sql .= " WHERE " . implode(" AND ", $conditions);
        } else {
            // Assume string
            $sql .= " WHERE $where";
        }

        $stmt = $this->pdo->prepare($sql);

        // Bind params if array
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $stmt->bindValue(":$col", $val);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Join users with roles
    // $data = $crud->read(
    //     "users u",
    //     ["u.status" => 1],
    //     "u.id, u.name, r.role_name",
    //     [
    //         ["type" => "INNER", "table" => "roles r", "on" => "u.role_id = r.id"]
    //     ]
    // );
    /** UPDATE */
    public function update($table, $data, $where) {
        $setPart = "";
        foreach ($data as $key => $value) {
            $setPart .= "$key = :$key, ";
        }
        $setPart = rtrim($setPart, ", ");
        $sql = "UPDATE $table SET $setPart WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /** DELETE */
    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->pdo->exec($sql);
    }
}
