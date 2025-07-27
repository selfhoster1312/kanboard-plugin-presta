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
// In this controller we define extra values for tasks, which don't need special handling.
class PrestaTaskController extends BaseController
{
    public function distance_fee($values = array(), $errors = array()) {
        $task = $this->getTask();
        $task_id = $task['id'];

        if ($this->request->isPost()) {
            $postValues = $this->request->getValues();
            if (!isset($postValues['distance_fee'])) {
                $errors['distance_fee'] = [ "Fee not provided." ];
            } else if (!is_numeric($postValues['distance_fee'])) {
                $errors['distance_fee'] = [ "Please provide a number for the fee" ];
            }

            if (empty($errors)) {
                $distance_fee = $postValues['distance_fee'];
                $this->prestaTaskModel->setDistanceFee($task_id, $distance_fee);
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
            }

        }

        return $this->response->html($this->template->render('presta:task_presta/distance_fee', array(
            'task_id' => $task_id,
            'errors' => $errors,
            'values' => [ 'distance_fee' => $this->prestaTaskModel->load($task_id)['distance_fee'] ?? null ],
        )));
    }

    public function discount($values = array(), $errors = array()) {
        $task = $this->getTask();
        $task_id = $task['id'];

        if ($this->request->isPost()) {
            $postValues = $this->request->getValues();
            if (!isset($postValues['discount_amount'])) {
                $errors['discount_amount'] = [ "Amount not provided." ];
            } else if (!is_numeric($postValues['discount_amount'])) {
                $errors['discount_amount'] = [ "Please provide a number for the amount" ];
            }

            if (!isset($postValues['discount_reason']) || empty($postValues['discount_reason'])) {
                $errors['discount_reason'] = [ "Please provide a reason for the discount" ];
            }

            // If both are unset, we just remove it that's not an error
            if (count($errors) == 2) {
                $this->prestaTaskModel->removeDiscount($task_id);
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
            }

            if (empty($errors)) {
                $discount_amount = $postValues['discount_amount'];
                $discount_reason = $postValues['discount_reason'];
                $this->prestaTaskModel->setDiscount($task_id, $discount_amount, $discount_reason);
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
            }
        }

        $presta_task = $this->prestaTaskModel->load($task_id);

        return $this->response->html($this->template->render('presta:task_presta/discount', array(
            'task_id' => $task_id,
            'errors' => $errors,
            'values' => [
                "discount_amount" => $presta_task['discount']['amount'] ?? null,
                "discount_reason" => $presta_task['discount']['reason'] ?? null,
            ],
        )));
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

