<?php
namespace Kanboard\Plugin\Presta\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;

use Kanboard\Plugin\Presta;

/**
 * Class PrestaOfferController
 *
 * @package Kanboard\Plugin\Presta\Controller
 */
class PrestaOfferController extends BaseController
{

    // Create a new offer from form
    public function create(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions
        $task_id = $this->request->getIntegerParam("task_id");


        if ($this->request->isPost()) {
            $postValues = $this->request->getValues();
            if (!isset($postValues["short_name"]) || empty($postValues["short_name"])) {
                $errors["short_name"] = [ "No name provided!" ];
            }

            if (!isset($postValues["description"]) || empty($postValues["description"])) {
                $errors["description"] = [ "No description provided!" ];
            }

            if (!isset($postValues["price"]) || !is_numeric($postValues["price"])) {
                $errors["price"] = [ "Please provide a number for the price" ];
            }
        
            // Return early if there were errors
            if (!empty($errors)) {
                return $this->response->html($this->template->render('presta:offer/create', array(
                    // 'task_id' => $task_id,
                    'errors' => $errors,
                    'values' => $values,
                )));
            }

            $short_name = $postValues["short_name"];
            $description = $postValues["description"];
            $price = intval($postValues["price"]);

            $this->prestaOfferModel->create($short_name, $description, $price);
        
            if ($task_id != null) {
                // In a task edition context, redirect to the task
                $this->prestaTaskModel->setOffer_unchecked($task_id, $offer_id);
                $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
            } else {
                // No task edition context, redirect to the clients list
                $this->response->redirect($this->helper->url->to('PrestaOfferController', 'list', [ 'plugin' => 'Presta' ]), true);
            }
        } else {
            $this->response->html($this->template->render('presta:offer/create', array(
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }

    public function list(array $values = array(), array $errors = array())
    {
        $this->response->html($this->helper->layout->sublayout('presta:layout/layout', 'presta:layout/sidebar', 'presta:offer/list', array(
            'title' => 'Presta',
            'offers' => $this->prestaOfferModel->list(),
            'errors' => $errors,
            'values' => $values,
        )));
    }

    // Ask for confirmation before removing
    public function confirm()
    {
        $offer_id = $this->request->getStringParam('offer_id');
        if (!$this->prestaOfferModel->has($offer_id)) {
            error_log("No such offer: $offer_id");
            return $this->response->redirect($this->helper->url->to('PrestaOfferController', 'list', [ 'plugin' => 'Presta' ]));
        }
        
        return $this->response->html($this->template->render('presta:offer/remove', array(
            'offer_id' => $offer_id,
            'offer' => $this->prestaOfferModel->get($offer_id),
        )));
    }

   
    public function remove()
    {
        $this->checkCSRFParam();
        $offer_id = $this->request->getStringParam('offer_id');
        $this->prestaOfferModel->remove($offer_id);
        $this->flash->success('Offer removed successfully.');
        $this->response->redirect($this->helper->url->to('PrestaOfferController', 'list', [ 'plugin' => 'Presta' ]));
    }

    public function edit($values = array(), $errors = array()) {
        $offer_id = $this->request->getIntegerParam('offer_id');
        $offer = $this->prestaOfferModel->get($offer_id);

        if ($offer == null) {
            return $this->response->html("No such offer ID $offer_id");
        }

        if ($this->request->isPost()) {
            $this->updateOffer($offer_id);
        } else {
            $this->response->html($this->template->render('presta:offer/edit', array(
                'offer_id' => $offer_id,
                'offer' => $offer,
                'errors' => $errors,
                'values' => $offer,
            )));
        }
    }

    // Update an existing offer
    public function updateOffer($offer_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["short_name"]) || empty($postValues["short_name"])) {
            $errors["short_name"] = [ "No name provided!" ];
        }
        if (!isset($postValues["description"]) || empty($postValues["description"])) {
            $errors["description"] = [ "No description provided!" ];
        }
        if (!isset($postValues["price"]) || !is_numeric($postValues["price"])) {
            $errors["price"] = [ "Price must be a number!" ];
        }
        
        // Return early if there were errors
        if (!empty($errors)) {
            $offer = $this->prestaOfferModel->get($offer_id);
            return $this->response->html($this->template->render('presta:offer/edit', array(
                'offer_id' => $offer_id,
                'offer' => $offer,
                'errors' => $errors,
                'values' => $postValues,
            )));
        }

        $short_name = $postValues["short_name"];
        $description = $postValues["description"];
        $price = $postValues["price"];

        $this->prestaOfferModel->update($offer_id, $short_name, $description, $price);
        $this->response->redirect($this->helper->url->to('PrestaOfferController', 'list', [ 'plugin' => 'Presta' ]), true);
    }


    public function select(array $values = array(), array $errors = array())
    {
        // TODO: CSRF validation
        // TODO: permissions

        $task_id = $this->request->getIntegerParam("task_id");

        if ($this->request->isPost()) {
            $this->selectOffer($task_id);
        } else {
            return $this->response->html($this->template->render('presta:offer/select', array(
                'offer_options' => $this->prestaOfferModel->options(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $values,
            )));
        }
    }
    
    // One can select several offers
    public function selectOffer($task_id) {
        $errors = [];

        $postValues = $this->request->getValues();
        if (!isset($postValues["offer_id"])) {
            $errors["offer_id"] = [ "No offer selected!" ];
        } else {
            $offer_id = $postValues["offer_id"];
            if (!$this->prestaOfferModel->has($offer_id)) {
                $errors["offer_id"] = [ "Offer does not exist: $offer_id" ];
            }
        }

        if(!isset($postValues["date"]) || empty($postValues["date"])) {
            $errors['date'] = [ 'Please define a date' ];
        }
        if(!isset($postValues["start"]) || empty($postValues["start"])) {
            $errors['start'] = [ 'Please define a starting time' ];
        }
        if(!isset($postValues["end"]) || empty($postValues["end"])) {
            $errors['end'] = [ 'Please define an ending time' ];
        }

        // Return early if there were errors
        if (!empty($errors)) {
            return $this->response->html($this->template->render('presta:offer/select', array(
                'offer_options' => $this->prestaOfferModel->options(),
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $postValues,
            )));
        }

        $date = $postValues['date'];
        $start = $postValues['start'];
        $end = $postValues['end'];

        $this->prestaTaskModel->addOffer_unchecked($task_id, $offer_id, $date, $start, $end);
        return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }

    // Ask for confirmation before removing
    public function task_confirm(array $values = array(), array $errors = array())
    {
        $task_id = $this->getTask()['id'];
        $offer_uuid = $this->request->getStringParam('offer_uuid');
        if (!$this->prestaTaskModel->hasOfferUuid($task_id, $offer_uuid)) {
            error_log("Task has no such offer: $offer_uuid");
            return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
        }
        
        return $this->response->html($this->template->render('presta:offer/task_remove', array(
            'task_id' => $task_id,
            'offer_uuid' => $offer_uuid,
        )));
    }

   
    public function task_delete(array $values = array(), array $errors = array())
    {
        $this->checkCSRFParam();
        $task_id = $this->getTask()['id'];
        $offer_uuid = $this->request->getStringParam('offer_uuid');
        $this->prestaTaskModel->removeOfferUuid($task_id, $offer_uuid);
        $this->flash->success('Offer removed successfully.');
        return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }

    public function task_edit(array $values = array(), array $errors = array())
    {
        $offer_uuid = $this->request->getStringParam('offer_uuid');
        $task_id = $this->getTask()['id'];

        if ($this->request->isPost()) {
            $this->task_update($task_id, $offer_uuid);
        } else {
            $offer = $this->prestaTaskModel->getOfferByUuid($task_id, $offer_uuid);
            $this->response->html($this->template->render('presta:offer/task_edit', array(
                'task_id' => $task_id,
                'offer_id' => $offer['offer_id'],
                'offer_uuid' => $offer_uuid,
                'offer' => $offer,
                'errors' => $errors,
                'values' => $offer,
            )));
        }
    }

    public function task_update(array $values = array(), array $errors = array())
    {
        $offer_uuid = $this->request->getStringParam('offer_uuid');
        $task_id = $this->getTask()['id'];
        $postValues = $this->request->getValues();
        if (!isset($postValues["offer_id"])) {
            $errors["offer_id"] = [ "No offer selected!" ];
        } else {
            $offer_id = $postValues["offer_id"];
            if (!$this->prestaOfferModel->has($offer_id)) {
                $errors["offer_id"] = [ "Offer does not exist: $offer_id" ];
            }
        }

        if(!isset($postValues["date"]) || empty($postValues["date"])) {
            $errors['date'] = [ 'Please define a date' ];
        }
        if(!isset($postValues["start"]) || empty($postValues["start"])) {
            $errors['start'] = [ 'Please define a starting time' ];
        }
        if(!isset($postValues["end"]) || empty($postValues["end"])) {
            $errors['end'] = [ 'Please define an ending time' ];
        }

        // Return early if there were errors
        if (!empty($errors)) {
            return $this->response->html($this->template->render('presta:offer/task_edit', array(
                'offer_id' => $offer['offer_id'],
                'offer_uuid' => $offer_uuid,
                'offer' => $offer,
                'task_id' => $task_id,
                'errors' => $errors,
                'values' => $postValues,
            )));
        }

        $date = $postValues['date'];
        $start = $postValues['start'];
        $end = $postValues['end'];

        $this->prestaTaskModel->updateOfferByUuid(
            $task_id,
            $offer_uuid,
            [ "id" => $offer_id, "uuid" => $offer_uuid, "date" => $date, "start" => $start, "end" => $end ]
        );
        return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }
}

