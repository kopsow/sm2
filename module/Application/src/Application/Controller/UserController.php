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
use Zend\Mvc\MvcEvent;
use Application\Model\Users;
use Application\Model\Patient;

class UserController extends AbstractActionController
{
    private $usersTable;
    private $patientTable;
    private $physicianTable;
    
    public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if (!$this->session->role)
        {
            echo $this->session->role;
            $this->redirect()->toRoute('login');
        }elseif($this->session->role == 2 || $this->session->role == 3)
        {
            $this->redirect()->toRoute('home');
        }
       
        return parent::onDispatch($e);
    }
    
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
    
    public function indexAction()
    {
       
       
        return new ViewModel();
    }
    
    public function addPatientAction()
    {
        $formUsers = new \Application\Form\UsersForm;
        $formPatient =  new \Application\Form\PatientForm;
        
        return new ViewModel(array(
           'formPatient'   => $formPatient ,
            'formUser'      => $formUsers,
        ));
    }
    public function addAction()
    {
        $form = new \Application\Form\UsersForm();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $user = new \Application\Model\Users;
            
            $form->setData($request->getPost());
            $form->setInputFilter($user->getInputFilter());
            
            if ($form->isValid())
            {
               $user = new Users();
               $user->exchangeArray($form->getData());
               $this->getUsersTable()->addUsers($user);
               
               switch ($request->getPost('role'))
               {
                  
                   case 2:
                       $this->redirect()->toRoute('user',array('action'=>'patient','id'=>$this->getUsersTable()->lastInsertId()));
                       break;
                   case 3:
                       $this->redirect()->toRoute('user',array('action'=>'physician','id'=>$this->getUsersTable()->lastInsertId()));
                       break;
                   default:
                       $this->redirect()->toRoute('user',array('action'=>'list'));
                  
                   
               }
               
            }
        }
        return new ViewModel(array(
            'form'  =>  $form
        ));
    }
    
    public function listAction()
    {
        if ($this->session->role != 2 && $this->session->role !=3)
        {
            $users = $this->getUsersTable()->fetchAll();
        } else {
            $users = $this->getUsersTable()->getUsers($this->session->id);
        }
        
        
        return new ViewModel(array(
            'users'     =>  $users,
            'session'   => $this->session
        ));
    }
    
    public function patientAction()
    {
        $form = new \Application\Form\PatientForm();
        $request = $this->getRequest();
        $form->get('user_id')->setValue($this->params()->fromRoute('id'));
        if ($request->isPost())
        {
            $patient = new \Application\Model\Patient;
            
            $form->setData($request->getPost());
            $form->setInputFilter($patient->getInputFilter());
            
            if($form->isValid())
            {
                
                $patient->exchangeArray($form->getData());
                
                $this->getPatientTable()->savePatient($patient);
                $this->redirect()->toRoute('user',array('action'=>'list'));
            }
        }
        
        
        return new ViewModel(array(
            'form'  => $form
        ));
    }
    
    public function physicianAction()
    {
        $request = $this->getRequest();
        $form = new \Application\Form\PhysicianForm();
        $form->get('user_id')->setValue($this->params()->fromRoute('id'));
        
        if($request->isPost())
        {
            $physician = new \Application\Model\Physician;
            $form->setData($request->getPost());
            $form->setInputFilter($physician->getInputFilter());
            
            if($form->isValid())
            {
                $physician->exchangeArray($form->getData());
                $this->getPhysicianTable()->savePhysician($physician);
                $this->redirect()->toRoute('user',array('action'=>'list'));
            }
        }
        return new ViewModel(array(
            'form'  =>  $form
        ));
    }
    public function editAction()
    {
        
        $form = null;
        if ($this->session->role == 1) 
        {
            $request = $this->getRequest();
        
            $id = (int) $this->params()->fromRoute('id');
            $data = $this->getUsersTable()->getUsers($id);
            $form = new \Application\Form\UsersForm();
            $form->setData((array)$data);
            $form->remove('role');
            if($request->isPost())
            {
                $user = new Users();

                $user->exchangeArray($request->getPost());

                $this->getUsersTable()->saveUsers($user);
                $this->redirect()->toRoute('user',array('action'=>'list'));
            }
        } elseif($this->session->role == 2) {
           
            $data = $this->getUsersTable()->getUsers($this->session->id);
            $form = new \Application\Form\UsersForm();
            $formPatient = new \Application\Form\PatientForm();
            $form->setData((array)$data);
            $formPatient->setData((array)$this->getPatientTable()->getPatientUid($this->session->id));
            
            
            
            return new ViewModel(array(
            'form'          =>  $form,
            'formPatient'   =>  $formPatient
            ));
           
        } else {
            $this->redirect()->toRoute('login',array('action'=>'access'));
        }
        
         return new ViewModel(array(
            'form'  =>  $form,
        ));
        
    }
    
    public function blockAction()
    {
        $this->getUsersTable()->blockUsers((int) $this->params()->fromRoute('id'));
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
    
    public function deleteAction()
    {
         $this->getUsersTable()->deleteUsers((int) $this->params()->fromRoute('id'));
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
}
