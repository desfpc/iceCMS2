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

use desfpc\Visualijoper\Visualijoper;
use iceCMS2\Controller\AbstractController;

if ($this->settings->dev === true) {
    $this->jsReady .= "

let devModeBtn = document.getElementById('dev_btn');
devModeBtn.addEventListener('click' , () => {
    let devContent = document.getElementById('dev_content');
    if (devModeBtn.classList.contains('active')) {
        devContent.classList.remove('active');
        devModeBtn.classList.remove('active');
    } else {
        devContent.classList.add('active');
        devModeBtn.classList.add('active');
    }
});

";

    $vj = new visualijoper($this, 'Controller data', true);

    $devMode = '<div class="developer_mode">
    <div class="dev_btn" id="dev_btn">
        dev
    </div>
    <div class="content" id="dev_content">
        ' . $vj->render() . '
    </div>
</div>';
} else {
    $devMode = '';
}

?><div class="container-fluid mt-auto bg-dark footer">
    <div class="container">
        <div class="row">
            <div class="col">
                <a class="navbar-brand" href="/">
                    <img src="/img/iceCMS2/logofw.svg" alt="" width="30" height="30"> IceCMS2
                </a>
            </div>
            <div class="col text-end developed">
                by <a href="https://nozhove.com" target="_blank"><img src="https://nozhove.com/nozhove_pixel.png" width="30" alt="Developed by Nozhove.com"> nozhove.com</a>
            </div>
        </div>
    </div>
</div><?= $devMode ?>