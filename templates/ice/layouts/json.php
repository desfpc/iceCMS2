<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Default templates layout file
 *
 * @var iceCMS2\Controller\AbstractController $this
 */
$this->_headers = $this->_getDefaultHeaders();
$this->_headers[] = 'Content-Type: application/json; charset=utf-8';
$this->_echoHeaders();
$this->_echoTemplateBody();