<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default templates layout file
 *
 * @var AbstractController $this
 */

use iceCMS2\Controller\AbstractController;

if (!empty($this->breadcrumbs)) {
    ?><div class="container">
        <nav aria-label="breadcrumb" class="mb-3 mt-3"><ol class="breadcrumb mb-0">
            <?php

            $i = 0;
            $count = count($this->breadcrumbs);
            foreach ($this->breadcrumbs as $breadcrumb) {
                ++$i;
                if ($i === $count) {
                    $active = ' active';
                    $aHrefStart = '';
                    $aHrefEnd = '';
                } else {
                    $active = '';
                    $aHrefStart = '<a href="' . $breadcrumb['url'] . '">';
                    $aHrefEnd = '</a>';
                }

                echo '<li class="breadcrumb-item' . $active . '" aria-current="page">' . $aHrefStart . $breadcrumb['title'] . $aHrefEnd . '</li>';
            }

            ?>
        </ol></nav>
    </div><?php
}
