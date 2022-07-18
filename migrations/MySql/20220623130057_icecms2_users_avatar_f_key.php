<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2UsersAvatarFKey DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2UsersAvatarFKey extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'ALTER TABLE `users` 
ADD CONSTRAINT `users_avatar_fk` FOREIGN KEY (`avatar`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'ALTER TABLE `users` 
DROP FOREIGN KEY `users_avatar_fk`;
    ALTER TABLE `users` 
DROP INDEX `users_avatar_fk`;';
    }
}