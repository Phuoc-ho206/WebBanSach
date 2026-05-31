<?php

/**
 * Interface RepositoryInterface
 * Dependency Inversion: các tầng cao phụ thuộc vào abstraction này
 */
interface RepositoryInterface {
    public function findAll(int $limit = 20, int $offset = 0): array;
    public function findById(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
