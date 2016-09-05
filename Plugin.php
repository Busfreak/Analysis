<?php

namespace Kanboard\Plugin\Analysis;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        $this->helper->register('AnalysisHelper', '\Kanboard\Plugin\Analysis\Helper\AnalysisHelper');
		$this->template->hook->attach('template:analytic:sidebar', 'Analysis:analytic/sidebar-extension');
        $this->hook->on('template:layout:head', array('template' => 'Analysis:head'));
    }

    public function onStartup()
    {
        // Translation
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
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
        return 'Analysis';
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
        return '0.0.3';
    }

	    public function getPluginHomepage()
    {
        return 'https://github.com/Busfreak/Analysis';
    }
}