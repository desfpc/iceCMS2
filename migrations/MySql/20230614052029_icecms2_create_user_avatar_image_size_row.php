<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * icecms2_create_user_avatar_image_size_row DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class icecms2_create_user_avatar_image_size_row extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "INSERT INTO `image_sizes` (`id`, `width`, `height`, `is_crop`, `string_id`)
                VALUES (NULL, 200, 200, 1, 'user_avatar');";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return "DELETE FROM `image_sizes` WHERE `string_id` ='user_avatar';";
    }
}