<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\InputFilter\InputFilter;
class AuthoryzationController extends AbstractActionController
{
    
     private $usersTable;
    
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
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
                   $this->session->surname  = $login->surname;
                   $this->session->login    = $login->login;
                   $this->session->role     = $login->role;
                   $this->redirect()->toRoute('home');
                } else {
                    $this->redirect()->toRoute('login');
                }
              
            } else {
               
            }
        }
        return new ViewModel(array(
            'form'  => $form,
        ));
    }
}
