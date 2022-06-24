<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File entity class
 */

namespace iceCMS2\Models;

class File extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'files';

    /**
     * Set Entity from POST value
     *
     * @param string $postValue
     * @param int|null $userId
     * @param bool|null $private
     * @return bool
     */
    public function saveFromPost(string $postValue, string $filetype = 'file', ?int $userId = null, ?bool $private = false): bool
    {
        
    }

    /**
     * Getting File/Image favicon image url
     *
     * @return string
     */
    public function getFaviconUrl(): string
    {

    }

    /**
     * Create favicon for image file
     *
     * @return bool
     */
    public function createFavicon(): bool
    {

    }

    /**
     * Creating image variant by width/height/waterMark
     *
     * @param int|null $x
     * @param int|null $y
     * @param int|null $waterMark
     * @return bool
     */
    public function createImageSize(?int $x = null, ?int $y = null, ?int $waterMark = null): bool
    {

    }

    /**
     * Getting file path in OS
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getPath(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {
        
    }

    /**
     * Getting file URL for web
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getUrl(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {

    }
}