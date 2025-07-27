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

        $clients = $this->getClients();
        
        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->selectClient($task_id, $clients);
        } else {
            $this->response->html($this->template->render('presta:client/select', array(
                'clients' => $clients,
                'task_id' => $task_id,
                'errors' => $errors,
            )));
        }
    }

    public function selectClient($task_id, $clients) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["client_id"])) {
            $errors["client_id"] = [ "No client selected!" ];
        } else {
            $client_id = $postValues["client_id"];
            if (!$clients->has($client_id)) {
                $errors["client_id"] = [ "Client does not exist: $client_id" ];
            }
        }

        // Return early if there were errors
        if (!empty($errors)) {
            $this->response->html($this->template->render('presta:client/select', array(
                'clients' => $clients,
                'task_id' => $task_id,
                'errors' => $errors,
            )));
        }

        $task_model = new Presta\Model\PrestaTaskModel($task_id);
        $task_model->setClient_unchecked($client_id);
        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }

    // Update an existing client
    public function updateClient($client_id, $clients) {
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
            $client = $clients->get($client_id);
            $this->response->html($this->template->render('presta:client/edit', array(
                'client_id' => $client_id,
                'client_name' => $client["name"],
                'client_address' => $client["address"],
                'errors' => $errors,
            )));
        }

        $client_name = $postValues["client_name"];
        $client_address = $postValues["client_address"];

        $clients->update($client_id, $client_name, $client_address);
        $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]), true);
    }

    // Create a new client from form
    public function createClient($task_id, $clients) {
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
                'clients' => $clients,
                'task_id' => $task_id,
                'errors' => $errors,
            )));
        }

        $client_name = $postValues["client_name"];
        $client_address = $postValues["client_address"];

        $client_id = $clients->create($client_name, $client_address);

        if ($task_id != null) {
            // In a task edition context, redirect to the task
            $task_model = new Presta\Model\PrestaTaskModel($task_id);
            $task_model->setClient_unchecked($client_id);
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
        $clients = $this->getClients();;
        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->createClient($task_id, $clients);
        } else {
            $this->response->html($this->template->render('presta:client/create', array(
                'clients' => $clients,
                'task_id' => $task_id,
                'errors' => $errors,
            )));
        }
    }

    public function list(array $values = array(), array $errors = array())
    {
        $clients = $this->getClients();

        $this->response->html($this->helper->layout->sublayout('presta:layout/layout', 'presta:layout/sidebar', 'presta:client/list', array(
            'title' => 'Presta',
            'clients' => $clients->list(),
            'errors' => $errors,
        )));
    }

    public function edit(array $values = array(), array $errors = array())
    {
        $client_id = $this->request->getIntegerParam('client_id');
        $clients = $this->getClients();;
        $client = $clients->get($client_id);

        if ($client == null) {
            $this->response->html("No such client ID $client_id");
        }

        if ($this->request->isPost()) {
            $this->updateClient($client_id, $clients);
        } else {
            $this->response->html($this->template->render('presta:client/edit', array(
                'client_id' => $client_id,
                'client_name' => $client["name"],
                'client_address' => $client["address"],
                'errors' => $errors,
            )));
        }
    }

    // Ask for confirmation before removing
    public function confirm()
    {
        $client_id = $this->request->getStringParam('client_id');
        $clients = $this->getClients();

        $client = $clients->get($client_id);
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
        $clients = $this->getClients();

        $clients->remove($client_id);
        $this->flash->success('Client removed successfully.');
        $this->response->redirect($this->helper->url->to('PrestaClientController', 'list', [ 'plugin' => 'Presta' ]));
    }

    public function getClients() {
        return new Presta\Model\PrestaClientModel();
    }
}

