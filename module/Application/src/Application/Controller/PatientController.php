<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class PatientController extends AbstractActionController
{
    
    public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if (!$this->session->role)
        {
            $this->redirect()->toRoute('login');
        }
       
        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        return new ViewModel();
    }
}
