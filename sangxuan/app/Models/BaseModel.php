<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Interfaces/RepositoryInterface.php';

/**
 * BaseModel - Open/Closed: mở rộng bằng kế thừa, không sửa class này
 */
abstract class BaseModel implements RepositoryInterface {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int {
        $cols   = implode(', ', array_keys($data));
        $params = ':' . implode(', :', array_keys($data));
        $stmt   = $this->db->prepare(
            "INSERT INTO {$this->table} ({$cols}) VALUES ({$params})"
        );
        $stmt->execute($this->prefixKeys($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $set  = implode(', ', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :_id"
        );
        $params          = $this->prefixKeys($data, '');
        $params[':_id']  = $id;
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    private function prefixKeys(array $data, string $prefix = ':'): array {
        $result = [];
        foreach ($data as $k => $v) {
            $result["{$prefix}{$k}"] = $v;
        }
        return $result;
    }
}
