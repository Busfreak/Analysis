<section id="task-summary">
    <h2><?= $this->e($task['title']) ?></h2>
    <div class="task-summary-container color-<?= $task['color_id'] ?>">
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Status:') ?></strong>
                    <span>
                    <?php if ($task['is_active'] == 1): ?>
                        <?= t('open') ?>
                    <?php else: ?>
                        <?= t('closed') ?>
                    <?php endif ?>
                    </span>
                </li>
                <li>
                    <strong><?= t('Priority:') ?></strong> <span><?= $task['priority'] ?></span>
                </li>
                    <li>
                        <strong><?= t('Reference:') ?></strong>
                <?php if (! empty($task['reference'])): ?>
                    <span><?= $this->e($task['reference']) ?></span>
                <?php endif ?>
                    </li>
                    <li>
                        <strong><?= t('Complexity:') ?></strong>
                <?php if (! empty($task['score'])): ?>
                    <span><?= $this->e($task['score']) ?></span>
                <?php endif ?>
                    </li>
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                    <li>
                        <strong><?= t('Category:') ?></strong>
                <?php if (! empty($task['category_name'])): ?>
                        <span><?= $this->e($task['category_name']) ?></span>
                <?php endif ?>
                    </li>
                    <li>
                        <strong><?= t('Swimlane:') ?></strong>
                <?php if (! empty($task['swimlane_name'])): ?>
                        <span><?= $this->e($task['swimlane_name']) ?></span>
                <?php endif ?>
                    </li>
                <li>
                    <strong><?= t('Column:') ?></strong>
                    <span><?= $this->e($task['column_title']) ?></span>
                </li>
                <li>
                    <strong><?= t('Position:') ?></strong>
                    <span><?= $task['position'] ?></span>
                </li>
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Assignee:') ?></strong>
                    <span>
                    <?php if ($task['assignee_username']): ?>
                        <?= $this->e($task['assignee_name'] ?: $task['assignee_username']) ?>
                    <?php else: ?>
                        <?= t('not assigned') ?>
                    <?php endif ?>
                    </span>
                </li>
                    <li>
                        <strong><?= t('Creator:') ?></strong>
                <?php if ($task['creator_username']): ?>
                        <span><?= $this->e($task['creator_name'] ?: $task['creator_username']) ?></span>
                <?php endif ?>
                    </li>
                <li>
                    <strong><?= t('Due date:') ?></strong>
                <?php if ($task['date_due']): ?>
                    <span><?= $this->dt->date($task['date_due']) ?></span>
                <?php endif ?>
                </li>
                <li>
                    <strong><?= t('Time estimated:') ?></strong>
                <?php if ($task['time_estimated']): ?>
                    <span><?= t('%s hours', $task['time_estimated']) ?></span>
                <?php endif ?>
                </li>
                <li>
                    <strong><?= t('Time spent:') ?></strong>
                <?php if ($task['time_spent']): ?>
                    <span><?= t('%s hours', $task['time_spent']) ?></span>
                <?php endif ?>
                </li>
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Created:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_creation']) ?></span>
                </li>
                <li>
                    <strong><?= t('Modified:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_modification']) ?></span>
                </li>
                <li>
                    <strong><?= t('Completed:') ?></strong>
                <?php if ($task['date_completed']): ?>
                    <span><?= $this->dt->datetime($task['date_completed']) ?></span>
                <?php endif ?>
                </li>
                <?php if ($task['date_started']): ?>
                <li>
                    <strong><?= t('Started:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_started']) ?></span>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</section>
