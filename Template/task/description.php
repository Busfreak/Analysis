<?php if (! empty($task['description'])): ?>
    <div id="description" class="task-show-section">
        <div class="page-header">
            <h2><?= t('Description') ?></h2>
        </div>

        <article class="markdown task-show-description">
            <?= $this->text->markdown(
                $task['description'],
                array(
                    'controller' => 'task',
                    'action' => 'show',
                    'params' => array(
                        'project_id' => $task['project_id']
                    )
                )
            ) ?>
        </article>
    </div>
<?php endif ?>