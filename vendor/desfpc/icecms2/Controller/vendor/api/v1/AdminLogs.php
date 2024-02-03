<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 Test Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\FileImage;
use iceCMS2\Tools\Exception;
use iceCMS2\Models\User;

class AdminLogs extends AbstractController implements ControllerInterface
{
    /** @var string  */
    private const PATH = '../logs';
    
    /**
     * @return void
     */
    public function getLogByNameFile(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        $response = $this->routing->pathInfo['query_vars']['nameFile'];
        $readFile = "The name file $response not found";

        $dir = dir(self::PATH);
        while (false !== ($item = $dir->read())) {
            if ($item === $response) {
                $readFile = file_get_contents(self::PATH . '/' . $item, true);
                $readFile = nl2br($readFile); 
            }
        }

        $this->renderJson([$readFile], true);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function getLogByAliasAndCreateTime(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        $response = $this->routing->pathInfo['query_vars']['aliasAndCreateTime'];
        $response = explode('_', $response);
        $result = "The alias $response[0] not found for $response[1]";

        $query = 'SELECT value FROM logs WHERE alias = ? and DATE(created_time) = ?';
        $result = $this->_db->queryBinded($query,[
            0 =>$response[0],
            1 =>$response[1]
        ]);

        foreach ($result as &$item) {
            $item = $item['value'];
        }

        $result = implode('</br>', $result);

        $this->renderJson([$result], true);
    }
}