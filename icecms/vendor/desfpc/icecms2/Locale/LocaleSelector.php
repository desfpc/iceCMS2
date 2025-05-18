<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * LocaleSelector class
 */

namespace iceCMS2\Locale;


use iceCMS2\Authorization\AuthorizationFactory;
use iceCMS2\Loader\Loader;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class LocaleSelector
{
    /** @var string Authorization type */
    private const AUTHORIZE_TYPE = 'session';

    public static function getBrowserLanguage(Settings $settings): string
    {
        $langStr = empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? '' : strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $locales = [];

        if (!empty($langStr)) {
            //*
            if ($langStr === '*') {
                return $settings->defaultLocale;
            }

            $langsArr = explode(',', $langStr);

            foreach ($langsArr as $lang) {
                $lang = explode(';', $lang)[0];
                $lang = explode('-', $lang)[0];
                $locales[] = $lang;
            }

            if (!empty($locales)) {
                foreach ($locales as $locale) {
                    if (in_array($locale, $settings->locales)) {
                        return $locale;
                    }
                }
            }
        }

        return $settings->defaultLocale;
    }

    /**
     * @param Loader $loader
     *
     * @return void
     * @throws Exception
     */
    public static function setLocale(Loader $loader): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $host = mb_strtolower($_SERVER['HTTP_HOST'], 'UTF-8');
        $mainDomain = $loader->settings->site->primaryDomain;
        $domainArr = explode('.', $host);

        //принудительно меняем язык в сессии, если передан GET параметр setLocale
        if (isset($_GET['setLocale']) && in_array($_GET['setLocale'], $loader->settings->locales)) {
            $_SESSION['locale'] = $_GET['setLocale'];
            $loader->settings->locale = $_GET['setLocale'];
        } elseif ($host !== $mainDomain && in_array($domainArr[0], $loader->settings->locales)) {
            $_SESSION['locale'] = $domainArr[0];
            $loader->settings->locale = $domainArr[0];
            return;
        }
        
        // Если мы на основном домене и нет выбранного языка
        if ($host === $mainDomain && !isset($_SESSION['locale'])) {
            $browserLocale = self::getBrowserLanguage($loader->settings);
            $_SESSION['locale'] = $browserLocale;
            $loader->settings->locale = $browserLocale;
            
            if ($browserLocale !== $loader->settings->defaultLocale) {
                $redirectUrl = 'https://' . $browserLocale . '.' . $mainDomain . $_SERVER['REQUEST_URI'];
                header('Location: ' . $redirectUrl);
                exit();
            }
        } elseif ($host === $mainDomain) {
            $loader->settings->locale = $loader->settings->defaultLocale;
        }
        
        // В остальных случаях используем язык из сессии или по умолчанию
        if (!isset($_SESSION['locale'])) {
            $_SESSION['locale'] = $loader->settings->defaultLocale;
        }

        $loader->settings->locale = $_SESSION['locale'];

        // Язык выбрали - радуемся и проверяем: если домен - основной, а язык - нет, то редиректим
        if ($host === $mainDomain && $loader->settings->locale !== $loader->settings->defaultLocale) {
            $redirectUrl = 'https://' . $loader->settings->locale . '.' . $mainDomain . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirectUrl);
            exit();
        }
    }
}