<?php

/** @var \App\Model\Athlete $athlete */
/** @var \App\Service\Router $router */

$title = 'Create Athlete';
$bodyClass = "edit";

ob_start(); ?>
    <h1>Create Athlete</h1>

    <form action="<?= $router->generatePath('athlete-create') ?>" method="post" class="edit-form">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
        <input type="hidden" name="action" value="athlete-create">
    </form>

    <a href="<?= $router->generatePath('athlete-index') ?>">Back to list</a>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';