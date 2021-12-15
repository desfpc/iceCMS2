<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Settings Class
 * TODO store in cache
 * TODO unit tests
 */

namespace iceCMS2\Settings;

use Exception;
use stdClass;
use Throwable;

class Settings
{
    /** @var stdClass|null DataBase settings */
    public ?stdClass $db = null;

    /** @var stdClass|null default email settings */
    public ?stdClass $email = null;

    /** @var stdClass|null default sms settings */
    public ?stdClass $sms = null;

    /** @var stdClass|null settings errors */
    public ?stdClass $errors = null;

    /** @var stdClass|null site settings */
    public ?stdClass $site = null;

    /** @var stdClass|null cache system settings */
    public ?stdClass $cache = null;

    /** @var stdClass|null site controllers routes */
    public ?stdClass $routes = null;

    /** @var string|null secret passphrase for encryption */
    public ?string $secret = null;

    /** @var string|null site active template name */
    public ?string $template = null;

    /** @var string|null full path to the site directory */
    public ?string $path = null;

    /** @var bool|null development mode */
    public ?bool $isDev = null;

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
            /**
             * @var array<string, int|array<string, int>> array with possible settings;
             *
             * possible values:
             * 1 - required
             * 0 - optional
             * [] - optional array
             */
            $possibleSettings = [];

            $possibleSettings['path'] = 1;
            $possibleSettings['template'] = 1;
            $possibleSettings['dev'] = 1;
            $possibleSettings['secret'] = 1;

            $possibleSettings['db']['type'] = 1;
            $possibleSettings['db']['name'] = 1;
            $possibleSettings['db']['host'] = 1;
            $possibleSettings['db']['port'] = 1;
            $possibleSettings['db']['login'] = 1;
            $possibleSettings['db']['pass'] = 1;
            $possibleSettings['db']['encoding'] = 1;

            $possibleSettings['email']['mail'] = 1;
            $possibleSettings['email']['port'] = 1;
            $possibleSettings['email']['signature'] = 1;
            $possibleSettings['email']['pass'] = 1;
            $possibleSettings['email']['smtp'] = 1;

            // TODO add SMS possible values
            $possibleSettings['sms'] = 0;

            $possibleSettings['site']['title'] = 1;
            $possibleSettings['site']['primary_domain'] = 1;
            $possibleSettings['site']['redirect_to_primary_domain'] = 1;
            $possibleSettings['site']['language_subdomain'] = 1;

            $possibleSettings['cache']['use_redis'] = 1;
            $possibleSettings['cache']['redis_host'] = 0;
            $possibleSettings['cache']['redis_port'] = 0;

            $possibleSettings['routes'] = [];

            $this->_buildSettings($possibleSettings, $settings);

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
     * @param array<string, int|array<string, int>|array> $possibleSettings
     * @param array<string, mixed> $settings
     * @return void
     * @throws Exception
     */
    private function _buildSettings(array $possibleSettings, array $settings): void
    {
        foreach ($possibleSettings as $key => $value) {
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
                    if (!isset($settings[$paramName]) && !is_array($settings[$paramName])) {
                        throw new Exception('Settings file error - there is no required field or it is not an array : ' . $paramName);
                    }
                    $this->$paramName = new stdClass();
                    foreach ($possibleSettings[$paramName] as $key2 => $value2) {
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