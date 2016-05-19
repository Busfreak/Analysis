<?php
namespace Kanboard\Plugin\Analysis\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\Subtask;
use Kanboard\Model\User;
use Kanboard\Model\Comment;

/**
 * Project Header Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class AnalysisHelper extends Base
{
    /**
     * Get current search query
     *
     * @access public
     * @param  array  $project
     * @return string
     */
    public function getSearchQuery(array $project)
    {
        $search = $this->request->getStringParam('search', $this->userSession->getFilters($project['id']));
        $this->userSession->setFilters($project['id'], $search);
        return urldecode($search);
    }

    /**
     * Render project header (views switcher and search box)
     *
     * @access public
     * @param  array  $project
     * @param  string $controller
     * @param  string $action
     * @param  bool   $boardView
     * @return string
     */
    public function render(array $project, $controller, $action, $boardView = false)
    {
        $filters = array(
            'controller' => $controller,
            'action' => $action,
            'project_id' => $project['id'],
            'search' => $this->getSearchQuery($project),
        );

        return $this->template->render('Analysis:project_header/header', array(
            'project' => $project,
            'filters' => $filters,
            'categories_list' => $this->category->getList($project['id'], false),
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id'], false),
            'custom_filters_list' => $this->customFilter->getAll($project['id'], $this->userSession->getId()),
            'board_view' => $boardView,
        ));
    }

    /**
     * Get project description
     *
     * @access public
     * @param  array  &$project
     * @return string
     */
    public function getDescription(array &$project)
    {
        if ($project['owner_id'] > 0) {
            $description = t('Project owner: ').'**'.$this->helper->text->e($project['owner_name'] ?: $project['owner_username']).'**'.PHP_EOL.PHP_EOL;

            if (! empty($project['description'])) {
                $description .= '***'.PHP_EOL.PHP_EOL;
                $description .= $project['description'];
            }
        } else {
            $description = $project['description'];
        }

        return $description;
    }

    public function getSubTasks($task_id)
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
    public function getComments($task_id, $sorting = 'ASC')
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
                User::TABLE.'.email',
				User::TABLE.'.avatar_path'
            )
            ->join(User::TABLE, 'id', 'user_id')
            ->orderBy(Comment::TABLE.'.date_creation', $sorting)
            ->eq(Comment::TABLE.'.task_id', $task_id)
            ->findAll();
    }

    public function getInternalTaskLinks($task_id)
    {
        return $this->taskLink->getAllGroupedByLabel($task_id);
    }

    public function getExternalTaskLinks($task_id)
    {
        return $this->taskExternalLink->getAll($task_id);
    }
}
