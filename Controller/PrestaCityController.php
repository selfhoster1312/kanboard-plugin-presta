<?php
namespace Kanboard\Plugin\Presta\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;

use Kanboard\Plugin\Presta;

/**
 * Class PrestaClientController
 *
 * @package Kanboard\Plugin\Presta\Controller
 */
class PrestaCityController extends BaseController
{
    public function select(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions

        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->selectCity($task_id);
        } else {
            $this->response->html($this->template->render('presta:city/select', array(
                'city_options' => $this->prestaCityModel->options(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    public function selectCity($task_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["city_name"])) {
            $errors["city_name"] = [ "No city selected!" ];
        } else {
            $city_name = $postValues["city_name"];
            if (!$this->prestaCityModel->has($city_name)) {
                $errors["city_name"] = [ "City does not exist: $city_name" ];
            }
        }

        // Return early if there were errors
        if (!empty($errors)) {
            return $this->response->html($this->template->render('presta:city/select', array(
                'city_options' => $this->prestaCityModel->list(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => array(),
            )));
        }

        // Save name in presta JSON
        $this->prestaTaskModel->setCity_unchecked($task_id, $city_name);
        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }

    // Create a new client from form
    public function createCity($task_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["city_name"]) || empty($postValues["city_name"])) {
            $errors["city_name"] = [ "No name provided!" ];
        }
        
        // Return early if there were errors
        if (!empty($errors)) {
            $this->response->html($this->template->render('presta:city/create', array(
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }

        $city_name = $postValues["city_name"];

        $this->prestaCityModel->create($city_name);
        
        if ($task_id != null) {
            // In a task edition context, redirect to the task
            $this->prestaTaskModel->setCity_unchecked($task_id, $city_name);
            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
        } else {
            // No task edition context, redirect to the clients list
            $this->response->redirect($this->helper->url->to('PrestaCityController', 'list', [ 'plugin' => 'Presta' ]), true);
        }
    }

    public function create(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions
        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->createCity($task_id);
        } else {
            $this->response->html($this->template->render('presta:city/create', array(
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    public function list(array $values = array(), array $errors = array())
    {
        $this->response->html($this->helper->layout->sublayout('presta:layout/layout', 'presta:layout/sidebar', 'presta:city/list', array(
            'title' => 'Presta',
            'cities' => $this->prestaCityModel->list(),
            'errors' => $errors,
            'values' => $values,
        )));
    }

    // Ask for confirmation before removing
    public function confirm()
    {
        $city_name = $this->request->getStringParam('city_name');
        if (!$this->prestaCityModel->has($city_name)) {
            error_log("No such city: $city_name");
            $this->response->redirect($this->helper->url->to('PrestaCityController', 'list', [ 'plugin' => 'Presta' ]));
        }
        
        $this->response->html($this->template->render('presta:city/remove', array(
            'city_name' => $city_name,
        )));
    }

   
    public function remove()
    {
        $this->checkCSRFParam();
        $city_name = $this->request->getStringParam('city_name');
        $this->prestaCityModel->remove($city_name);
        $this->flash->success('City removed successfully.');
        $this->response->redirect($this->helper->url->to('PrestaCityController', 'list', [ 'plugin' => 'Presta' ]));
    }
}

