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

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Helpers\Strings;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class Routing
{
    /** @var array Parsed path information */
    public array $pathInfo;

    /** @var string Cache key for routes tree */
    private const CACHE_KEY_ROUTES_TREE = 'Routing_Tree';
    /** @var string Cache key for routes match */
    private const CACHE_KEY_ROUTES_MATCH = 'Routing_Match';
    
    /**
     * Route info
     *
     * @var array
     */
    public array $route = [
        'controller' => 'ServerErrors',
        'method' => 'main',
        'useVendor' => false,
        'parts' => [],
    ];

    /**
     * Getting route info from $pathInfo and Settings
     *
     * @param Settings $settings
     * @param bool $useCache
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRoute(Settings $settings, bool $useCache = true): void
    {
        $cacher = CachingFactory::instance($settings);
        if (empty($this->pathInfo['call_parts'])) {
            $this->pathInfo['call_parts'][] = '';
        }
        if (!empty($settings->routes)) {
            $rouresTreeKey = Strings::cacheKey($settings, self::CACHE_KEY_ROUTES_TREE);
            if ($useCache && $cacher->has($rouresTreeKey)) {
                $rouresTree = $cacher->get($rouresTreeKey, true);
            } else {
                $rouresTree = [];
                foreach ($settings->routes as $route => $value)
                {
                    $routeParts = explode('/', (string)$route);
                    $realPartsCnt = 0;
                    $realRouteKey = '';
                    $routeReal = [];
                    if (!empty($routeParts)) {
                        foreach ($routeParts as $part) {
                            if (mb_substr($part, 0, 1, 'UTF-8') === '$') {
                                $routeReal[] = [
                                    'partName' => str_replace('$', '', $part),
                                    'type' => 'value',
                                ];
                            } else {
                                $routeReal[] = [
                                    'partName' => $part,
                                    'type' => 'route',
                                ];
                                ++$realPartsCnt;
                                if ($realRouteKey !== '') {
                                    $realRouteKey .= '/';
                                }
                                $realRouteKey .= $part;
                            }
                        }
                    }
                    if ($realPartsCnt > 0) {
                        $rouresTree[] = [
                            'route' => $route,
                            'key' => $realRouteKey,
                            'value' => $value,
                            'parts' => $routeReal,
                        ];
                    }
                }
                if ($useCache) {
                    $cacher->set($rouresTreeKey, json_encode($rouresTree), 60);
                }
            }

            // Finding a Match between a Request Query String and a Route
            foreach ($rouresTree as $route) {
                $this->route['method'] = 'main';
                $this->route['parts'] = [];
                $addedQueryVars = [];
                $i = -1;
                foreach ($this->pathInfo['call_parts'] as $callPart) {
                    ++$i;
                    if (!isset($route['parts'][$i])) {
                        if ($this->route['method'] === 'main') {
                            $this->route['method'] = Strings::snakeToCamel($callPart);
                        } else {
                            $this->route['parts'][] = $callPart;
                        }
                        continue;
                    }
                    $part = $route['parts'][$i];

                    if($part['type'] === 'route') {
                        if (mb_strtolower($callPart, 'UTF-8') !== $part['partName']) {
                            continue(2);
                        }
                    } else {
                        $addedQueryVars[$part['partName']] = $callPart;
                    }
                }
                if (isset($route['parts'][$i+1])) {
                    continue;
                }

                if (is_array($route['value'])) {
                    $this->route['controller'] = $route['value']['controller'];
                    if (isset($route['value']['method'])) {
                        $this->route['method'] = $route['value']['method'];
                    }
                    if (isset($route['value']['useVendor'])) {
                        $this->route['useVendor'] = $route['value']['useVendor'];
                    }
                } else {
                    $this->route['controller'] = $route['value'];
                }
                $this->pathInfo['query_vars'] = array_merge($this->pathInfo['query_vars'], $addedQueryVars);
                break;
            }
        }
        if ($this->route['controller'] === 'ServerErrors' && $this->route['method'] === 'main') {
            $this->route['method'] = 'notFound';
            $this->route['useVendor'] = true;
        }
    }

    /**
     * URL parsing
     *
     * @return void
     */
    public function parseURL(): void
    {
        $path = [];

        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestPath = explode('?', $_SERVER['REQUEST_URI']);

            $path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
            $path['call_utf8'] = substr(urldecode($requestPath[0]), strlen($path['base']) + 1);
            $path['call'] = utf8_decode($path['call_utf8']);
            if ($path['call'] == basename($_SERVER['PHP_SELF'])) {
                $path['call'] = '';
            }
            $path['call_parts'] = explode('/', $path['call_utf8']);
            if (!empty($path['call_parts'])) {
                if ($path['call_parts'][count($path['call_parts'])-1] === '') {
                    unset($path['call_parts'][count($path['call_parts'])-1]);
                }
            }

            if (isset($requestPath[1])) {
                $path['query_utf8'] = urldecode($requestPath[1]);
                $path['query'] = utf8_decode(urldecode($requestPath[1]));
            } else {
                $path['query_utf8'] = '';
                $path['query'] = '';
            }
            $vars = explode('&', $path['query_utf8']);
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