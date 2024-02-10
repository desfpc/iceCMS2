<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app Class
 */

namespace iceCMS2\Cli;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class App
{
    /** @var Settings app settings */
    private Settings $_settings;

    /** @var array console arguments */
    private array $_argv;

    /** @var string */
    private const DIR_FRAMEWORK = __DIR__ . '/../Commands';

    /** @var string */
    private const DIR_CUSTOM = __DIR__ . '/../../../../classes/commands';

    /**
     * Class constructor
     *
     * @param array $settings
     * @param array $argv
     *
     * @throws Exception
     */
    public function __construct(array $settings, array $argv)
    {
        $this->_settings = new Settings($settings);
        if ($this->_settings->errors->flag === 1) {
            throw new Exception($this->_settings->errors->text);
        }
        if (!empty($argv)) {
            unset($argv[0]);
        }
        if (empty($argv)) {
            throw new Exception('No command found. Type "php cli.php help" for command help.');
        }
        $this->_argv = $argv;
        $this->existMethod();
    }

    /**
     * @return void
     */
    private function existMethod(): void
    {
        $method = ($this->_argv[1] && mb_substr($this->_argv[1], -8) === '-command') ? 'help' : $this->_argv[1];
        var_dump($method);
        $param = $this->_argv;
        if ('help' === $method) {
            $this->help();
        } else {
            $className = str_replace(
                    ' ',
                    '',
                    ucwords(str_replace('-', ' ', $method))
                ). 'Command';
            $fileName = $className. '.php';
            $fullPath = $this->findFileByName($fileName);
            $namespace = $this->getNamespaceFromFile($fullPath);
            $fullClassName = $namespace . '\\' . $className;
            if (class_exists($fullClassName)) {
                $classInstance = new $fullClassName();
                if (method_exists($classInstance, 'run')) {
                    echo $classInstance->run($this->_settings, $param) . "\n";
                }
            }
        }
    }

    /**
     * @return void
     */
    private function help(): void
    {
        echo "\n" . 'IceCMS2 Help';
        echo "\n" . 'Type framework command after php cli.php:';
        echo "\n";
        $this->scanDirectory(self::DIR_FRAMEWORK);
        echo "\n" . 'Type custom command after php cli.php:';
        echo "\n";
        $this->scanDirectory(self::DIR_CUSTOM);
    }

    /**
     * @param $dir
     *
     * @return void
     */
    private function scanDirectory($dir): void
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'AbstractCommand.php') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->scanDirectory($path);
            } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php' && str_contains($file, 'Command.php')) {
                $this->getInfo($path);
            }
        }
    }

    /**
     * @param string $file
     *
     * @return void
     */
    private function getInfo(string $file): void
    {
        $namespace = $this->getNamespaceFromFile($file);
        $fileName = basename($file, '.php');

        $fullClassName = $namespace . '\\' . $fileName;

        if (class_exists($fullClassName)) {
            $classInstance = new $fullClassName();
            if (property_exists($classInstance, 'info')) {
                echo "      " . $classInstance->info . "\n";
            }
        }
    }

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    private function getNamespaceFromFile(string $filePath): ?string
    {
        $result = null;
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^\s;]+)/', $content, $matches)) {
            $result = $matches[1];
        }

        return $result;
    }

    /**
     * @param string $fileName
     *
     * @return string|null
     */
    private function findFileByName(string $fileName): ?string
    {
        $result = null;
        $dirs = [
            self::DIR_FRAMEWORK,
            self::DIR_CUSTOM,
        ];

        foreach ($dirs as $dir) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getFilename() === $fileName) {
                    $result = $file->getPathname();
                }
            }
        }
        return $result;
    }
}