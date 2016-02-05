        <li <?= $this->app->checkMenuSelection('Analysis', 'index') ?>>
            <?= $this->url->link(t('Advanced analysis'), 'Analysis', 'index', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
            <?php if($this->app->getRouterController() === 'Analysis'): ?>
                <ul>
                    <li <?= $this->app->checkMenuSelection('Analysis', 'index') ?>>
                        <?= $this->url->link(t('Task distribution'), 'analysis', 'tasks', array('plugin' => 'Analysis','project_id' => $project['id'])) ?>
                    <?php echo $this->container['analysis']->getSwimlanesList($project['id']); ?>
                    </li>
        </ul>


        <li <?= $this->app->checkMenuSelection('analysis', 'users') ?>>
            <?= $this->url->link(t('User repartition'), 'analysis', 'users', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analysis', 'cfd') ?>>
            <?= $this->url->link(t('Cumulative flow diagram'), 'analysis', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analysis', 'burndown') ?>>
            <?= $this->url->link(t('Burndown chart'), 'analysis', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analysis', 'averageTimeByColumn') ?>>
            <?= $this->url->link(t('Average time into each column'), 'analysis', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analysis', 'leadAndCycleTime') ?>>
            <?= $this->url->link(t('Lead and cycle time'), 'analysis', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('analysis', 'compareHours') ?>>
            <?= $this->url->link(t('Estimated vs actual time'), 'analysis', 'compareHours', array('project_id' => $project['id'])) ?>
        </li>
                </ul>
            <?php endif ?>
        </li><?php echo $this->app->getRouterController() . ':' . $this->app->getRouterAction(); ?>