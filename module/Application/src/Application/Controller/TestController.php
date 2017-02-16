<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Form;
use Application\Model\Patient;
use Application\Model\PatientTable;

use Application\Model\Users;

class TestController extends AbstractActionController
{
    private $patientTable;
    private $usersTable;
    
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new Form\UsersForm;
        if ($request->isPost())
        {
           $users = new Users();
           $form->setInputFilter($users->getInputFilter());
           $form->setData($request->getPost());
           
           
        
           if ($form->isValid())
           {
              
               $user = new Users();
               $user->exchangeArray($form->getData());
               $this->getUsersTable()->addUsers($user);
               
           }
        }
        
        $form->get('verified')->setLabel('Zweryfikowany');
        
        return new ViewModel(array(
            'form'  =>  $form,
            'patient'   => $this->getPatientTable()->fetchAll(),
        ));
    }
}
