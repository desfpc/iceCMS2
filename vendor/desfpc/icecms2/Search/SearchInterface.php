<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Elastic Interface class
 */

namespace iceCMS2\Search;

use iceCMS2\Settings\Settings;

interface SearchInterface
{
    /**
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function search(Settings $settings, array $params): string;

    /**
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function create(Settings $settings, array $params): string;

    /**
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function update(Settings $settings, array $params): string;

    /**
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function delete(Settings $settings, array $params): string;
}