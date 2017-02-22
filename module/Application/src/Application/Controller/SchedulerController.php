<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Form\PhysicianForm;
use Application\Model\Scheduler;
use Application\Model\SchedulerTable;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;



class SchedulerController extends AbstractActionController
{
    
    
    public $physicianTable;
    public $schedulerTable;
    public $usersTable;
    public $schedulerListTable;
    public $daysTable;
    public $session;
    
   public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if ($this->session->role == 2)
        {
              $this->redirect()->toRoute('home');
           
        }
        switch ($this->session->role)
        {
            case 1:
                $this->layout('layout/admin');
                $this->layout()->setVariable('scheduler_active', 'active');
                break;
            case 2:
                $this->layout('layout/patient');
                $this->layout()->setVariable('scheduler_active', 'active');
                break;
            case 3:
                $this->layout('layout/physician');
                $this->layout()->setVariable('scheduler_active', 'active');
                break;
            case 4:
                $this->layout('layout/register');
                $this->layout()->setVariable('scheduler_active', 'active');
                break;
            default:
                $this->layout('layout/layout');
        }
       
        return parent::onDispatch($e);
    }
    
    public function __construct() {
        $this->session = new Container('loginData');
    }
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
    
    public function getSchedulerTable()
    {
        if (!$this->schedulerTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->schedulerTable;
    }
    
    public function getSchedulerListTable()
    {
        if (!$this->schedulerListTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerListTable = $sm->get('SchedulerList\Model\SchedulerListTable');
        }
        return $this->schedulerListTable;
    }
    public function getDaysTable()
    {
        if (!$this->daysTable) {
            $sm = $this->getServiceLocator();
            $this->daysTable = $sm->get('Days\Model\DaysTable');
        }
        return $this->daysTable;
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
        
        if ($this->session->role === 'physician')
        {
            $this->layout('layout/physician');
            $this->layout()->setVariable('scheduler_active', 'active');
            $scheduler = $this->getSchedulerTable()->getSchedulerPhysician($this->session->id,date('m'));
        }elseif($this->session->role === 'admin')
        {
             $this->layout('layout/admin');
            $this->layout()->setVariable('scheduler_active', 'active');
            $scheduler = $this->getSchedulerTable()->fetchAll();
        }
        else  {
            $this->redirect()->toRoute('autoryzacja');
        }
        return new ViewModel(array(
            'schedulers'    => $scheduler,
            'session'       => $this->session
        ));
    }
    
    public function addAction() 
    {
        $this->layout()->setVariable('schedulerAdd_active', 'active');
        if($this->session->role !=1)
        {
            $this->redirect()->toRoute('home');
        }
        $request = $this->getRequest();
        
        if ($request->isPost()) 
        {
           
           $data =  explode(',', $request->getPost('date'));
           asort($data);
           
           for ($i=0 ; $i<count($data) ; $i++) 
           {               
               $day =  date("w",  strtotime($data[$i]));
               
               switch ($day)
               {
                   
                   case 1:                         
                      $this->addSchedulerDb($data[$i], 'mon_start', 'mon_end');
                       break;
                   case 2:
                      $this->addSchedulerDb($data[$i], 'tue_start', 'tue_end');
                       break;
                   case 3:
                       $this->addSchedulerDb($data[$i], 'wed_start', 'wed_end');
                       break;
                   case 4:
                      $this->addSchedulerDb($data[$i], 'thu_start', 'thu_end');
                       break;
                   case 5:
                       $this->addSchedulerDb($data[$i], 'fri_start', 'fri_end');
                       break;
                   
               }
           }
          
        }
        $form = new \Application\Form\SchedulerForm();
        return new ViewModel(array(
            'physicians' =>$form,
        ));
       
    }
    public function deleteAction() 
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
             return $this->redirect()->toRoute('scheduler');
         } else {
               
               $this->getSchedulerTable()->deleteScheduler($id);
         }
         
         
         return $this->redirect()->toRoute('scheduler',array('action'=>'list'));
    }
    public function showAction() {
         $form = new physicianForm();
        
        $request = $this->getRequest();
        $schedulerList = null;
        
       
        if ($request->isPost()) {
            
            $physicianId =(int) $request->getPost('physicianId');
            $orderBy = $request->getPost('orderBy');
          
            if (!empty($orderBy)) {               
                $schedulerList =  $this->getSchedulerTable()->showScheduler($orderBy,$physicianId);
                $form->get('physicianScheduler')->setAttribute('value',$physicianId);                
            } else {
                $schedulerList =  $this->getSchedulerTable()->showScheduler(null,$physicianId);
                $form->get('physicianScheduler')->setAttribute('value',$physicianId);
            }
            
        } else {
          $schedulerList =  $this->getSchedulerTable()->showScheduler();
        }
        return new ViewModel(array(
            'schedulers' => $schedulerList,
            'physicians' => $form
        ));
    }
    public function listAction()
    {
        $result = null;
        $this->layout()->setVariable('schedulerList_active', 'active');
        
        if ($this->session->role == 3)
        {
            $physician = $this->getUsersTable()->getUsers($this->session->id);
            $name = $physician->name." ".$physician->surname;
            $result = $this->getSchedulerListTable()->getSchedulerPhysicianName($name);
        }else {
            $result = $this->getSchedulerListTable()->fetchAll();
        }
        
        return new ViewModel(array(
             'schedulers'=>$result,
        )
               
                );
    }
   private function addSchedulerDb($data,$start,$end) {
      
      
       $request = $this->getRequest();
      
        if ($request->getPost($start)!='00:00' && $request->getPost($end) !='00:00')
        {            
            
                $date_start = new \DateTime($data.$request->getPost($start));
                $date_end = new \DateTime($data.$request->getPost($end));
            
                $physcianId = $this->getPhysicianTable()->getPhysicianUid($request->getPost('physician'));
            $schedule = new Scheduler();
            $dataSchedule = array(
                'physician_id'   =>  $physcianId->id,
                'date_start'     =>  $date_start->format('Y-m-d H:i'),
                'date_end'       =>  $date_end->format('Y-m-d H:i')
            );            
             if ($this->getSchedulerTable()->checkDate($request->getPost('physician'),$date_start->format('Y-m-d H:i')) === 0)
             {
                 $schedule->exchangeArray($dataSchedule);
                 
                 /*echo '<pre>';
                 
                 var_dump($dataSchedule);
                 echo '</pre>';*/
            $this->getSchedulerTable()->saveScheduler($schedule);
            
        return $this->getSchedulerTable()->lastInsertId();
             }
            
                      
        } else {
            return false;
        }
   }
}


