<?php

namespace iceCMS2\Search;

use iceCMS2\Settings\Settings;
use Exception;

final class SearchFactory
{
    /**
     * @param Settings $settings
     *
     * @return Elastic|null
     * @throws Exception
     */
    public static function instance(Settings $settings): ?Elastic
    {
        return match ($settings->search->type) {
            'Elastic' => new Elastic(),
            default => throw new Exception('error SearchFactory')
        };
    }
}