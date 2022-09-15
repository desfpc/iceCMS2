<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class ImageSizeList extends AbstractEntityList
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'image_sizes';

    /** @var int|null File ID */
    private ?int $_fileId = null;

    /**
     * Entity list constructor
     *
     * @param Settings $settings
     * @param array $conditions
     * @param array $order
     * @param int $page
     * @param ?int $size
     * @throws Exception
     */
    public function __construct(Settings $settings, array $conditions = [], array $order = [], int $page = 1, ?int $size = 10)
    {
        if (isset($conditions['file_id'])) {
            $this->_fileId = $conditions['file_id'];
        }
        unset($conditions['file_id']);
        parent::__construct($settings, $conditions, $order, $page, $size);
    }

    /**
     * Getting more select query part
     *
     * @return string
     */
    protected function _getMoreSelectQuery(): string
    {
        if (is_null($this->_fileId)) {
            return '';
        }
        return ', `fis`.`is_created`';
    }

    /**
     * Getting Join query part
     *
     * @return string
     */
    protected function _getJoinQuery(): string
    {
        if (is_null($this->_fileId)) {
            return '';
        }
        return ' INNER JOIN `file_image_sizes` `fis` ON `dbtable`.`id` = `fis`.`image_size_id` ';
    }

    /**
     * Getting more WHERE query part
     *
     * @return string
     */
    protected function _getMoreWhereQuery(): string
    {
        if (is_null($this->_fileId)) {
            return '';
        }
        return ' AND `fis`.`file_id` = ' . $this->_fileId . ' ';
    }
}