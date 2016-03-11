<?php if (! empty($links)): ?>
<div class="page-header">
    <h2><?= t('Internal links') ?></h2>
</div>
<table id="links" class="table-small table-stripped">
    <tr>
        <th class="column-20"><?= t('Label') ?></th>
        <th class="column-30"><?= t('Task') ?></th>
        <th class="column-20"><?= t('Project') ?></th>
        <th><?= t('Column') ?></th>
        <th><?= t('Assignee') ?></th>
    </tr>
    <?php foreach ($links as $label => $grouped_links): ?>
        <?php $hide_td = false ?>
        <?php foreach ($grouped_links as $link): ?>
        <tr>
            <?php if (! $hide_td): ?>
                <td rowspan="<?= count($grouped_links) ?>"><?= t('This task') ?> <strong><?= t($label) ?></strong></td>
                <?php $hide_td = true ?>
            <?php endif ?>

            <td>
                <?= $this->url->link(
                    $this->text->e('#'.$link['task_id'].' '.$link['title']),
                    'task',
                    'show',
                    array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                    false,
                    $link['is_active'] ? '' : 'task-link-closed'
                ) ?>

                <br>

                <?php if (! empty($link['task_time_spent'])): ?>
                    <strong><?= $this->text->e($link['task_time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($link['task_time_estimated'])): ?>
                    <strong><?= $this->text->e($link['task_time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
            <td><?= $this->text->e($link['project_name']) ?></td>
            <td><?= $this->text->e($link['column_title']) ?></td>
            <td>
                <?php if (! empty($link['task_assignee_username'])): ?>
                    <?= $this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    <?php endforeach ?>
</table>

<?php endif ?>
