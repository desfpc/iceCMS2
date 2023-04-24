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

$fullTitle = $this->settings->site->title;
if ($this->title !== '') {
    $fullTitle .= ' | ' . $this->title;
}

$this->_echoHeaders(); ?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $this->description ?>">
    <meta name="keyword" content="<?= $this->keyword ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link href="/css/ice.css" rel="stylesheet">
    <?php $this->_echoCSS(); ?>

    <title><?= $fullTitle ?></title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '_mainMenu.php'; ?>
<?php if ($this->isTemplate) { $this->_echoTemplateBody(); } ?>
<?php include '_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
<?php $this->_echoJS() ?>
<?php $this->_echoOnReadyJS() ?>
</body>
</html>