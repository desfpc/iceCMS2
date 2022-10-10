<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Material Type entity class
 */

namespace iceCMS2\Models;

class MaterialType extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'material_types';

    /** @var array|null Extra params */
    public ?array $extraParams = null;

    /** @var array|null Parent types */
    public ?array $parents = null;

    /** @var array|null Child types */
    public ?array $childs = null;

    /**
     * Some logics after Entity load() method. Fore extend in child classes.
     *
     * @return void
     */
    protected function _afterLoad(): void
    {
        $this->_getChilds();
        $this->_getParents();
    }

    /**
     * TODO Get Type extra params
     *
     * @return void
     */
    private function _getExtraParams(): void
    {
    }

    /**
     * Get Type parent types
     *
     * @return void
     *
     */
    private function _getParents(): void
    {
        $query = 'WITH RECURSIVE ptypes AS (
	
	SELECT t.* FROM 
	material_types t WHERE t.id = ' . $this->_id . '

	UNION ALL

	SELECT tt.* FROM
	material_types tt, ptypes p
	WHERE tt.id = p.parent_id
	)

    SELECT * FROM ptypes WHERE id <> ' . $this->_id . ' ORDER BY parent_id ASC;';

        if ($res = $this->_db->query($query)) {
            $this->parents = $res;
        }
    }

    /**
     * Get Type child types
     *
     * @return void
     */
    private function _getChilds(): void
    {
        $query = 'WITH RECURSIVE ptypes AS (
	
	SELECT t.* FROM 
	material_types t WHERE t.id = ' . $this->_id . '

	UNION ALL

	SELECT tt.* FROM
	material_types tt, ptypes p
	WHERE tt.parent_id = p.id

	)

    SELECT * FROM ptypes WHERE id <> ' . $this->_id . ';';

        if ($res = $this->_db->query($query)) {
            $this->childs = $res;
        }
    }

    /**
     * Get type string
     *
     * @return string
     */
    public function getUrl(): string
    {
        $url = '';
        if (!empty($this->parents)) {
            foreach ($this->parents as $parent) {
                $url .= $parent['url'] . '/';
            }
        }

        if ($this->_values['id_char'] !== 'main') {
            return $url . '/' . $this->_values['id_char'];
        }

        return $url;
    }
}