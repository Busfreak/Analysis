<?php

namespace Kanboard\Plugin\Analysis\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Model\TaskModel;
use Kanboard\Model\User;
use Kanboard\Filter\TaskProjectFilter;

/**
 * Project Analysis controller
 *
 * @package  controller
 * @author   Martin Middeke
 */
class AnalysisController extends BaseController
{

    /**
     * Show summary of all tasks
     *
     * @access public
     */
    public function summary()
    {
		$project = $this->getProject();
        $search = urldecode($this->request->getStringParam('search'));
#        $search = $this->userSession->getFilters($project['id']);

        $paginator = $this->paginator
            ->setUrl('analysis', 'summary', array('project_id' => $project['id'], 'plugin' => 'Analysis', 'search' => $search))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.column_id')
            ->setDirection('DESC')
            ->setQuery($this->taskLexer
                ->build($search)
#                ->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery()
            )
#            ->setQuery($this->filter->getExtendedQuery($project['id']))
            ->calculate();


$debug = 1;
        $this->response->html($this->analyticLayout('analysis:project/summary', array(
            'project' => $project,
            'paginator' => $paginator,
            'link_label_list' => $this->linkModel->getList(0, false),
            'title' => $project['name'].' &gt; ' . t('Summary'),
			'debug' => $debug,
        )));

    }

    /**
     * Common layout for analytic views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function analyticLayout($template, array $params)
    {
        return $this->helper->layout->subLayout('analysis:analytic/layout', 'analytic/sidebar', $template, $params);
    }

    private function getTasks($project_id, $column_id)
    {
        return $this->db
            ->table('tasks')
            ->columns('date_completed', 'date_creation', 'date_started')
            ->eq('project_id', $project_id)
            ->desc('id')
            ->limit(1)
            ->findAll();
    }


    /**
     * Common method to get filters
     *
     * based on function getProjectFilters
     *
     * @access protected
     * @param  string $controller
     * @param  string $action
     * @return array
     */
    protected function getFilters($controller, $action, $plugin)
    {
        $project = $this->getProject();
        $search = $this->request->getStringParam('search', $this->userSession->getFilters($project['id']));

        $filters = array(
            'controller' => $controller,
            'action' => $action,
            'project_id' => $project['id'],
            'search' => urldecode($search),
            'plugin' => $plugin,
        );

        $this->userSession->setFilters($project['id'], $filters['search']);

        return array(
            'project' => $project,
            'filters' => $filters,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
        );
    }





    /**
     * Show a task
     *
     * @access public
     */
    public function show()
    {
        $task = $this->getTasks();
echo'<pre>';
var_dump($task);
echo'</pre>';
        $subtasks = $this->subtask->getAll($task['id']);
        $values = array(
            'id' => $task['id'],
            'date_started' => $task['date_started'],
            'time_estimated' => $task['time_estimated'] ?: '',
            'time_spent' => $task['time_spent'] ?: '',
        );

        $values = $this->dateParser->format($values, array('date_started'), $this->config->get('application_datetime_format', 'd-m-Y H:i'));

        $this->response->html($this->helper->layout->task('task/show', array(
            'project' => $this->project->getById($task['project_id']),
            'files' => $this->file->getAllDocuments($task['id']),
            'images' => $this->file->getAllImages($task['id']),
            'comments' => $this->comment->getAll($task['id'], $this->userSession->getCommentSorting()),
            'subtasks' => $subtasks,
            'links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'task' => $task,
            'values' => $values,
            'link_label_list' => $this->link->getList(0, false),
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'users_list' => $this->projectUserRole->getAssignableUsersList($task['project_id'], true, false, false),
            'title' => $task['project_name'].' &gt; '.$task['title'],
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
        )));
    }

    /**
     * Common method to get a task for task views
     *
     * @access protected
     * @return array
     */
    protected function getTask($project_id)
    {
        
        $task = $this->taskFinderModel->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            $this->notfound();
        }

        if ($project_id !== 0 && $project_id != $task['project_id']) {
            $this->forbidden();
        }

        return $task;
    }




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
        $query = $this->filter->create()->filterByProject($params['project']['id'])->getQuery();

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
        $closedtasks = $this->taskFinderModel->getAll($project['id'], 0);

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
            'swimlanes' => $this->swimlaneModel->getAll($project['id']),
            'swimlaneActive' => $this->swimlaneModel->getNameById($swimlane_id),
        )));
    }


    public function build($project_id, $swimlane_id = 0)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->columnModel->getAll($project_id);

        foreach ($columns as $column) {
            if ($swimlane_id === 0) {
                $nb_tasks = $this->taskFinderModel->countByColumnId($project_id, $column['id']);
            }
            else
            {
                $nb_tasks = $this->taskFinderModel->countByColumnAndSwimlaneId($project_id, $column['id'], $swimlane_id);
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
}
