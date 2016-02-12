    <?= $this->render('analysis:analytic/filters', array(
        'project' => $project,
        'filters' => $filters,
        'categories' => $categories,
        'swimlanes' => $swimlanes,
    )) ?>

    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No tasks found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>


<pre>debug
<?= var_dump($debug); ?>
</pre>

<div>
<?php foreach($paginator->getCollection() as $task): ?>
<?= $this->render('analysis:task/details', array(
    'task' => $task,
    'project' => $project,
    'editable' => $this->user->hasProjectAccess('taskmodification', 'edit', $project['id']),
)) ?>

<?= $this->render('analysis:task/description', array('task' => $task)) ?>

<?= $this->render('subtask/show', array(
    'task' => $task,
    'subtasks' => $subtasks,
    'project' => $project,
    'users_list' => isset($users_list) ? $users_list : array(),
    'editable' => false,
    'redirect' => 'task',
)) ?>

<?= $this->render('analysis:tasklink/show', array(
    'task' => $task,
    'links' => $links,
    'link_label_list' => $link_label_list,
    'editable' => false,
    'is_public' => false,
)) ?>

<?= $this->render('analysis:task/time_tracking_summary', array('task' => $task)) ?>

<?= $this->render('analysis:task/comments', array(
    'task' => $task,
    'comments' => $comments,
    'project' => $project,
    'editable' => false,
)) ?>


<?php endforeach ?>
</div>
    <?php endif ?>
