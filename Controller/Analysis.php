<?php

namespace Kanboard\Plugin\Analysis\Controller;

use Kanboard\Controller\Base;
use Kanboard\Model\Task as TaskModel;

/**
 * Project Analytic controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Analysis extends Base
{
    /**
     * Show average Lead and Cycle time
     *
     * @access public
     */
    public function leadAndCycleTime()
    {
        $project = $this->getProject();
        list($from, $to) = $this->getDates();

        $this->response->html($this->helper->layout->analytic('analytic/lead_cycle_time', array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'project' => $project,
            'average' => $this->averageLeadCycleTimeAnalytic->build($project['id']),
            'metrics' => $this->projectDailyStats->getRawMetrics($project['id'], $from, $to),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t('Lead and Cycle time for "%s"', $project['name']),
        )));
    }

    /**
     * Show comparison between actual and estimated hours chart
     *
     * @access public
     */
    public function compareHours()
    {
        $project = $this->getProject();
        $params = $this->getProjectFilters('analytic', 'compareHours');
        $query = $this->taskFilter->create()->filterByProject($params['project']['id'])->getQuery();

        $paginator = $this->paginator
            ->setUrl('analytic', 'compareHours', array('project_id' => $project['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->analytic('analytic/compare_hours', array(
            'project' => $project,
            'paginator' => $paginator,
            'metrics' => $this->estimatedTimeComparisonAnalytic->build($project['id']),
            'title' => t('Compare hours for "%s"', $project['name']),
        )));
    }

    /**
     * Show average time spent by column
     *
     * @access public
     */
    public function averageTimeByColumn()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->analytic('analytic/avg_time_columns', array(
            'project' => $project,
            'metrics' => $this->averageTimeSpentColumnAnalytic->build($project['id']),
            'title' => t('Average time spent into each column for "%s"', $project['name']),
        )));
    }

    public function index(){
        $project = $this->getProject();
        $this->response->html($this->helper->layout->analytic('analysis:analytic/index', array(
            'title' => t('Advanced analysis for "%s"', $project['name']),
            'project' => $project,
        )));
    }

    /**
     * Show tasks distribution graph
     *
     * @access public
     */
    public function tasks()
    {
        $project = $this->getProject();
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');
#        $metrics = $this->taskDistributionAnalytic->build($project['id'], $swimlane_id);
        $metrics = $this->build($project['id'], $swimlane_id);
        $closedcount = 0;
        $closedtasks = $this->taskFinder->getAll($project['id'], 0);

        if ($swimlane_id > 0){
            foreach ($closedtasks as $closedtask) {
                $closedcount += ($closedtask['swimlane_id'] == $swimlane_id) ? 1 : 0;
            }
        }
        else{
                 $closedcount = count($closedtasks);
        }    
        $closedtotal = 0;
        foreach ($metrics as $metric) {
            $closedtotal += $metric['nb_tasks'];
        }

            $metrics[] = array(
                'column_title' => t('Closed'),
                'nb_tasks' => $closedcount . '/' . $closedtotal,
                'percentage' => round(($closedcount * 100) / $closedtotal, 2)
            );


        $this->response->html($this->helper->layout->analytic('analysis:analytic/tasks', array(
            'project' => $project,
            'metrics' => $metrics,
            'title' => t('Task repartition for "%s"', $project['name']),
            'swimlanes' => $this->swimlane->getAll($project['id']),
            'swimlaneActive' => $this->swimlane->getNameById($swimlane_id),
        )));
    }


    public function build($project_id, $swimlane_id = 0)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->board->getColumns($project_id);

        foreach ($columns as $column) {
            if ($swimlane_id === 0) {
                $nb_tasks = $this->taskFinder->countByColumnId($project_id, $column['id']);
            }
            else
            {
                $nb_tasks = $this->taskFinder->countByColumnAndSwimlaneId($project_id, $column['id'], $swimlane_id);
            }

            $total += $nb_tasks;

            $metrics[] = array(
                'column_title' => $column['title'],
                'nb_tasks' => $nb_tasks,
            );
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }

        return $metrics;
    }




    /**
     * Show users repartition
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->analytic('analytic/users', array(
            'project' => $project,
            'metrics' => $this->userDistributionAnalytic->build($project['id']),
            'title' => t('User repartition for "%s"', $project['name']),
        )));
    }

    /**
     * Show cumulative flow diagram
     *
     * @access public
     */
    public function cfd()
    {
        $this->commonAggregateMetrics('analytic/cfd', 'total', 'Cumulative flow diagram for "%s"');
    }

    /**
     * Show burndown chart
     *
     * @access public
     */
    public function burndown()
    {
        $this->commonAggregateMetrics('analytic/burndown', 'score', 'Burndown chart for "%s"');
    }

    /**
     * Common method for CFD and Burdown chart
     *
     * @access private
     * @param string $template
     * @param string $column
     * @param string $title
     */
    private function commonAggregateMetrics($template, $column, $title)
    {
        $project = $this->getProject();
        list($from, $to) = $this->getDates();

        $display_graph = $this->projectDailyColumnStats->countDays($project['id'], $from, $to) >= 2;

        $this->response->html($this->helper->layout->analytic($template, array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'display_graph' => $display_graph,
            'metrics' => $display_graph ? $this->projectDailyColumnStats->getAggregatedMetrics($project['id'], $from, $to, $column) : array(),
            'project' => $project,
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t($title, $project['name']),
        )));
    }

    private function getDates()
    {
        $values = $this->request->getValues();

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $values['from'];
            $to = $values['to'];
        }

        return array($from, $to);
    }
}
