<?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>

<div class="page-header">
    <h2><?= t('Time tracking') ?></h2>
</div>

<ul class="listing">
    <li style="display: inline; list-style-type: none; padding-right: 20px;"><?= t('Estimate:') ?> <strong><?= $this->text->e($task['time_estimated']) ?></strong> <?= t('hours') ?></li>
    <li style="display: inline; list-style-type: none; padding-right: 20px;"><?= t('Spent:') ?> <strong><?= $this->text->e($task['time_spent']) ?></strong> <?= t('hours') ?></li>
    <li style="display: inline; list-style-type: none; padding-right: 20px;"><?= t('Remaining:') ?> <strong><?= $this->text->e($task['time_estimated'] - $task['time_spent']) ?></strong> <?= t('hours') ?></li>
</ul>

<?php endif ?>