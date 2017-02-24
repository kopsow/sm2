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
use Application\Model\Physician;
use Zend\Debug\Debug as debug;

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
    
    
    public function avatarAction()
    {
        $request = $this->getRequest();
        $form = new \Application\Form\UploadForm();

        if($request->isPost())
        {
            $post = array_merge_recursive($request->getPost()->toArray(),
                            $request->getFiles()->toArray()
            );
            $form->setData($post);
            if($form->isValid())
            {
                $data = $form->getData();
                $data['image'] = $post['image-file']['name'];
                //\Zend\Debug\Debug::dump($data);
               $this->getPhysicianTable()->savePhysicianDesc($data);
               move_uploaded_file($post['image-file']['tmp_name'], './public/img/physician/'.$post['image-file']['name']);
               
               $this->redirect()->toRoute('user',array('action'=>'avatar'));
            }else {
               // \Zend\Debug\Debug::dump($post);
            }

        }
        
        
        return new ViewModel(array(
            'form'  => $form
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
                $role = new \Zend\Form\Element\Hidden('role');
                $role->setValue('2');
                $form->add($role);
                $form->remove('verified');
                break;
        }
        
        if ($request->isPost())
        {
            $user = new \Application\Model\Users;
            
            $form->setData($request->getPost());
           

            if ($this->session->role == 1 || $this->session->role == 4)
            {
                             
                $user->getInputFilter()->remove('email');
            }
             $form->setInputFilter($user->getInputFilter());
            if ($form->isValid())
            {
               $user = new Users();
               $user->exchangeArray($form->getData());
               if($this->session->role == 4)
               {
                   $user->role=2;
                   $user->verified=1;
               }
               //\Zend\Debug\Debug::dump($user);
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
    
    private function editAdmin($id,$data)
    {
        $user = new Users();
        $user->exchangeArray((array)$data);
        $user->id = $id;  

        if($user->role == 2)
        {
            $patient = new Patient();
            $patient->exchangeArray((array)$data);  
            $form = new \Application\Form\PatientForm();
            $form->setData($data);
            $patient->id = $this->getPatientTable()->getPatientUid($id)->id;
            $patient->user_id = $id;           
            $this->getPatientTable()->savePatient($patient);
        }
        
        if($user->role == 3)
        {
            $physician = new Physician();
            $physician->exchangeArray((array)$data);
            $physician->id = $this->getPhysicianTable()->getPhysicianUid($id)->id;
            $physician->user_id = $id;            
            $this->getPhysicianTable()->savePhysician($physician);
        }
        $this->getUsersTable()->saveUsers($user);
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
    
    private function editPatient($data)
    {
        $user = new Users();
        $userInfo = $this->getUsersTable()->getUsers($this->session->id);
        $user->exchangeArray((array)$data);
        $user->id = $this->session->id;
        $user->role = $userInfo->role;
        $user->verified= $userInfo->verified;
        $user->login = $userInfo->login;
        $user->email = $userInfo->email;
        $patient = new Patient();
        $patient->exchangeArray($data);
        $patient->id = $this->getPatientTable()->getPatientUid($this->session->id)->id;
        $patient->birthday = $this->getPatientTable()->getPatientUid($this->session->id)->birthday;
        $patient->user_id = $this->session->id;
        $this->getUsersTable()->saveUsers($user);
        $this->getPatientTable()->savePatient($patient);
        $this->redirect()->toRoute('patient');
    }
    
    private function editPhysician($data)
    {
        $user = new Users();
        $userInfo = $this->getUsersTable()->getUsers($this->session->id);
        $user->exchangeArray((array)$data);
        $user->id = $this->session->id;
        $user->role = $userInfo->role;
        $user->verified = $userInfo->verified;
        $physician = new Physician();
        $physician->exchangeArray($data);
        $physician->id = $this->getPhysicianTable()->getPhysicianUid($this->session->id)->id;
        $physician->user_id = $this->session->id;
        $this->getUsersTable()->saveUsers($user);
        $this->getPhysicianTable()->savePhysician($physician);
        $this->redirect()->toRoute('user',array('action'=>'edit'));
    }
    
    private function editRegister($id,$data)
    {
        $user = new Users();
        $user->exchangeArray((array)$data);
        $user->id = $id;  
        $userInfo = $this->getUsersTable()->getUsers($id);
        
        if($userInfo->role == 2)
        {
            $user->role = 2;
            $user->verified = $userInfo->verified;
            $patient = new Patient();
            $patient->exchangeArray((array)$data);            
            $patient->id = $this->getPatientTable()->getPatientUid($id)->id;
            $patient->user_id = $id;   
            $this->getPatientTable()->savePatient($patient);
        }
        
        if($user->role == 3)
        {
            $user->role = 2;
            $user->verified = $userInfo->verified;
            $physician = new Physician();
            $physician->exchangeArray((array)$data);
            $physician->id = $this->getPhysicianTable()->getPhysicianUid($id)->id;
            $physician->user_id = $id;            
            $this->getPhysicianTable()->savePhysician($physician);
        }

        $this->getUsersTable()->saveUsers($user);
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
    public function editAction()
    {
        
        $formUser = new \Application\Form\UsersForm();
        $request     = $this->getRequest();
        $id = null;
        $formPatient = null;
        $formPhysician = null;
                
        
        
        if($request->isPost())
        {
            
            $user = new Users();
            $user->exchangeArray($request->getPost());
            $patient = new \Application\Form\PatientForm();
            $patient->setData($request->getPost());
            
            if($request->getPost('pesel'))
            {
                $this->redirect()->toRoute('home');
            }
            if($this->session->role == 1)
            {
                $this->editAdmin($this->params()->fromRoute('id'),$request->getPost());               
                
            }
            if($this->session->role == 2)
            {
                $this->editPatient($request->getPost());                
            }
            
            if($this->session->role == 3)
            {
                $this->editPhysician($request->getPost());
               
            }
            
            if($this->session->role == 4)
            {
                $this->editRegister($this->params()->fromRoute('id'),$request->getPost());
            }
        } else {
           
            if($this->session->role == 2 )
            {
                $id = $this->session->id;
                $dataUser = $this->getUsersTable()->getUsers($id);
                $formUser->setData((array)$dataUser);
            }elseif($this->session->role == 3)
            {
                $id = $this->session->id;
                $dataUser = $this->getUsersTable()->getUsers($id);
                $formUser->setData((array)$dataUser);
            }else {
                $id = $this->params()->fromRoute('id');
                $dataUser = $this->getUsersTable()->getUsers($id);
                $formUser->setData((array)$dataUser);
            }
           
           
           switch($dataUser->role)
           {
               case 2:
                   $formPatient = new \Application\Form\PatientForm();
                   $data = $this->getPatientTable()->getPatientUid($id);                   
                   $formPatient->setData((array)$data);
                   break;
               case 3:
                   $formPhysician = new \Application\Form\PhysicianForm();
                   $data = $this->getPhysicianTable()->getPhysicianUid($id);
                   $formPhysician->setData((array)$data);
                   break;
               
           }
           
           switch($this->session->role)
           {
               case 2:
                   $formUser->get('login')->setAttribute('disabled', 'disabled');
                   $formUser->get('email')->setAttribute('disabled', 'disabled');
                   $formUser->get('role')->setAttribute('disabled', 'disabled');
                   $formUser->get('verified')->setAttribute('disabled', 'disabled');
                   $formPatient->get('pesel')->setAttribute('disabled', 'disabled');                   
                   $formPatient->get('birthday')->setAttribute('disabled', 'disabled');     
                   break;
               case 3:
                   $formUser->get('email')->setAttribute('disabled', 'disabled');
                   $formUser->get('role')->setAttribute('disabled', 'disabled');
                   $formUser->get('verified')->setAttribute('disabled', 'disabled');
                   break;
               case 4:
                   $formUser->get('role')->setAttribute('disabled', 'disabled');
                   $formUser->get('verified')->setAttribute('disabled', 'disabled');
                   break;
                   
                   
           }
        }
        
        
         return new ViewModel(array(
                'form'          =>  $formUser,
                'formPatient'   =>  $formPatient,
                'formPhysician' =>  $formPhysician
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
