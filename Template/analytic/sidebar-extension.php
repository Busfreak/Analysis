        <li <?= $this->app->checkMenuSelection('Analysis', 'index') ?>>
            <?= $this->url->link(t('Advanced analysis'), 'analysis', 'tasks', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
        </li>
<?php echo $this->app->getRouterController() . ':' . $this->app->getRouterAction(); ?>