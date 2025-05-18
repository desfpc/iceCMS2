<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;

/** @var AbstractController $this */
if ($this->routing->pathInfo['call_parts'][1] === 'files') {
    $filesClass = 'active';
    $iSizesClass = '';
} else {
    $filesClass = '';
    $iSizesClass = 'active';
}

?><div class="btn-group pb-4" role="group" aria-label="Files Menu">
    <a href="/admin/files/" class="btn btn-primary <?php echo $filesClass; ?>" aria-current="page">Files</a>
    <a href="/admin/image-sizes/" class="btn btn-primary <?php echo $iSizesClass; ?>" aria-current="page">Image Sizes</a>
</div>