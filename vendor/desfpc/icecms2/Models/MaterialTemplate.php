<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Material Template entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Tools\Exception;

class MaterialTemplate extends AbstractEntity
{
    /** @var string|null Material Type Template column  name */
    public ?string $materialTypeColumnName = null;

    /** @var array|null Connected  */
    public ?array $connectedMaterials = null;

    /** @var string Entity DB table name */
    protected string $_dbtable = 'material_templates';

    /**
     * Get material type column name for current template type
     *
     * @return string
     * @throws Exception
     */
    private function _getColName(): string
    {
        if (empty($this->_values['type'])) {
            throw new Exception('Empty template type');
        }
        return 'template_' . $this->_values['type'];
    }

    /** Get connected materials list */
    private function _getConnectedMaterialTypes(): array
    {
        $query = 'SELECT * FROM `material_types`
         WHERE `template_list` = ' . $this->_id . ' OR `template_material` = ' . $this->_id
            . ' OR `template_admin` = ' . $this->_id
            . ' ORDER BY `id` ASC';

        if ($res = $this->_db->query($query)) {
            return $res;
        } else {
            return [];
        }
    }

    /**
     * Some logics after Entity load() method. Fore extend in child classes.
     *
     * @return void
     * @throws Exception
     */
    protected function _afterLoad(): void
    {
        $this->materialTypeColumnName = $this->_getColName();
        $this->connectedMaterials = $this->_getConnectedMaterialTypes();
    }
}