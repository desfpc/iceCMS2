<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * ImageSize entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class ImageSize extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'image_sizes';

    /** @var int|null FileId */
    private ?int $_imageId;

    /**
     * @param Settings $settings
     * @param int|null $id
     * @param int|null $imageId
     * @throws Exception
     */
    public function __construct(Settings $settings, ?int $id = null, ?int $imageId = null)
    {
        $this->_imageId = $imageId;
        parent::__construct($settings, $id);
    }

    /**
     * Getting SQL string for filling Entity values query
     *
     * @return string
     * @throws Exception
     */
    protected function _getEntityValuesSQL(): string
    {
        if (is_null($this->_id)) {
            throw new Exception('Entity has no ID');
        }
        if (is_null($this->_imageId)) {
            return parent::_getEntityValuesSQL();
        }
        return 'SELECT `s`.*, `i`.`is_created` FROM ' . $this->_dbtable . ' `s`'
            . ' RIGHT JOIN `file_image_sizes` `i` ON `s`.`id` = `i`.`image_size_id`'
            . ' WHERE `s`.`id` = ' . $this->_id . ' AND `i`.`file_id` = ' . $this->_imageId;
    }
}