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
        
        if(!$this->session->role)
        {
            $this->redirect()->toRoute('login');
        }
       switch ($this->session->role)
       {
           case 1:
               $this->layout('layout/admin');
               $this->layout()->setVariable('registration_active', 'active');
               break;
           case 2:
               $this->layout('layout/patient');
               $this->layout()->setVariable('registration_active', 'active');
               break;
           case 3:
               $this->layout('layout/physician');
               $this->layout()->setVariable('registration_active', 'active');
               break;
           case 4:
               $this->layout('layout/register');
               $this->layout()->setVariable('registration_active', 'active');
               break;
           default:
               $this->layout('layout/layout');
               $this->layout()->setVariable('registration_active', 'active');
               
               
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
       $physicians = null;
       $result = null;
     
        if ($this->chceckLimit($this->session->id) == TRUE)
        {
            $physicians = $this->getUsersTable()->getUsersRole('3');
        } else {
            $patient = new \Application\Controller\PatientController;
        
            $id = $this->getPatientTable()->getPatientUid($this->session->id)->id;
            $result = $patient->getRegistration($this->session->id);
            
        }
        return new ViewModel(array(
            'lekarze'   =>  $physicians,
            'limit'=>$result
        ));
    }
    
    /**
     * Rejestracja pacjenta przez rejestratorkę
     * @return ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $day = null;
        $godzinyPrzyjec = null;
        $result = null;
        $busy = null;
        $physicianInfo = null;
        $form = new \Application\Form\FilterForm();
        
        if($request->isPost())
        {
            $patient    = $request->getPost('patient');
            if($this->session->role == 3)
            {
                $physician  = $this->getPhysicianTable()->getPhysicianUid($this->session->id)->id;
            }else {
                $physician  = $this->getPhysicianTable()->getPhysicianUid($request->getPost('physician'))->id;
            }
            
            $this->session->patient = $patient;
            $this->session->physician = $physician;
            $day = $this->getSchedulerTable()->getSchedulerPhysician($physician,date('Y-m-d'));  

            
           
        }
        if($this->params()->fromRoute('param'))
        {
            $visit_date = $this->params()->fromRoute('param');
            $this->session->visit_date = $visit_date;
           
            $result = $this->getSchedulerTable()->getSchedulerPhysicianHours($this->session->physician,$visit_date);
           
            $time_start = date('H:i',  strtotime($result->date_start));
            $time_end = date('H:i',  strtotime($result->date_end));

            $godzinyPrzyjec = array();
            $godzinyPrzyjec[]=$time_start;

            while ($time_start != $time_end)
            {
               $time_start = date('H:i',  strtotime($time_start.'+15 minutes'));
               $godzinyPrzyjec[]=$time_start;
            }
            $busyHours = $this->getRegistrationTable()->busyHours($this->session->physician,$visit_date);
            $busy = array();
            foreach ($busyHours as $hour)
            {
                $busy[]=date('H:i',  strtotime($hour->visit_date));
            }
            
        }
        if ($this->session->role == 3)
        {
            $form = new \Application\Form\FilterForm();
            $physicianId =  $this->session->id;
            $physicianInfo = $this->getUsersTable()->getUsers($this->session->id);
            $view = new ViewModel(array(
                'form'      =>  $form,
                'result'    => $day,
                'hours'     => $godzinyPrzyjec,
                'busy'      => $busy,
                'physician' => $physicianInfo
            ));
        }
        if ($this->session->role == 4)
        {
            
            if ($this->session->physician)
            {
              $physicianId = $this->getPhysicianTable()->getPhysician($this->session->physician)->user_id;
              $physicianInfo = $this->getUsersTable()->getUsers($physicianId);
            }
            $form = new \Application\Form\FilterForm();
            $view = new ViewModel(array(
                'form'      =>  $form,
                'result'    => $day,
                'hours'     => $godzinyPrzyjec,
                'busy'      => $busy,
                'physician' => $physicianInfo
            ));
        }
        
        return $view;
    }
    
    public function finalAction()
    {
       $visit_date = date('Y-m-d H:i:s',strtotime($this->session->visit_date." ".$this->params()->fromRoute('param')));
        $data = array (
            'patient_id'        =>  $this->getPatientTable()->getPatientUid($this->session->patient)->id,
            'physician_id'      =>  $this->session->physician,
            'visit_date'        =>  $visit_date,
            'registration_date' =>  date('Y-m-d H:s'),
        );
  
             $physicianInfo = $this->getUsersTable()->getUsers($this->getPhysicianTable()->getPhysician($this->session->physician)->user_id);
        
         
        $registration = new \Application\Model\Registration;
        $registration->exchangeArray($data);
        $this->getRegistrationTable()->saveRegistration($registration);
        $patientInfo = $this->getUsersTable()->getUsers($this->session->patient);
       if ($this->session->role == 2){
           $name = $this->session->name;
           $email = $this->session->email;
       }else {
           $name = $patientInfo->name;
           $email = $patientInfo->email;
       }
        $body = 'Witaj! '.$name.'<br/>'
                . 'Potwierdzamy dokonanie rezerwacji do <br/>'
                . 'Lekarza: '.$physicianInfo->name." ".$physicianInfo->surname.'<br />'
                . 'W dniu: '.date('Y-m-d',  strtotime($visit_date)).'<br />'
                . 'Na godzinę: '.date('H:i',  strtotime($visit_date));
        
        
        //$this->sendMail($this->session->email, 'Rejestracja wizyty', $body);
        if($email)
        {
            $this->sendMail2($body, $email, 'Informacja o zarezerwowaniu wizyty');
        }
        
        $this->redirect()->toRoute('registration',array('action'=>'list'));
    }
    
    
    /**
     * funkcja sprawdza czy pacjent nie wykorzystał limitu rezerwacji
     * @param type $patientId
     */
    private function chceckLimit($patientId)
    {
        $id =  $this->getPatientTable()->getPatientUid($patientId)->id;
        $result = $this->getRegistrationTable()->getRegistrationUser($id)->current();
        $date_current = date('Y-m-d');
        $date_visit = date(('Y-m-d'),  strtotime($result['visit_date']));

        
        
        //Czy data nowej rezerwacji jest równa dacie wizyty
        if($date_current == $date_visit)
        {
            return true;
        }elseif ($date_current> $date_visit)
        {
           return true;
        }else {
           return false;
        }
    }
    
    public function oneAction()
    {
         
        
        
        if ($this->session->role == 2)
        {
            $id = (int) $this->params()->fromRoute('param');
            $this->session->idPhysician = $id;
            $result = $this->getPhysicianTable()->getPhysicianUid($id);
           
            $resultDay = $this->getSchedulerTable()->getSchedulerPhysician($result->id,date('Y-m-d'));
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
        $this->redirect()->toRoute('patient',array('action'=>'visit'));
        
        
    }
  
    private function sendMail($to,$subject,$bodyInput)
    {
        var_dump($to);
        $transport = $this->getServiceLocator()->get('mail.transport');
        $message = new \Zend\Mail\Message();       
        $message->addFrom("rejestracja@super-med.pl", "Super-Med")
        ->addTo($to)
        ->setSubject($subject);
        $message->setEncoding("UTF-8");
        $bodyHtml = ($bodyInput);
        $htmlPart = new MimePart($bodyHtml);
        $htmlPart->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        $message->setBody($body);
        $transport->send($message);
       
    }
    
    public function completedAction()
    {
        $id = (int) $this->params()->fromRoute('param');
        $this->getRegistrationTable()->completedRegistration($id);
        $this->redirect()->toRoute('registration',array('action'=>'list'));
    }
    public function cancelAction()
    {
        $id = (int) $this->params()->fromRoute('param');
        
        $info = (array) $this->getRegistrationTable()->getRegistrationUser($id)->current();
        
          $body ='Witaj '.$info['name'].'<br />'
                  . 'Informujemy, że twoja wizyta <br />w dniu: '.date('Y-m-d',strtotime($info['visit_date'])).''
                  . '<br />'
                  . 'na godzinę: '.date('H:i',  strtotime($info['visit_date'])).'<br />'
                  . 'do lekarza: '.$info['physician'].'<br />'
                  . 'Została odwołana';


       $this->getRegistrationTable()->deleteRegistration($id);
       if($info['email'])
       {
          $this->sendMail2($body, $info['email'], 'Anulowanie wizyty'); 
       }
       
        
        if ($this->session->role == 4)
        {
            $this->redirect()->toRoute('registration',array('action'=>'list'));
        } elseif($this->session->role == 3) {
           $this->redirect()->toRoute('registration',array('action'=>'list'));
        }
        elseif($this->session->role == 1)
        {
            $this->redirect()->toRoute('registration',array('action'=>'list'));
        }
        else {
            $this->redirect()->toRoute('patient',array('action'=>'visit'));
        }
       
    }
    
    
    private function sendMail2($body_html,$to,$subject)
    {
        $body = new \Zend\Mime\Message;
                  
        $bodyHtml =$body_html;
        $mail = new \Zend\Mail\Message;
        $mail->addFrom('rejestracja@super-med.pl','SuperMed')
                ->addTo($to)
                ->setSubject($subject);

        $mail->setEncoding('UTF-8');
        if ($mail->isValid())
        {
            $bodyHtml = ($bodyHtml);
            $htmlPart = new MimePart($bodyHtml);
            $htmlPart->type = "text/html";
            $body = new MimeMessage();
            $body->setParts(array($htmlPart));
            $mail->setBody($body);
            $transport = new \Zend\Mail\Transport\Smtp();
            $options   = new \Zend\Mail\Transport\SmtpOptions(array(
                'host'              => 's44.linuxpl.com',
                'connection_class'  => 'login',
                'connection_config' => array(
                    'username' => 'rejestracja@super-med.pl',
                    'password' => 'AoT7kIhf',
                ),
            ));
            $transport->setOptions($options);
           return $transport->send($mail);
        }else {
            return false;
        }
    }
    public function listAction()
    {
        $this->layout()->setVariable('registration_active', '');
        $this->layout()->setVariable('registrationList_active', 'active');
        $result = null;
        
        $request = $this->getRequest();
        $form = new \Application\Form\FilterForm();
        $order = new \Application\Form\OrderForm();
        if ($this->session->role == 3)
        {
            $id = $this->getPhysicianTable()->getPhysicianUid($this->session->id)->id;
            $result = $this->getRegistrationTable()->getRegistrationPhysician($id,date('Y-m-d'));
        }else {
            $result = $this->getRegistrationTable()->listRegistration();
        }
        if($request->isPost())
        {
            /**
             * Ustawiam w sesji dane potrzebne do wydruku
             */
            $this->session->pdfPatient      = $request->getPost('patient');
            $this->session->pdfPhysician    = $request->getPost('physician');
            $this->session->pdfDate         = $request->getPost('date');
            
            /**
             * Ustawiamy domyślne wartości pól
             */
            $form->get('patient')->setValue($request->getPost('patient'));
            $form->get('physician')->setValue($request->getPost('physician'));
            $form->get('date')->setValue($request->getPost('date'));
            $order->get('order')->setValue($request->getPost('order'));
            if($this->session->role == 3)
            {
                $physicianId = $this->session->id;
            }else {
                $physicianId = $request->getPost('physician');
            }
            $result = $this->getRegistrationTable()->filter(
                    $request->getPost('patient'),
                    $physicianId,
                    $request->getPost('date'),
                    $request->getPost('order')
                    );
            
        }
        
       
            return new ViewModel(array(
               'result' => $result, 
               'form'   => $form,
                'order' => $order
            ));
        }
        

}
