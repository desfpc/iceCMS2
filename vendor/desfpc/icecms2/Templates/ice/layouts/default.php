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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link href="/css/ice.css" rel="stylesheet">
    <?php $this->_echoCSS(); ?>

    <title><?= $fullTitle ?></title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '_mainMenu.php'; ?>
<?php include '_breadCrumbs.php'; ?>
<?php if ($this->isTemplate) { $this->_echoTemplateBody(); } ?>
<?php include '_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"
        integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS"
        crossorigin="anonymous"></script>
<?php $this->_echoJS() ?>
<?php $this->_echoOnReadyJS() ?>
</body>
</html>