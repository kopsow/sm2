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


use Zend\Captcha;
use Zend\Captcha\Figlet;


class TestController extends AbstractActionController
{
    private $patientTable;
    private $usersTable;
    private $registrationTable;
    
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
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
    }
    public function indexAction()
    {
        $dataUser = $this->getUsersTable()->getUsersEmail('kopsow@gmail.com');
        echo md5($dataUser->password+$dataUser->login);
        
        return new ViewModel(array(
          
        ));
        
       /*$captcha = new \Zend\Captcha\Figlet(array(
    'name' => 'foo',
    'wordLen' => 6,
    'timeout' => 300,
        ));
       $id = $captcha->generate();
       echo '<pre>';
       echo $captcha->getFiglet()->render($captcha->getWord());
     echo '</pre>';*/
    }
}
