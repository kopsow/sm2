<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class PatientController extends AbstractActionController
{
    private $registrationTable;
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
        return new ViewModel();
    }
    
    public function VisitAction()
    {
        if($this->session->role == 2)
        {
            $result = $this->getRegistration($this->session->id);
        } else {
            
            $result = $this->getAllRegistration();
        }
        
        
        return new ViewModel(array(
           'registrations' => $result,
            'session' =>$this->session
        ));
    }
    
    public function getRegistration($id)
    {
        $dbAdapter = new \Zend\Db\Adapter\Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT registration.id,patient_id,users.name,users.surname,visit_date,registration_date,end_date FROM registration LEFT JOIN users ON users.id = (SELECT user_id FROM physician WHERE id=registration.physician_id) WHERE patient_id=(SELECT id FROM patient where user_id='.$id.')');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            $selectData2=array();
            $selectData2['id'] =   $res['id'];
            $selectData2['patient_id'] =   $res['patient_id'];
            $selectData2['name'] =   $res['name'];
            $selectData2['surname'] =   $res['surname'];
            $selectData2['visit_date'] =   $res['visit_date'];
            $selectData2['registration_date'] =   $res['registration_date'];
            $selectData[]=$selectData2;
        }
       
        return $selectData;
    }
    public function getAllRegistration()
    {
        $dbAdapter = new \Zend\Db\Adapter\Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT registration.id,patient_id,users.name,users.surname,visit_date,registration_date,end_date FROM registration LEFT JOIN users ON users.id = (SELECT user_id FROM physician WHERE id=registration.physician_id)');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            $selectData2=array();
            $selectData2['id'] =   $res['id'];
            $selectData2['patient_id'] =   $res['patient_id'];
            $selectData2['name'] =   $res['name'];
            $selectData2['surname'] =   $res['surname'];
            $selectData2['visit_date'] =   $res['visit_date'];
            $selectData2['registration_date'] =   $res['registration_date'];
            $selectData[]=$selectData2;
        }
       
        return $selectData;
    }
}
