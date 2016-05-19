<section id="main">
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No tasks found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>

<?php if (false): ?>
<pre>debug
<?= var_dump($task); ?>
</pre>
<?php endif ?>

<div>
    <?php foreach($paginator->getCollection() as $task): ?>
        <?php $subtasks = $this->AnalysisHelper->getSubTasks($task['id']); ?>
        <?php $comments = $this->AnalysisHelper->getComments($task['id']); ?>
        <?php $internal_links = $this->AnalysisHelper->getInternalTaskLinks($task['id']); ?>
        <?php $external_links = $this->AnalysisHelper->getExternalTaskLinks($task['id']); ?>

<?= $this->render('analysis:task/details', array('task' => $task)) ?>

<?= $this->render('analysis:task/description', array('task' => $task)) ?>

<?= $this->render('analysis:subtask/show', array(
    'task' => $task,
    'subtasks' => $subtasks,
    'editable' => false,
)) ?>

<?= $this->render('analysis:task_internal_link/show', array(
    'task' => $task,
    'links' => $internal_links,
    'project' => $project,
    'link_label_list' => $link_label_list,
    'editable' => false,
    'is_public' => false,
)) ?>

<?= $this->render('analysis:task_external_link/show', array(
    'task' => $task,
    'links' => $external_links,
    'project' => $project,
)) ?>

<?= $this->render('analysis:task/time_tracking_summary', array('task' => $task)) ?>

<?= $this->render('analysis:comments/show', array(
    'task' => $task,
    'comments' => $comments,
    'project' => $project,
    'editable' => false,
    'is_public' => true,
)) ?>

<hr><hr><hr>
<?php endforeach ?>
</div>
<?= $paginator ?>
    <?php endif ?>
</section>