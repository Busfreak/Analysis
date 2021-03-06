<?php if (! empty($subtasks)): ?>
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Sub-Tasks') ?></h3>
    </div>
    <div class="accordion-content">
        <?= $this->render('subtask/table', array(
            'subtasks' => $subtasks,
            'task' => $task,
            'editable' => $editable
        )) ?>
    </div>
<?php endif ?>
