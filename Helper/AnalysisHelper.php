<?php
namespace Kanboard\Plugin\Analysis\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\CommentModel;

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
            'categories_list' => $this->categoryModel->getList($project['id'], false),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], false),
            'custom_filters_list' => $this->customFilterModel->getAll($project['id'], $this->userSession->getId()),
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
            ->table(SubtaskModel::TABLE)
            ->columns(
                SubtaskModel::TABLE.'.id',
                SubtaskModel::TABLE.'.title',
                SubtaskModel::TABLE.'.status',
                SubtaskModel::TABLE.'.user_id',
                SubtaskModel::TABLE.'.time_estimated',
                SubtaskModel::TABLE.'.time_spent',
                SubtaskModel::TABLE.'.position',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(SubtaskModel::TABLE.'.task_id', $task_id)
            ->findAll();
    }
    public function getComments($task_id, $sorting = 'ASC')
    {
	      return $this->db
            ->table(CommentModel::TABLE)
            ->columns(
                CommentModel::TABLE.'.id',
                CommentModel::TABLE.'.date_creation',
                CommentModel::TABLE.'.task_id',
                CommentModel::TABLE.'.user_id',
                CommentModel::TABLE.'.comment',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
				UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->orderBy(CommentModel::TABLE.'.date_creation', $sorting)
            ->eq(CommentModel::TABLE.'.task_id', $task_id)
            ->findAll();
    }

    public function getInternalTaskLinks($task_id)
    {
        return $this->taskLinkModel->getAllGroupedByLabel($task_id);
    }

    public function getExternalTaskLinks($task_id)
    {
        return $this->taskExternalLinkModel->getAll($task_id);
    }
}
