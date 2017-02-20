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
        }
        switch ($this->session->role)
        {
            case 1:
                $this->layout('layout/admin');
                $this->layout()->setVariable('user_active', 'active');
                break;
            case 2:
                $this->layout('layout/patient');
                $this->layout()->setVariable('user_active', 'active');
                break;
            case 3:
                $this->layout('layout/physician');
                $this->layout()->setVariable('user_active', 'active');
                break;
            case 4:
                $this->layout('layout/register');
                $this->layout()->setVariable('user_active', 'active');
                break;
            default:
                $this->layout('layout/layout');
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
    
    public function activeAction()
    {
        $this->getUsersTable()->activeUsers($this->params()->fromRoute('id'));
        $this->redirect()->toRoute('user',array('action'=>'list'));
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
        $this->layout()->setVariable('userAdd_active', 'active');
        $this->layout()->setVariable('user_active', '');
        $form = new \Application\Form\UsersForm();
        $request = $this->getRequest();
        
        switch($this->session->role)
        {
            case 4:
                $form->remove('role');
                $form->remove('verified');
                break;
        }
        
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
        $this->layout()->setVariable('userList_active', 'active');
        
        $request = $this->getRequest();
        $form= new \Application\Form\FilterForm;
        $users = NULL;
        
        
        if ($this->session->role == 1)        
        {
            
            $users = $this->getUsersTable()->fetchAll();
            
        }elseif($this->session->role == 4 || $this->session->role == 3)
        {
            $users = $this->getUsersTable()->getUsersRole(2);
        }
        elseif($this->session->role == 2) {
            $users = $this->getUsersTable()->getUsers($this->session->id);
        }
        
        
        if ($request->isPost())
        {
            if ($this->session->role == 4)
            {
                $role = 2;
            } else {
                $role = $request->getPost('role');
            }
            $form->get('name')->setValue($request->getPost('name'));
            $form->get('surname')->setValue($request->getPost('surname'));
            $form->get('login')->setValue($request->getPost('login'));
            $form->get('email')->setValue($request->getPost('email'));
            //$form->get('role')->setValue($role);
            $form->get('verified')->setValue($request->getPost('verified'));
            $form->get('sort')->setValue($request->getPost('sort'));
            
            $users = $this->getUsersTable()->filter(
                    $request->getPost('name'),
                    $request->getPost('surname'),
                    $request->getPost('login'),
                    $request->getPost('email'),
                    $role,
                    $request->getPost('verified'),
                    $request->getPost('sort')
                    );
            
           
        } 
        
        return new ViewModel(array(
            'users'     =>  $users,
            'session'   =>  $this->session,
            'form'      =>  $form
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
        
        $form = new \Application\Form\UsersForm();
        $formPatient = null;
        $request     = $this->getRequest();
        
        
        
        switch($this->session->role)
        {
           
            case 2:
                $data = $this->getUsersTable()->getUsers($this->session->id);
                
                $formPatient = new \Application\Form\PatientForm();
                $form->setData((array)$data);
                $formPatient->setData((array)$this->getPatientTable()->getPatientUid($this->session->id));
                $formPatient->get('pesel')->setAttribute('disabled', 'disabled');
                $form->get('email')->setAttribute('disabled', 'disabled');
                if($request->isPost())
                {
                    $user = new Users();
                    $patient = new Patient();
                    $user->exchangeArray($request->getPost());
                    $patient->exchangeArray($request->getPost());
                    $user->id = $this->session->id;
                    $user->role = 2;
                    $user->verified = 1;
                    $user->email = $this->session->email;
                    $patient->id = $this->getPatientTable()->getPatientUid($this->session->id)->id;
                    $patient->user_id = $this->session->id;
                    $this->getUsersTable()->saveUsers($user);
                    $this->getPatientTable()->savePatient($patient);
                    $this->redirect()->toRoute('user',array('action'=>'edit'));
                }                
                break;
                
            case 4:
                $id = (int) $this->params()->fromRoute('id');
                $data = $this->getUsersTable()->getUsers($id);
                $dataPatient = $this->getPatientTable()->getPatientUid($id);
                $form->get('role')->setAttribute('disabled', 'disabled');
                $form->get('verified')->setAttribute('disabled', 'disabled');
                $form->setData((array)$data);
                $formPatient = new \Application\Form\PatientForm();
                $formPatient->setData((array)$dataPatient);
                

                
                if($request->isPost())
                {
                    $user = new Users();
                    $user->exchangeArray($request->getPost());
                    $user->id = $id;
                    $this->getUsersTable()->saveUsers($user);
                    $this->redirect()->toRoute('user',array('action'=>'list'));
                }
                break;
            default:
                $id = (int) $this->params()->fromRoute('id');
                $data = $this->getUsersTable()->getUsers($id);
                
                $form->setData((array)$data);
                if($request->isPost())
                {
                    $user = new Users();
                    $user->exchangeArray($request->getPost());
                    $user->id = $id;
                    $this->getUsersTable()->saveUsers($user);
                    $this->redirect()->toRoute('user',array('action'=>'list'));
                }
                break;
                
        }
        
         return new ViewModel(array(
                'form'          =>  $form,
                'formPatient'   =>  $formPatient
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
