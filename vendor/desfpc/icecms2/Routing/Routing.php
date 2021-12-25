<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Routing class
 */

namespace iceCMS2\Routing;

class Routing
{
    /**
     * Parsed path information
     *
     * @var array
     */
    public array $pathInfo;

    /**
     * URL parsing
     *
     * @return void
     */
    public function parseURL(): void
    {
        $path = [];

        if (!empty($_SERVER['REQUEST_URI'])) {
            $request_path = explode('?', $_SERVER['REQUEST_URI']);

            $path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
            $path['call_utf8'] = substr(urldecode($request_path[0]), strlen($path['base']) + 1);
            $path['call'] = utf8_decode($path['call_utf8']);
            if ($path['call'] == basename($_SERVER['PHP_SELF'])) {
                $path['call'] = '';
            }
            $path['call_parts'] = explode('/', $path['call']);
            if (!empty($path['call_parts'])) {
                if ($path['call_parts'][count($path['call_parts'])-1] === '') {
                    unset($path['call_parts'][count($path['call_parts'])-1]);
                }
            }

            if (isset($request_path[1])) {
                $path['query_utf8'] = urldecode($request_path[1]);
                $path['query'] = utf8_decode(urldecode($request_path[1]));
            } else {
                $path['query_utf8'] = '';
                $path['query'] = '';
            }
            $vars = explode('&', $path['query']);
            foreach ($vars as $var) {
                $t = explode('=', $var);
                if (isset($t[1])) {
                    $path['query_vars'][$t[0]] = $t[1];
                } else {
                    $path['query_vars'][$t[0]] = '';
                }

            }
        }
        $this->pathInfo = $path;
    }
}