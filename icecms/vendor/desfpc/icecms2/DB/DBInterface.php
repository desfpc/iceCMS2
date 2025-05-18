<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB Interface
 */

namespace iceCMS2\DB;

interface DBInterface
{
    /**
     * Get error flag
     *
     * @return bool
     */
    public function getError(): bool;

    /**
     * Get error text
     *
     * @return string|null
     */
    public function getErrorText(): ?string;

    /**
     * Get warning flag
     *
     * @return bool
     */
    public function getWarning(): bool;

    /**
     * Get warning text
     *
     * @return string|null
     */
    public function getWarningText(): ?string;

    /**
     * Get connecting flag
     *
     * @return bool
     */
    public function getConnected(): bool;

    /**
     * Get connecting status
     *
     * @return string|null
     */
    public function getConnectedText(): ?string;

    /**
     * Connect to DB
     *
     * @return bool
     */
    public function connect(): bool;

    /**
     * Close DB connection
     *
     * @return bool
     */
    public function disconnect(): bool;

    /**
     * Query to DB
     *
     * @param string $query SQL query
     * @param bool $isFree clear result after query
     * @param bool $isCnt return number of rows, not rows array
     * @param bool $isForced try to execute the request even if there are errors
     * @return bool|array|int
     */
    public function query(string $query, bool $isFree = true, bool $isCnt = false, bool $isForced = false): bool|array|int;

    /**
     * Query to DB with binded values (prepare and execute)
     *
     * @param string $query SQL query
     * @param array $values values for bind
     * @param false $isCnt return number of rows, not rows array
     * @param bool $isForced try to execute the request even if there are errors
     * @return bool|array|int
     */
    public function queryBinded(string $query, array $values, bool $isCnt = false, bool $isForced = false): bool|array|int;

    /**
     * MultiQuery to DB
     *
     * @param string $query
     * @return bool|array
     */
    public function multiQuery(string $query): bool|array;

    /**
     * Transaction CREATE -> process $query -> COMMIT OR ROLLBACK
     *
     * @param string $query
     * @return bool
     */
    public function transaction(string $query): bool;

    /**
     * Creating migration table
     *
     * @return bool
     */
    public function createMigrationTable(): bool;

    /**
     * Real escape string for SQL request
     *
     * @param string $value
     * @return string
     */
    public function realEscapeString(string $value): string;

    /**
     * Get array of Enum values
     *
     * @param $table
     * @param $field
     * @return array
     */
    public function getEnumValues($table, $field): array;
}