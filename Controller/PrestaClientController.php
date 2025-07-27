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
class PrestaClientController extends BaseController
{
    public function select(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions

        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->selectClient($task_id);
        } else {
            $this->response->html($this->template->render('presta:client/select', array(
                'client_options' => $this->prestaClientModel->options(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    public function selectClient($task_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["client_id"])) {
            $errors["client_id"] = [ "No client selected!" ];
        } else {
            $client_id = $postValues["client_id"];
            if (!$this->prestaClientModel->has($client_id)) {
                $errors["client_id"] = [ "Client does not exist: $client_id" ];
            }
        }

        // Return early if there were errors
        if (!empty($errors)) {
            $this->response->html($this->template->render('presta:client/select', array(
                'client_options' => $this->prestaClientModel->options(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }

        $this->prestaTaskModel->setClientId_unchecked($task_id, $client_id);
        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }

    // Update an existing client
    public function updateClient($client_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["client_name"]) || empty($postValues["client_name"])) {
            $errors["client_name"] = [ "No name provided!" ];
        }
        if (!isset($postValues["client_address"]) || empty($postValues["client_address"])) {
            $errors["client_address"] = [ "No address provided!" ];
        }
        
        // Return early if there were errors
        if (!empty($errors)) {
            $client = $this->prestaClientModel->get($client_id);
            $this->response->html($this->template->render('presta:client/edit', array(
                'client_id' => $client_id,
                'client_name' => $client["name"],
                'client_address' => $client["address"],
                'errors' => $errors,
                'values' => $values,
            )));
        }

        $client_name = $postValues["client_name"];
        $client_address = $postValues["client_address"];

        $this->prestaClientModel->update($client_id, $client_name, $client_address);
        $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]), true);
    }

    // Create a new client from form
    public function createClient($task_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["client_name"]) || empty($postValues["client_name"])) {
            $errors["client_name"] = [ "No name provided!" ];
        }
        if (!isset($postValues["client_address"]) || empty($postValues["claddress_address"])) {
            $errors["client_address"] = [ "No address provided!" ];
        }
        
        // Return early if there were errors
        if (!empty($errors)) {
            $this->response->html($this->template->render('presta:client/create', array(
                'task_id' => $task_id,
                'errors' => $errors,
            )));
        }

        $client_name = $postValues["client_name"];
        $client_address = $postValues["client_address"];

        $client_id = $this->prestaClientModel->create($client_name, $client_address);

        if ($task_id != null) {
            // In a task edition context, redirect to the task
            $this->prestaTaskModel->setClientId_unchecked($task_id, $client_id);
            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
        } else {
            // No task edition context, redirect to the clients list
            $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]), true);
        }
    }

    public function create(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions
        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->createClient($task_id);
        } else {
            $this->response->html($this->template->render('presta:client/create', array(
                'clients' => $this->prestaClientModel->list(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    public function list(array $values = array(), array $errors = array())
    {
        $this->response->html($this->helper->layout->sublayout('presta:layout/layout', 'presta:layout/sidebar', 'presta:client/list', array(
            'title' => 'Presta',
            'clients' => $this->prestaClientModel->list(),
            'errors' => $errors,
            'values' => $values,
        )));
    }

    public function edit(array $values = array(), array $errors = array())
    {
        $client_id = $this->request->getIntegerParam('client_id');
        $client = $this->prestaClientModel->get($client_id);

        if ($client == null) {
            $this->response->html("No such client ID $client_id");
        }

        if ($this->request->isPost()) {
            $this->updateClient($client_id);
        } else {
            $this->response->html($this->template->render('presta:client/edit', array(
                'client_id' => $client_id,
                'client_name' => $client["name"],
                'client_address' => $client["address"],
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    // Ask for confirmation before removing
    public function confirm()
    {
        $client_id = $this->request->getStringParam('client_id');
        $client = $this->prestaClientModel->get($client_id);
        if ($client == null) {
            $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]));
        }
        
        $this->response->html($this->template->render('presta:client/remove', array(
            'client_id' => $client_id,
            'client' => $client,
        )));
    }

   
    public function remove()
    {
        $this->checkCSRFParam();
        $client_id = $this->request->getStringParam('client_id');
        $this->prestaClientModel->remove($client_id);
        $this->flash->success('Client removed successfully.');
        $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]));
    }
}

