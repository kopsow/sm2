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
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Form;
use Application\Model\Patient;
use Application\Model\PatientTable;
use Zend\Mvc\MvcEvent;
use Application\Model\Users;

class RegisterController extends AbstractActionController
{
    private $patientTable;
    private $usersTable;
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
        
        if (!$this->session->role)
        {
            $this->redirect()->toRoute('login');
        }
       if ($this->session->role == 2)
       {
             $this->layout('layout/patient');
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
       
        return new ViewModel(array(
            'form'  =>  $form,
            'patient'   => $this->getPatientTable()->fetchAll(),
        ));
    }
    
    public function patientListAction()
    {
        
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name,surname AS name FROM users WHERE role =3');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
        return new ViewModel(array(
            'form'  => $this->getPatientTable()->fet
        ));
    }
}
