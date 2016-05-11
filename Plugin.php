<?php

namespace Kanboard\Plugin\Analysis;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {

#        $this->template->setTemplateOverride('analytic/tasks', 'Analysis:analytic/tasks');
        $this->template->hook->attach('template:analytic:sidebar', 'Analysis:analytic/sidebar-extension');
        $this->hook->on('template:layout:css', 'plugins/analysis/css/style.css');
    }

    public function onStartup()
    {
        // Translation
        Translator::load($this->language->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getClasses()
    {
        return array(
            'Plugin\Analysis\Model' => array(
                'Analysis',
                'Filter',
            )
        );
    }

    public function getPluginName()
    {
        return 'QMAnalysis';
    }

    public function getPluginDescription()
    {
        return t('Advanced analysis for QM');
    }

    public function getPluginAuthor()
    {
        return 'Martin Middeke';
    }

    public function getPluginVersion()
    {
        return '0.0.1';
    }

	    public function getPluginHomepage()
    {
        return 'https://github.com/Busfreak/QMAnalysis';
    }
}