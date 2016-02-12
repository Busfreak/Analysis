        <li <?= $this->app->checkMenuSelection('Analysis', 'tasks') ?>>
            <?= $this->url->link(t('Advanced analysis'), 'analysis', 'tasks', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('Analysis', 'summary') ?>>
            <?= $this->url->link(t('Summary'), 'analysis', 'summary', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
        </li>
