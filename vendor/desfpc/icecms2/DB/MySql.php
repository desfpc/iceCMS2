<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB MySql Class
 */

namespace iceCMS2\DB;

use iceCMS2\Types\UnixTime;
use iceCMS2\Tools\Exception;
use mysqli;
use mysqli_result;
use mysqli_sql_exception;
use mysqli_stmt;
use \stdClass;
use Throwable;

class MySql implements DBInterface
{
    /** @var stdClass|null DB Connection settings */
    private ?stdClass $_settings = null;

    /** @var bool DB error flag */
    private bool $_isError = false;

    /** @var string|null DB error text */
    private ?string $_errorText = null;

    /** @var bool DB warning flag */
    private bool $_isWarning = false;

    /** @var string[]|null DB warning text */
    private ?array $_warningText = null;

    /** @var mysqli|false|null mysqli resourse */
    private mysqli|null|false $_mysqli = null;

    /** @var bool connecting flag */
    private bool $_isConnected = false;

    /** @var string|null connecting status text */
    private ?string $_connectedText = null;

    /**
     * Class Constructor
     *
     * @param \stdClass|null $dbSettings
     */
    public function __construct(?stdClass $dbSettings)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->_settings = $dbSettings;
    }

    /**
     * @inheritDoc
     */
    public function getError(): bool
    {
        return $this->_isError;
    }

    /**
     * @inheritDoc
     */
    public function getErrorText(): ?string
    {
        return $this->_errorText;
    }

    /**
     * @inheritDoc
     */
    public function getWarning(): bool
    {
        return $this->_isWarning;
    }

    /**
     * @inheritDoc
     */
    public function getWarningText(): ?string
    {
        return json_encode($this->_warningText);
    }

    /**
     * @inheritDoc
     */
    public function getConnected(): bool
    {
        return $this->_isConnected;
    }

    /**
     * @inheritDoc
     */
    public function getConnectedText(): ?string
    {
        return $this->_connectedText;
    }

    /**
     * @inheritDoc
     */
    public function connect(): bool
    {

        $this->_isConnected = false;
        $this->_connectedText = null;
        $this->_isError = false;
        $this->_errorText = null;
        $this->_isWarning = false;
        $this->_warningText = null;

        try {
            if (!$this->_mysqli = mysqli_connect($this->_settings->host, $this->_settings->login, $this->_settings->pass)) {
                $this->_isError = true;
                $this->_errorText = 'Failed to establish a connection to the database';
            } else {
                if (!$this->_mysqli->select_db($this->_settings->name)) {
                    $this->_isError = true;
                    $this->_errorText = 'There is no way to select a database "' . $this->_settings->name . '"';
                } else {
                    if (!$this->_mysqli->set_charset($this->_settings->encoding)) {
                        $this->_isWarning = true;
                        if (is_null($this->_warningText)) {
                            $this->_warningText = [];
                        }
                        $this->_warningText[] = 'Error choosing encoding: ' . $this->_settings->encoding;
                    }
                    $this->_isConnected = true;
                    $this->_connectedText = 'The connection to the database has been established.';
                }
            }
        } catch (Throwable $t) {
            $this->_isError = true;
            $this->_errorText = 'Failed to establish a connection to the database: ' . $t->getMessage();
        }
        return $this->_isConnected;
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): bool
    {
        if ($this->_isConnected) {
            $this->_mysqli->close();
        }

        $this->_isConnected = false;
        $this->_connectedText = null;
        $this->_isError = false;
        $this->_errorText = null;
        $this->_isWarning = false;
        $this->_warningText = null;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query): bool|array
    {
        if ($this->_isConnected) {

            try {
                $results = [];
                $i = -1;
                $this->_mysqli->multi_query($query);
                do {
                    ++$i;
                    /** @var mysqli_result|false $result */
                    if ($result = $this->_mysqli->store_result()) {
                        while ($row = $result->fetch_assoc()) {
                            $results[$i][] = $row;
                        }
                    }
                } while ($this->_mysqli->next_result());

                if (empty($results)) {
                    return true;
                }
                return $results;
            } catch (Exception $exception) {
                $this->_isWarning = true;
                if (is_null($this->_warningText)) {
                    $this->_warningText = [];
                }
                $this->_warningText[] = $exception->getMessage() . ': Error in request query: ' . $query;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function createMigrationTable(): bool
    {
        return $this->query('CREATE TABLE `migrations`  (
  `version` bigint(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`) USING BTREE,
  INDEX `migration_name_idx`(`name`) USING BTREE
) ENGINE = InnoDB;');
    }

    /**
     * @inheritDoc
     */
    public function queryBinded(string $query, array $values, $isCnt = false, $isForced = false): bool|array|int
    {
        if ($this->_isConnected || $isForced) {
            try {
                /** @var mysqli_stmt $stmt */
                if (!$stmt = $this->_mysqli->prepare($query)) {
                    throw new Exception('Wrong query: ' . $query);
                }
                if (!empty($values)) {
                    if (empty($values['types'])) {
                        $types = '';
                        foreach ($values as $key => $value) {
                            switch (gettype($value)) {
                                case 'boolean':
                                    if ($value === true) {
                                        $values[$key] = 1;
                                    } else {
                                        $values[$key] = 0;
                                    }
                                    $types .= 'i';
                                    break;
                                case 'NULL':
                                case 'integer':
                                    $types .= 'i';
                                    break;
                                case 'double':
                                    $types .= 'd';
                                    break;
                                case 'string':
                                    $types .= 's';
                                    break;
                                case 'object':
                                    if ($value instanceof UnixTime) {
                                        $types .= 'i';
                                        $values[$key] = ($value)->get();
                                    }
                                    break;
                                default:
                                    throw new Exception('Wrong value type "' . gettype($value) . '" for 
                                    auto-generate "types" string for mysqli params bindidg. Specify $values["types"] 
                                    with a valid type string. https://www.php.net/manual/en/mysqli-stmt.bind-param.php');
                            }

                        }
                    } else {
                        $types = $values['types'];
                        unset($values['types']);
                    }
                    $stmtValues = [];
                    foreach ($values as $value) {
                        $stmtValues[] = $value;
                    }
                    $stmt->bind_param($types, ...$stmtValues);
                }
                $stmt->execute();
            } catch (mysqli_sql_exception $exception) {
                return $this->_queryException($exception, $query);
            }
            catch (\Exception $exception) {
                $this->_isWarning = true;
                if (is_null($this->_warningText)) {
                    $this->_warningText = [];
                }
                $this->_warningText[] = $exception->getMessage();
                return false;
            }
            $res = $this->_queryRes($query, $stmt, $isCnt, false);
            $stmt->free_result();
            return $res;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, $isFree = true, $isCnt = false, $isForced = false): bool|array|int
    {
        if ($this->_isConnected || $isForced) {
            try {
                /** @var mysqli_result|bool $res */
                $res = $this->_mysqli->query($query);
            } catch (Exception|mysqli_sql_exception $exception) {
                return $this->_queryException($exception, $query);
            }
            return $this->_queryRes($query, $res, $isCnt, $isFree);
        }
        return false;
    }

    /**
     * Query exception processing
     *
     * @param Exception|mysqli_sql_exception $exception
     * @param string $query
     * @return bool
     */
    private function _queryException(Exception|mysqli_sql_exception $exception, string $query): bool
    {
        $this->_isWarning = true;
        if (is_null($this->_warningText)) {
            $this->_warningText = [];
        }
        $this->_warningText[] = $exception->getMessage() . ': Error in request query (' . $query . '): '
            . $this->_mysqli->error;
        return false;
    }

    /**
     * Query result processing
     *
     * @param string $query
     * @param mysqli_result|mysqli_stmt|bool $res
     * @param bool $isCnt
     * @param bool $isFree
     * @return bool|array|int
     */
    private function _queryRes(string $query, mysqli_result|mysqli_stmt|bool $res, bool $isCnt, bool $isFree): bool|array|int
    {
        // Query is SELECT, SHOW or WITH RECURSIVE
        if (
            preg_match("/^select/i", trim($query))
            || preg_match("/^show/i", trim($query))
            || preg_match("/^with recursive/i", trim($query))
        ) {
            if (!$isCnt) {
                $result = [];
                if (get_class($res) === 'mysqli_stmt') {
                    $resStmt = $res;
                    $res = $res->get_result();
                    $resStmt->free_result();
                }
                while ($row = $res->fetch_assoc()) {
                    $result[] = $row;
                }
                if ($isFree) {
                    //$res->free();
                }
                $res->free_result();
                return $result;
            }

            $result = $res->num_rows;
            if ($isFree) {
                $res->free();
            }
            $res->free_result();
            return $result;
        }

        // Other queryes
        if (!is_bool($res) && $res->insert_id > 0) {
            $res->free_result();
            return $res->insert_id;
        }
        if (!is_bool($res)) {
            $res->free_result();
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function realEscapeString(string $value): string
    {
        return $this->_mysqli->real_escape_string($value);
    }

    /**
     * @inheritDoc
     */
    public function transaction(string $query): bool
    {
        $this->_mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        try {
            $queryes = explode(';', $query);
            $s = [];
            foreach ($queryes as $q) {
                if(!empty($q)) {
                    $s[] = $this->_mysqli->prepare($q);
                }
            }

            foreach ($s as $q) {
                $q->execute();
            }

            $this->_mysqli->commit();
            $this->_isWarning = false;
            $this->_warningText = null;
        } catch (\Exception $exception) {
            $this->_mysqli->rollback();
            $this->_isWarning = true;
            if (is_null($this->_warningText)) {
                $this->_warningText = [];
            }
            $this->_warningText[] = $exception->getMessage();
        }
        return !$this->_isWarning;
    }
}