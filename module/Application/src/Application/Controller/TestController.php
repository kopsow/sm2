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
       
        var_dump($this->chceckLimit(40));
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
    
    private function chceckLimit($patientId)
    {
        $id =  $this->getPatientTable()->getPatientUid($patientId)->id;
        $result = $this->getRegistrationTable()->getRegistrationUser($id)->current();
        $date_current = date('Y-m-d');
   
        $date_visit = date(('Y-m-d'),  strtotime($result['visit_date']));

        
        
        //Czy data nowej rezerwacji jest równa dacie wizyty
        if($date_current == $date_visit)
        {
            echo 'rezerwacja w dzień wizyty';
        }elseif ($date_current> $date_visit)
        {
            echo 'rezerwacja po odbytej wizycie';
        }else {
            echo 'Rezerwacja nie jest możłiwa';
        }
    }
}
