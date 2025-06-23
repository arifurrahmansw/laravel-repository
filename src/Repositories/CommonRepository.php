<?php
namespace App\Repositories\Common;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class CommonRepository
 *
 * A universal data access helper for performing CRUD operations dynamically.
 *
 * @author Md Arifur Rahman
 * @github https://github.com/arifurrahmansw
 * @created 2025-06-22
 * @version 1.0.0
 */
class CommonRepository
{
    /**
     * Insert a single record into the table.
     */
    public function insert(string $table, array $data): int
    {
        return DB::table($table)->insertGetId($data);
    }
    /**
     * Insert multiple records into the table.
     */
    public function insertBatch(string $table, array $data): bool
    {
        return DB::table($table)->insert($data);
    }

    /**
     * Update a record by a single condition.
     */
    public function update(string $table, array $data, string $key, mixed $value): int
    {
        return DB::table($table)->where($key, $value)->update($data);
    }

    /**
     * Update records using multiple conditions.
     */
    public function updateMulti(string $table, array $data, array $conditions): int
    {
        return DB::table($table)->where($conditions)->update($data);
    }

    /**
     * Update multiple rows by a list of values (e.g., IDs).
     */
    public function updateBulk(string $table, array $data, string $key, array $values): int
    {
        return DB::table($table)->whereIn($key, $values)->update($data);
    }

    /**
     * Perform batch upsert based on index key.
     */
    public function updateBatch(string $table, array $data, string $index): bool
    {
        return DB::table($table)->upsert($data, [$index]);
    }

    /**
     * Delete records by condition(s).
     */
    public function delete(string $table, array $conditions): int
    {
        return DB::table($table)->where($conditions)->delete();
    }

    /**
     * Get a single record by condition(s).
     */
    public function getRow(string $table, array $conditions = [], array $columns = ['*']): ?object
    {
        return DB::table($table)->where($conditions)->first($columns);
    }

    /**
     * Get multiple records by condition(s), with optional ordering.
     */
    public function getRows(
        string $table,
        array $conditions = [],
        array $columns = ['*'],
        ?string $orderBy = null,
        string $direction = 'asc'
    ): Collection {
        $query = DB::table($table)->select($columns)->where($conditions);
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }
        return $query->get();
    }

    /**
     * Paginate records with optional filtering and ordering.
     */
    public function paginate(
        string $table,
        int $perPage = 15,
        array $conditions = [],
        array $columns = ['*'],
        ?string $orderBy = null,
        string $direction = 'asc'
    ): LengthAwarePaginator {
        $query = DB::table($table)->select($columns)->where($conditions);
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }
        return $query->paginate($perPage);
    }

    /**
     * Check if a record exists by given conditions.
     */
    public function exists(string $table, array $conditions): bool
    {
        return DB::table($table)->where($conditions)->exists();
    }

    /**
     * Count records by conditions.
     */
    public function count(string $table, array $conditions = []): int
    {
        return DB::table($table)->where($conditions)->count();
    }

    /**
     * Get the maximum value of a column.
     */
    public function max(string $table, string $column): mixed
    {
        return DB::table($table)->max($column);
    }

    /**
     * Get the minimum value of a column.
     */
    public function min(string $table, string $column): mixed
    {
        return DB::table($table)->min($column);
    }

    /**
     * Get the average value of a column.
     */
    public function avg(string $table, string $column): mixed
    {
        return DB::table($table)->avg($column);
    }

    /**
     * Get the sum of a column.
     */
    public function sum(string $table, string $column): mixed
    {
        return DB::table($table)->sum($column);
    }
}
