    <table class="subtasks-table">
        <tr>
            <th class="column-40"><?= t('Title') ?></th>
            <th><?= t('Status') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td>
                <?= $subtask['title'] ?>
            </td>
            <td>
                <?= $subtask['status'] ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </td>
            <td>
                <ul class="no-bullet">
                    <li>
                        <?php if (! empty($subtask['time_spent'])): ?>
                            <strong><?= $this->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
                        <?php endif ?>

                        <?php if (! empty($subtask['time_estimated'])): ?>
                            <strong><?= $this->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                        <?php endif ?>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
