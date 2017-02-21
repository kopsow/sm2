<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\InputFilter\InputFilter;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;


class AuthoryzationController extends AbstractActionController
{
    
     private $usersTable;
     private $patientTable;
    
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
     private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
     
     
    public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if ($this->session->role)
        {
            $this->redirect()->toRoute('home');
        }
       
        return parent::onDispatch($e);
    }
    public function accessAction()
    {
        
    }
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function logoutAction()
    {
        $this->session->getManager()->getStorage()->clear('login');
        $this->redirect()->toRoute('login');
    }
    public function loginAction()
    {
        $request = $this->getRequest();
        $form = new \Application\Form\LoginForm();
        $message = null;
        
        if ($request->isPost())
        {
            
            $form->setData($request->getPost());
            $test = new InputFilter();
            $test->add(array(
                 'name'     => 'login',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać nazwę użytkownika' 
                            ),
                        ),
                    ),
                     /*array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 5,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'Login musi się składać z min. 5 znaków', 
                                
                            ),
                         ),
                     ),*/
                    array(
                        'name'  =>  'Zend\Validator\Db\RecordExists',
                        'options' =>array(
                            'table' =>  'users',
                            'field' =>  'login',
                            'adapter' => new \Zend\Db\Adapter\Adapter($this->configArray),
                            'message' => array(
                            \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Nie ma takiego loginu',
                            )
                        ),
                        
                    ),
                    ),
             ));
            
            $test->add(array(
                 'name'     => 'password',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać hasło',
                                
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                         'options' => array (
                             'min' => '5',
                             'messages' => array(
                                 'stringLengthTooShort' => 'Hasło musi się składać z min 5 znaków'
                             )
                         )
                    ),
                     
                    ),
             ));
            $form->setInputFilter($test);
            if ($form->isValid())
            {
                
                $login = $this->getUsersTable()->loginUsers($request->getPost('login'),$request->getPost('password'));
                if ($login)
                {
                   $this->session->name     = $login->name;
                   $this->session->email    = $login->email;
                   $this->session->id       = $login->id;
                   $this->session->surname  = $login->surname;
                   $this->session->login    = $login->login;
                   $this->session->role     = $login->role;
                   $this->redirect()->toRoute('home');

                    
                } else {
                    $message = 'Błędne hasło';
                }
              
            } else {
               
            }
        }
        return new ViewModel(array(
            'form'      =>  $form,
            'message'   =>  $message
        ));
    }
    public function rememberAction()
    {
        $form = new \Application\Form\RememberForm();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            
        }
        
        return new ViewModel(array(
            'form'  => $form
        ));
    }
    public function addAction()
    {
        $formUser          = new \Application\Form\UsersForm();
        $formPatient   = new \Application\Form\PatientForm();
        $users         = new \Application\Model\Users;
        $patient       = new \Application\Model\Patient;
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $formUser->setData($request->getPost());
            $formPatient->setData(($request->getPost()));
            
            $formUser->setInputFilter($users->getInputFilter());
            $formUser->getInputFilter()->get('role')->setRequired(false);
            $formUser->getInputFilter()->get('verified')->setRequired(false);
            $formPatient->setInputFilter($patient->getInputFilter());
            
           
            $formPatient->isValid();
            $formUser->isValid();
            if($formUser->isValid() && $formPatient->isValid())
            {
                $users->exchangeArray($formUser->getData());
                $users->role=2;      
                $users->verified=0;
                $this->getUsersTable()->saveUsers($users);
                
                $patient->exchangeArray($formPatient->getData());
                $patient->user_id = $this->getUsersTable()->lastInsertId();
                $this->getPatientTable()->savePatient($patient);
                $email = $users->email;
                 $body = 'Witaj! <br/>'
                . 'Potwierdzamy utworzenie konta w serwiei SUPER-MED.pl <br/>'
                . 'Aby móc się zalogować konieczne jest aktywowanie swojego konta za pomocą'
                . 'poniższego adres: </br>'
                . '<a href="http://www.super-med.pl/auth/active/'.$email.'">Link aktywacyjny</a>';
                $this->sendMail($users->email, 'Rejestracja w serwisie', $body);
                $this->redirect()->toRoute('login');
            }
         
        }
        
        return new ViewModel(array(
            'form'          =>  $formUser,
            'formPatient'   =>  $formPatient
        ));
    }
    public function activeAction()
    {
        $this->getUsersTable()->verifiedUsers($this->params()->fromRoute('email'));
        $this->redirect()->toRoute('login');
    }
    public function sendMail($to,$subject,$bodyInput)
    {
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
}
