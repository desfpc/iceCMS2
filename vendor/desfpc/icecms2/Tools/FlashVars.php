<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Flash Variables class
 */

namespace iceCMS2\Tools;

class FlashVars
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }

        if (!isset($_SESSION['flashVars'])) {
            $_SESSION['flashVars'] = [];
        }
    }

    /**
     * Flash Variable setter
     *
     * @param string $name
     * @param mixed $value
     * @param bool $rewrite
     * @return void
     */
    public function set(string $name, $value, bool $rewrite = true): void
    {
        if ($rewrite || !isset($_SESSION['flashVars'][$name]) || (empty($_SESSION['flashVars'][$name]))) {
            $_SESSION['flashVars'][$name] = $value;
        } else {
            if (!is_array($_SESSION['flashVars'][$name]) || !isset($_SESSION['flashVars'][$name][0])) {
                $_SESSION['flashVars'][$name] = [$_SESSION['flashVars'][$name], $value];
            } else {
                $_SESSION['flashVars'][$name][] = $value;
            }
        }
    }

    /**
     * Flash Variable getter
     *
     * @param string $name
     * @return false|mixed
     */
    public function get(string $name)
    {
        if (isset($_SESSION['flashVars'][$name])) {
            $value = $_SESSION['flashVars'][$name];
            unset($_SESSION['flashVars'][$name]);
            return $value;
        }
        return false;
    }
}