<?php

namespace Kanboard\Plugin\Analysis\Controller;

use Kanboard\Controller\Base;
use Kanboard\Model\Task as TaskModel;
use Kanboard\Model\Subtask;
use Kanboard\Model\User;
use Kanboard\Model\Comment;

/**
 * Project Analytic controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Analysis extends Base
{

    /**
     * Show list view for projects
     *
     * @access public
     */
    public function summaryx()
    {
        $params = $this->getProjectFilters('listing', 'show');
        $query = $this->filter->search($params['filters']['search'])->filterByProject($params['project']['id'])->getQuery();

        $paginator = $this->paginator
            ->setUrl('listing', 'show', array('project_id' => $params['project']['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->app('listing/show', $params + array(
            'paginator' => $paginator,
        )));
    }



    /**
     * Show summary of all tasks
     *
     * @access public
     */
    public function summary()
    {
        $debug = array();
        $subtasks = array();
        $project = $this->getProject();
        $project_id = $project['id'];
        $columns = $this->board->getColumns($project['id']);
        $swimlanes = $this->swimlane->getSwimlanes($project_id);
        $task_ids = $this->taskFinder->getAll($project['id'], 1);
        foreach ($task_ids as $task_id):
            $tasks[] = $this->taskFinder->getDetails($task_id['id']);
        endforeach;
        $params = $this->getFilters('analysis', 'summary', 'Analysis');
        $search = urldecode($this->request->getStringParam('search'));
        $query = $this->filter->search($params['filters']['search'])->filterByProject($params['project']['id'])->getQuery();

        $paginator = $this->paginator
            ->setUrl('listing', 'show', array('project_id' => $params['project']['id']))
            ->setMax(100)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setQuery($query)
            ->calculate();

        foreach($paginator->getCollection() as $task):
            $subtasks[$task['id']] = $this->getSubTasks($task['id']);
            $comments[$task['id']] = $this->getComments($task['id']);
            $links[$task['id']] = $this->taskLink->getAllGroupedByLabel($task['id']);
        endforeach;



#        $debug = $this->filter->search($params['filters']['search'])->filterByProject($params['project']['id'])->getQuery();
#        $debug = $subtasks;

#        $e = $this->getAllTasks($project['id'], 1);
        
        
        
# $e =  $this->taskFinder->getProjectUserOverviewQuery($array, 1);

#$e = $this->container;
#$e = $this->container['analysis']->getSwimlanesList($project['id']);
#$e = $this->analysis->getSwimlanesList($project['id']);

# alle tasks aus einem board lesen:
#$e = $this->taskFinder->getAll($project['id']);

# alle Swimlanes lesen
#$e = $swimlanes = $this->swimlane->getSwimlanes($project_id);

# alle Spalten lesen
#$e = $columns = $this->board->getColumns($project['id']);

        $this->response->html($this->helper->layout->analytic('analysis:task/summary', array(
            'categories' => $this->category->getList($params['project']['id'], false),
            'project' => $project,
            'title' => $project['name'].' &gt; summary',
            'swimlanes' => $swimlanes,
            'columns' => $columns,
#            'tasks' => $tasks,
            'paginator' => $paginator,
            'subtasks' => $subtasks,
            'comments' => $comments,
            'link_label_list' => $this->link->getList(0, false),
            'links' => $links,
            'debug' => $debug,
        ) + $params));
    }
    
    private function getSubTasks($task_id)
    {
        return $this->db
            ->table(Subtask::TABLE)
            ->columns(
                Subtask::TABLE.'.id',
                Subtask::TABLE.'.title',
                Subtask::TABLE.'.status',
                Subtask::TABLE.'.user_id',
                Subtask::TABLE.'.time_estimated',
                Subtask::TABLE.'.time_spent',
                Subtask::TABLE.'.position',
                User::TABLE.'.username',
                User::TABLE.'.name'
            )
            ->join(User::TABLE, 'id', 'user_id')
            ->eq(Subtask::TABLE.'.task_id', $task_id)
            ->findAll();
    }

    private function getComments($task_id, $sorting = 'ASC')
    {
	      return $this->db
            ->table(Comment::TABLE)
            ->columns(
                Comment::TABLE.'.id',
                Comment::TABLE.'.date_creation',
                Comment::TABLE.'.task_id',
                Comment::TABLE.'.user_id',
                Comment::TABLE.'.comment',
                User::TABLE.'.username',
                User::TABLE.'.name',
                User::TABLE.'.email'
            )
            ->join(User::TABLE, 'id', 'user_id')
            ->orderBy(Comment::TABLE.'.date_creation', $sorting)
            ->eq(Comment::TABLE.'.task_id', $task_id)
            ->findAll();
}


public function tzu(){
	        return $this->db
                    ->table(SubtaskTimeTracking::TABLE)
                    ->columns(
                        SubtaskTimeTracking::TABLE.'.id',
                        SubtaskTimeTracking::TABLE.'.user_id',
                        SubtaskTimeTracking::TABLE.'.subtask_id',
                        SubtaskTimeTracking::TABLE.'.start',
                        SubtaskTimeTracking::TABLE.'.time_spent',
                        Subtask::TABLE.'.task_id',
                        Subtask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.title AS task_title',
                        Task::TABLE.'.project_id',
                        User::TABLE.'.username',
                        User::TABLE.'.name'
                    )
                    ->join(Subtask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', Subtask::TABLE)
                    ->join(User::TABLE, 'id', 'user_id')
                    ->eq(Task::TABLE.'.project_id', $project_id)
                    ->callback(array($this, 'applyUserRate'));
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
            'description' => $this->getProjectDescription($project),
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
        
        $task = $this->taskFinder->getDetails($this->request->getIntegerParam('task_id'));

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
