<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialTypesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialTypesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `material_types`  (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Material type ID',
            `name` varchar(512) NOT NULL COMMENT 'Material type name',
            `parent_id` int UNSIGNED NULL DEFAULT NULL COMMENT 'Parent material type ID',
            `id_char` varchar(512) NOT NULL COMMENT 'Material type char ID',
            `ordernum` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material type order number',
            `sitemenu` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Show in site menu',
            `template_list` int UNSIGNED NULL DEFAULT NULL COMMENT 'Template ID for material list',
            `template_material` int UNSIGNED NULL DEFAULT NULL COMMENT 'Template ID for material item',
            `template_admin` int UNSIGNED NULL DEFAULT NULL COMMENT 'Template ID for material admin',
            `prepare_list` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Prepare list in template flag',
            `prepare_item` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Prepare item in template flag',
            `list_items` int UNSIGNED NULL DEFAULT NULL COMMENT 'Number of items in list',
            `shop_ifgood` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Is type for shop good',
            `shop_ifstore` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Is type for shop store',
            PRIMARY KEY (`id`) USING BTREE,
            CONSTRAINT `mt_temp_admin_fk` FOREIGN KEY (`template_admin`) REFERENCES `material_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `mt_temp_item_fk` FOREIGN KEY (`template_material`) REFERENCES `material_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `mt_temp_list_fk` FOREIGN KEY (`template_list`) REFERENCES `material_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
        ";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `material_types`;';
    }
}