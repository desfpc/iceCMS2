<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * AbstractLogEntity class
 */

namespace iceCMS2\Models;

use iceCMS2\Locale\LocaleText;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

abstract class AbstractLogEntity extends AbstractEntity
{
    /**
     * @inheritDoc
     */
    public function __construct(Settings $settings, int|array|null $id = null)
    {
        $this->_settings = $settings;
        $this->_dbtable = $this->_getTableName();
        parent::__construct($settings, $id);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _ifGetTableColsError(): void
    {
        //Checking for table exist and creating if not
        if (!$this->_ifLogTableExists() && !$this->_createLogTable()) {
            throw new Exception(LocaleText::get(
                $this->_settings,
                'log/errors/Error creating log table: {tableName}',
                ['tableName' => $this->_dbtable]
            ));
        }
    }

    /**
     * TODO Get name of table according to log period and now time
     *
     * @return string
     */
    private function _getTableName(): string
    {

    }

    /**
     * TODO Checking if real log table is exists
     *
     * @return bool
     */
    private function _ifLogTableExists(): bool
    {

    }

    /**
     * TODO Creating log table for now time
     *
     * @return bool
     */
    private function _createLogTable(): bool
    {

    }
}