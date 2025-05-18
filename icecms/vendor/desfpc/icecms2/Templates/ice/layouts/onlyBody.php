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
?>

<?php if ($this->isTemplate) { $this->_echoTemplateBody(); } ?>
<?php include '_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"
        integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS"
        crossorigin="anonymous"></script>
<?php $this->_echoJS() ?>
<?php $this->_echoOnReadyJS() ?>
</body>
</html>