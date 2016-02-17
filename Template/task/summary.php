    <?= $this->render('analysis:analytic/filters', array(
        'project' => $project,
        'filters' => $filters,
        'categories' => $categories,
        'swimlanes' => $swimlanes,
    )) ?>

    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No tasks found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>

<?php if (false): ?>
<pre>debug
<?= var_dump($debug); ?>
</pre>
<?php endif ?>



    <?php foreach($paginator->getCollection() as $task): ?>

    <?= $this->render('analysis:task/details', array(
        'task' => $task,
        'project' => $project,
        )) ?>

    <?= $this->render('analysis:task/description', array('task' => $task)) ?>

    <?= $this->render('analysis:subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks[$task['id']],
        'project' => $project,
        )) ?>

    <?= $this->render('analysis:tasklink/show', array(
        'task' => $task,
        'links' => $links[$task['id']],
        )) ?>

    <?= $this->render('analysis:task/time_tracking_summary', array('task' => $task)) ?>

    <?= $this->render('analysis:task/comments', array(
        'task' => $task,
        'comments' => $comments[$task['id']],
        'project' => $project,
        'editable' => false,
    )) ?>

    <?php endforeach ?>
    <?php endif ?>
