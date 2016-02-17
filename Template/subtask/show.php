<?php if (! empty($subtasks)): ?>
<div class="page-header">
    <h2><?= t('Sub-Tasks') ?></h2>
</div>

<div id="subtasks">

    <?= $this->render('analysis:subtask/table', array('subtasks' => $subtasks, 'task' => $task)) ?>

</div>
<?php endif ?>
