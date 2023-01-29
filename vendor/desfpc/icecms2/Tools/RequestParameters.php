<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Request Parameters class
 */

namespace iceCMS2\Tools;

use stdClass;

class RequestParameters
{
    /**
     * @var stdClass
     */
    public stdClass $values;

    /**
     * Constructor
     *
     * @param stdClass|null $values
     */
    public function __construct(stdClass $values = null)
    {
        if (is_null($values)) {
            $this->values = new stdClass();
        } else {
            $this->values = $values;
        }
    }

    /**
     * Get request values
     *
     * @param array|string $valuesnames
     * @param int $mode
     * @return void
     */
    public function getRequestValues(array|string $valuesnames, int $mode = 0): void
    {
        if (is_array($valuesnames)) {
            foreach ($valuesnames as $valuename)
                $this->getRequestValue($valuename, $mode);
        } else {
            $this->getRequestValue($valuesnames, $mode);
        }
    }

    /**
     * Get request value
     *
     * @param string $valuename
     * @param int $mode
     * @return void
     */
    public function getRequestValue(string $valuename, int $mode = 0): void
    {
        if ($valuename != '') {
            if (isset($_REQUEST[$valuename])) {
                if (is_array($_REQUEST[$valuename])) {
                    $this->values->$valuename = array();
                    foreach ($_REQUEST[$valuename] as $val) {
                        if ($mode == 0) {
                            $this->values->$valuename[] = htmlspecialchars($val, ENT_QUOTES);
                        } else {
                            $this->values->$valuename[] = $val;
                        }
                    }
                } else {
                    if ($mode == 0) {
                        $this->values->$valuename = htmlspecialchars($_REQUEST[$valuename], ENT_QUOTES);
                    } else {
                        $this->values->$valuename = $_REQUEST[$valuename];
                    }
                }
            } else {
                $this->values->$valuename = '';
            }
        }
    }

    /**
     * Return values
     *
     * @return stdClass
     */
    public function returnValues(): stdClass
    {
        return ($this->values);
    }
}