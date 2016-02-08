<?php

namespace Kanboard\Plugin\Analysis\Model;

use Kanboard\Model\Base;

/**
 * Analysis
 *
 * @package  model
 * @author   Martin Middeke
 */
class Analysis extends Base
{

    public function getSwimlanesList($project_id)
    {
        $swimlanes = $this->swimlane->getSwimlanes($project_id);
        if (empty($swimlanes))
        {
            return '';
        }
        else
        {
#        <li #$this->app->checkMenuSelection('Analysis', 'tasks')

            $return = '<ul>';
            if($this->helper->app->getRouterAction() === 'Analysis');
            $return .= $this->helper->url->link(t('Swimlane') . ': ' . t('all'), 'Analysis', 'tasks', array('plugin' => 'Analysis', 'project_id' => $project_id));
            foreach ($swimlanes as $swimlane)
            {
                $return .= '<li>';
                $return .= $this->helper->url->link(t('Swimlane') . ': ' . $swimlane['name'], 'Analysis', 'tasks', array('plugin' => 'Analysis','project_id' => $project_id, 'swimlane_id' => $swimlane['id']));
                $return .= '</li>';
            }
#        $return .= '</ul>';
        }

        
        
        
        return $return;

    }

}