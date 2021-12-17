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

class MySql implements DBInterface
{
    /** @var stdClass|null DB Connection settings */
    private ?\stdClass $_settings = null;

    /** @var bool DB error flag */
    private bool $_isError = false;

    /** @var string|null DB error text */
    private ?string $_errorText = null;

    /** @var bool DB warning flag */
    private bool $_isWarning = false;

    /** @var string[]|null DB warning text */
    private ?array $_warningText = null;

    /** @var mysqli|false|null mysqli resourse */
    private $_mysqli = null;

    /** @var bool connecting flag */
    private bool $_isConnected = false;

    /** @var string|null connecting status text */
    private ?string $_connectedText = null;

    /**
     * Class Constructor
     *
     * @param \stdClass|null $dbSettings
     */
    public function __construct(?\stdClass $dbSettings)
    {
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
        return $this->_warningText;
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
    public function query(string $query, $isFree = true, $isCnt = false, $isForced = false)
    {
        if ($this->_isConnected || $isForced) {
            /** @var mysqli_result|bool $res */
            if (!$res = $this->_mysqli->query($query)) {
                $this->_isWarning = true;
                if (is_null($this->_warningText)) {
                    $this->_warningText = [];
                }
                $this->_warningText[] = 'Error in request query: ' . $query;
                return false;
            }

            // Query is SELECT, SHOW or WITH RECURSIVE
            if (preg_match("/^select/i", trim($query)) || preg_match("/^show/i", trim($query)) || preg_match("/^with recursive/i", trim($query))) {
                if (!$isCnt) {
                    $result = [];
                    while ($row = $res->fetch_assoc()) {
                        $result[] = $row;
                    }
                    if ($isFree) {
                        $res->free();
                    }
                    return $result;
                }

                $result = $res->num_rows;
                if ($free) {
                    $res->free();
                }
                return $result;
            }

            // Other queryes
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query): bool
    {
        if ($this->_isConnected) {
            /** @var mysqli_result|bool $res */
            if (!$res = $this->_mysqli->multi_query($query)) {
                do {
                    $this->_isWarning = true;
                    if (is_null($this->_warningText)) {
                        $this->_warningText = [];
                    }
                    $this->_warningText[] = 'Error in request query: ' . $query;
                    return false;
                } while (mysqli_more_results($this->_mysqli) && mysqli_next_result($this->_mysqli));
            }
            return true;
        }
    }
}