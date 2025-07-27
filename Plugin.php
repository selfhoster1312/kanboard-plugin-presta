<?php
namespace Kanboard\Plugin\Presta;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

define('PRESTA_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'presta');

class Plugin extends Base
{
    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }
    
    public function initialize()
    {
        // Remove stuff from task sidebar
        $this->template->setTemplateOverride('task/dropdown', 'presta:task/dropdown');
        $this->template->setTemplateOverride('task/sidebar', 'presta:task/sidebar');

        // Cleanup task detailed view. Don't display associated headings unless some content is present.
        $this->template->setTemplateOverride('task_external_link/show', 'presta:task_external_link/show');
        $this->template->setTemplateOverride('task_internal_link/show', 'presta:task_internal_link/show');
        $this->template->setTemplateOverride('task/description', 'presta:task/description');
        $this->template->setTemplateOverride('subtask/show', 'presta:subtask/show');
        $this->template->setTemplateOverride('task_file/show', 'presta:task_file/show');
        $this->template->setTemplateOverride('task_comments/show', 'presta:task_comments/show');

        // Display "add comment" widget unconditionally
        // Our custom task_comments/create does not have a visibility param
        $this->template->hook->attachCallable(
            'template:task:show:bottom',
            'presta:task_comments/create',
            function($task, $project, $user) {
                return [
                    'values'   => array(
                        'user_id' => $user->getId(),
                        'task_id' => $task['id'],
                        'project_id' => $task['project_id'],
                    ),
                    'errors'   => array(),
                    'task'     => $task,
                ];
            }
        );

        // Improved detailed task view
        $this->template->setTemplateOverride('task/details', 'presta:task/details');

        // Add link to Presta plugin in user drop down menu
        // TODO: make Presta overview page instead of going to clients
        $this->template->hook->attach('template:header:dropdown', 'presta:header/dropdown');

        // Add route for task-based modal client management
        $this->route->addRoute('/presta/client/:task_id', 'PrestaClientController', 'select', 'Presta');
        $this->route->addRoute('/presta/client/:task_id/create', 'PrestaClientController', 'create', 'Presta');

        // Add route for complete client management
        $this->route->addRoute('/presta/client', 'PrestaClientController', 'list', 'Presta');
        $this->route->addRoute('/presta/client/edit', 'PrestaClientController', 'edit', 'Presta');
        $this->route->addRoute('/presta/client/delete', 'PrestaClientController', 'delete', 'Presta');
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.46';
    }
}
