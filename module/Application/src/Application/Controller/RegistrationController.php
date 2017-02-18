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
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

use Zend\Mvc\MvcEvent;

class RegistrationController extends AbstractActionController
{
    private $patientTable;
    private $physicianTable;
    private $usersTable;
    private $registrationTable;
    private $schedulerTable;
    
    public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if (!$this->session->role)
        {
            $this->redirect()->toRoute('login');
        }
       
        return parent::onDispatch($e);
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
    
     public function getSchedulerTable()
    {
        if (!$this->schedulerTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->schedulerTable;
    }
    
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
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
       $physicians = $this->getUsersTable()->getUsersRole('3');
        
        return new ViewModel(array(
            'lekarze' =>$physicians
        ));
    }
    
    public function addAction()
    {
        
    }
    public function oneAction()
    {
         
        
        
        if ($this->session->role == 2)
        {
            $id = (int) $this->params()->fromRoute('param');
            $this->session->idPhysician = $id;
            $result = $this->getPhysicianTable()->getPhysicianUid($id);
           
            $resultDay = $this->getSchedulerTable()->getSchedulerPhysician($result->id,date('m'));
        } 
        
       
        
       
        return new ViewModel(array(
            'days'  =>  $resultDay
        ));
    }
    
    public function twoAction()
    {
       
        $day = $this->params()->fromRoute('param');
        $this->session->visit_date = $day;
        
            $physicianId = $this->session->idPhysician;
            $visitDate = trim($this->session->visit_date);
            $resultId = $this->getPhysicianTable()->getPhysicianUid($physicianId);
            $result = $this->getSchedulerTable()->getSchedulerPhysicianHours($resultId->id,$visitDate);
           
            $time_start = date('H:i',  strtotime($result->date_start));
            $time_end = date('H:i',  strtotime($result->date_end));

            $godzinyPrzyjec = array();
            $godzinyPrzyjec[]=$time_start;

            while ($time_start != $time_end)
            {
               $time_start = date('H:i',  strtotime($time_start.'+15 minutes'));
               $godzinyPrzyjec[]=$time_start;
            }
            $busyHours = $this->getRegistrationTable()->busyHours($resultId->id,$visitDate);
            $busy = array();
            foreach ($busyHours as $hour)
            {
                $busy[]=date('H:i',  strtotime($hour->visit_date));
            }
            return new ViewModel(array(
               'physician'     =>  $this->getUsersTable()->getUsers($this->session->idPhysician),
               'day'           =>  $this->session->visit_date,
               'hours'         =>  $godzinyPrzyjec ,
               'busy'          => $busy
            ));
    }
    
    public function threeAction()
    {
        
        
        
        if ($this->session->role == 2)
        {
            $patientId      =   $this->session->id;
        }
       
        $physicianId    =   $this->session->idPhysician;
        $visit_date     =   $this->session->visit_date;
        $visit_time     =   $this->params()->fromRoute('param');
        
        $data = array (
            'patient_id'        =>  $this->getPatientTable()->getPatientUid($patientId)->id,
            'physician_id'      =>  $this->getPhysicianTable()->getPhysicianUid($physicianId)->id,
            'visit_date'        =>  date('Y-m-d H:i:s',strtotime($visit_date." ".$visit_time)),
            'registration_date' =>  date('Y-m-d H:s'),
        );
  
        
            $physicianInfo = $this->getUsersTable()->getUsers($physicianId);
        
        echo '<pre>';
        var_dump($this->session->email);
        echo '</pre>';
        $registration = new \Application\Model\Registration;
        $registration->exchangeArray($data);
        $this->getRegistrationTable()->saveRegistration($registration);
        $body = 'Witaj! '.$this->session->name.'<br/>'
                . 'Potwierdzamy dokonanie rezerwacji do <br/>'
                . 'Lekarza: '.$physicianInfo->name." ".$physicianInfo->surname.'<br />'
                . 'W dniu: '.$visit_date.'<br />'
                . 'Na godzinę: '.$visit_time;
        $this->sendMail($this->session->email, 'Rejestracja wizyty', $body);
        switch ($this->session->role)
        {
            case patient:
                $this->redirect()->toRoute('patient');
                break;
            case physician:
                $this->redirect()->toRoute('physician');
                break;
        }
        
        
    }
  
    private function sendMail($to,$subject,$body)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');
        $message = new \Zend\Mail\Message();       
        $message->addFrom("rejestracja@super-med.pl", "Super-Med")
        ->addTo($to)
        ->setSubject($subject);
        $message->setEncoding("UTF-8");
        $bodyHtml = ($body);
        $htmlPart = new MimePart($bodyHtml);
        $htmlPart->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        $message->setBody($body);
        $transport->send($message);
        $this->redirect()->toRoute('patient');
    }
    
    public function cancelAction()
    {
        $id = (int) $this->params()->fromRoute('param');
        $this->getRegistrationTable()->deleteRegistration($id);
        $this->redirect()->toRoute('patient',array('action'=>'visit'));
    }
}
