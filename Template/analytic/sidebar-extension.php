        <li <?= $this->app->checkMenuSelection('AnalysisController', 'tasks') ?>>
            <?= $this->url->link(t('Advanced analysis'), 'AnalysisController', 'tasks', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalysisController', 'summary') ?>>
            <?= $this->url->link(t('Summary'), 'AnalysisController', 'summary', array('plugin' => 'Analysis', 'project_id' => $project['id'])) ?>
        </li>
