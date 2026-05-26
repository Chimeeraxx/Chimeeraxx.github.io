<?php

/** @var \App\Model\Athlete[] $athletes */
/** @var \App\Service\Router $router */

$title = 'Athlete List';
$bodyClass = 'index';

ob_start(); ?>
    <h1>Athletes List</h1>

    <a href="<?= $router->generatePath('athlete-create') ?>">Create new</a>

    <ul class="index-list">
        <?php foreach ($athletes as $athlete): ?>
            <li>
                <h3><?= $athlete->getName() ?></h3>
                <p>
                    Sport: <?= $athlete->getSportName() ?><br>
                    Age: <?= $athlete->getAge() ?>
                </p>

                <ul class="action-list">
                    <li>
                        <a href="<?= $router->generatePath('athlete-show', ['id' => $athlete->getId()]) ?>">Details</a>
                    </li>
                    <li>
                        <a href="<?= $router->generatePath('athlete-edit', ['id' => $athlete->getId()]) ?>">Edit</a>
                    </li>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';