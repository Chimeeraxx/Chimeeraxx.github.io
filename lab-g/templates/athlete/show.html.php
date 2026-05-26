<?php

/** @var \App\Model\Athlete $athlete */
/** @var \App\Service\Router $router */

$title = "{$athlete->getName()} ({$athlete->getId()})";
$bodyClass = 'show';

ob_start(); ?>
    <h1><?= $athlete->getName() ?></h1>

    <article>
        <p><strong>Sport:</strong> <?= $athlete->getSportName(); ?></p>
        <p><strong>Age:</strong> <?= $athlete->getAge(); ?></p>
    </article>

    <ul class="action-list">
        <li>
            <a href="<?= $router->generatePath('athlete-index') ?>">Back to list</a>
        </li>
        <li>
            <a href="<?= $router->generatePath('athlete-edit', ['id' => $athlete->getId()]) ?>">Edit</a>
        </li>
    </ul>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';