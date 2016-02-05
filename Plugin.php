<?php

namespace Kanboard\Plugin\Analysis;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {

        $this->template->setTemplateOverride('analytic/sidebar', 'Analysis:analytic/sidebar');
#        $this->template->setTemplateOverride('analytic/tasks', 'Analysis:analytic/tasks');
        $this->template->hook->attach('template:analytic:sidebar', 'Analysis:analytic/sidebar-extension');
        $this->on('app.bootstrap', function($container) {
            Translator::load($container['config']->getCurrentLanguage(), __DIR__.'/Locale');
        });
    }

    public function getClasses()
    {
        return array(
            'Plugin\Analysis\Model' => array(
                'Analysis',
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