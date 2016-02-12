<div class="page-header">
    <h2><?= t('Task distribution') ?></h2>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
    <section id="analytic-task-repartition">


    <div id="chart" data-metrics='<?= json_encode($metrics, JSON_HEX_APOS) ?>'></div>

    <table>
        <tr>
            <th><?= t('Column') ?></th>
            <th><?= t('Number of tasks') ?></th>
            <th><?= t('Percentage') ?></th>
        </tr>
        <?php foreach ($metrics as $metric): ?>
        <tr>
            <td>
                <?= $this->e($metric['column_title']) ?>
            </td>
            <td>
                <?= $metric['nb_tasks'] ?>
            </td>
            <td>
                <?= n($metric['percentage']) ?>%
            </td>
        </tr>
        <?php endforeach ?>
 
        <tr>
            <td>
                <?= t('Closed') ?>
            </td>
            <td>
                <?= $this->e($closed['count']) . '/' . e($closed['total']) ?>
            </td>
            <td>
                <?= $this->e($closed['percentage']) ?>%
            </td>
        </tr>
   </table>

    </section>
<?php endif ?>