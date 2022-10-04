<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Settings Class (make Settings object from $settings array in /Settings/ directory)
 */

namespace iceCMS2\Settings;

use Exception;
use stdClass;
use Throwable;

class Settings
{
    /**
     * Possible settings;
     *
     * possible values:
     * 1 - required
     * 0 - optional
     * [] - optional array
     */
    private const _POSSIBLE_SETTINGS = [
        'path' => 1,
        'template' => 1,
        'dev' => 1,
        'secret' => 1,
        'db' => [
            'type' => 1, 
            'name' => 1,
            'host' => 1,
            'port' => 1,
            'login' => 1,
            'pass' => 1,
            'encoding' => 1,
        ],
        'dbTest' => [
            'type' => 0,
            'name' => 0,
            'host' => 0,
            'port' => 0,
            'login' => 0,
            'pass' => 0,
            'encoding' => 0,
        ],
        'email' => [
            'mail' => 1,
            'port' => 1,
            'signature' => 1,
            'pass' => 1,
            'smtp' => 1,
        ],
        'sms' => 0,
        'site' => [
            'title' => 1,
            'primaryDomain' => 1,
            'redirectToPrimaryDomain' => 1,
            'localeSubdomain' => 1,
            'cssScriptsVersion' => 1,
            'jsScriptsVersion' => 1,
        ],
        'locales' => [],
        'logs' => [
            'period' => 1,
            'type' => 1,
        ],
        'cache' => [
            'useRedis' => 1,
            'redisHost' => 0,
            'redisPort' => 0,
        ],
        'routes' => [],
        'isUseCms' => 1,
    ];
    
    /** @var stdClass|null DataBase settings */
    public ?stdClass $db = null;

    /** @var stdClass|null Test DataBase settings */
    public ?stdClass $dbTest = null;

    /** @var stdClass|null default email settings */
    public ?stdClass $email = null;

    /** @var stdClass|null default sms settings */
    public ?stdClass $sms = null;

    /** @var stdClass|null settings errors */
    public ?stdClass $errors = null;

    /** @var stdClass|null site settings */
    public ?stdClass $site = null;

    /** @var array|null site locales */
    public ?array $locales = null;

    /** @var stdClass|null logs settings */
    public ?stdClass $logs = null;

    /** @var string|null site active locale */
    public ?string $locale = 'en';

    /** @var stdClass|null cache system settings */
    public ?stdClass $cache = null;

    /** @var array<string, mixed>|null site controllers routes */
    public ?array $routes = null;

    /** @var string|null secret passphrase for encryption */
    public ?string $secret = null;

    /** @var string|null site active template name */
    public ?string $template = null;

    /** @var string|null full path to the site directory */
    public ?string $path = null;

    /** @var bool development mode */
    public bool $dev = false;

    /** @var bool is use CMS system */
    public bool $isUseCms = true;

    /**
     * Class constructor
     *
     * @param array<string, mixed> $settings
     * @return void
     */
    public function __construct(array $settings)
    {

        $this->errors = new stdClass();
        $this->errors->flag = 0;
        $this->errors->text = 'Settings were not loaded';

        try {
            $this->_buildSettings($settings);

            $this->errors->flag = 0;
            $this->errors->text = 'Settings loaded ';

        } catch (Throwable $t) {
            $this->errors->flag = 1;
            $this->errors->text = 'Failed to load settings: ' . $t->getMessage();
        }

    }

    /**
     * Initializes the properties of the settings object from the passed array
     * TODO convert the repetitive code to a recursive function
     *
     * @param array<string, mixed> $settings
     * @return void
     * @throws Exception
     */
    private function _buildSettings(array $settings): void
    {
        foreach (self::_POSSIBLE_SETTINGS as $key => $value) {
            $paramName = $key;
            if (!is_array($value)) {
                if (!isset($settings[$paramName])) {
                    if ($value === 1) {
                        throw new Exception('Settings file error - there is no required field: ' . $paramName);
                    }
                    $settings[$paramName] = null;
                }
                $this->$paramName = $settings[$paramName];
            } else {
                if (count($value) === 0) {
                    if (isset($settings[$paramName])) {
                        $this->$paramName = $settings[$paramName];
                    } else {
                        $this->$paramName = null;
                    }
                } else {
                    if (!isset($settings[$paramName])) {
                        throw new Exception('Settings file error - there is no required field: ' . $paramName);
                    } elseif (!is_array($settings[$paramName])) {
                        throw new Exception('Settings file error - required field is no array: ' . $paramName);
                    }
                    $this->$paramName = new stdClass();
                    foreach (self::_POSSIBLE_SETTINGS[$paramName] as $key2 => $value2) {
                        $paramName2 = $key2;
                        if (!isset($settings[$paramName][$paramName2])) {
                            if ($value2 === 1) {
                                throw new Exception('Settings file error - there is no required field: ' . $paramName . '-' . $paramName2);
                            }
                            $settings[$paramName][$paramName2] = null;
                        }
                        $this->$paramName->$paramName2 = $settings[$paramName][$paramName2];
                    }
                }
            }
        }
    }
}