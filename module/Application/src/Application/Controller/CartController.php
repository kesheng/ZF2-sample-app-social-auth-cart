<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;


class CartController extends AbstractActionController
{
    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            if (!$controller->zfcUserAuthentication()->hasIdentity()) {
                return $controller->redirect()->toRoute('zfcuser');
            } else {
                //var_dump($controller->ZendCart()->cart());exit;
            }
        }, 100); // execute before executing action logic

        return $this;
    }

    public function addAction()
    {
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		if (isset($_POST['id'])) {
    			// check this product exist in cart or not
    			$token = $this->__checkProductById($_POST['id']);
    			if($token !== false) {
    				$product = array(
					    'token'   => $token,
					    'qty'     => $_POST['qty'],
					    'price'   => $_POST['price'],
					    'name'    => $_POST['qty'],
					    'options' => array('Size' => $_POST['size'], 'Color' => $_POST['color'])
					);
					$this->ZendCart()->update($product);
    			} else {
    				$product = array(
					    'id'      => $_POST['id'],
					    'qty'     => $_POST['qty'],
					    'price'   => $_POST['price'],
					    'name'    => $_POST['name'],
					    'options' => array('Size' => $_POST['size'], 'Color' => $_POST['color'])
					);
					$this->ZendCart()->insert($product);
    			}
    		}
    	}

    	return false;
    }

    public function cartAction()
    {
    	var_dump($this->ZendCart()->cart());

    	return false;
    }

    private function __checkProductById($id)
    {
    	foreach($this->ZendCart()->cart() as $key) {
    		if ($key['id'] == $id) {
    			return key($this->ZendCart()->cart());
    		}
    	}

    	return false;
    }
}