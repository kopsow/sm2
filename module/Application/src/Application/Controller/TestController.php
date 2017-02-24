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
    private $physicianTable;
    
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
       
       
        return new ViewModel(array(
          
        ));
     
    }
    
    public function fileAction()
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
                \Zend\Debug\Debug::dump($data);
               $this->getPhysicianTable()->savePhysicianDesc($data);
               move_uploaded_file($post['image-file']['tmp_name'], './public/img/physician/'.$post['image-file']['name']);
            }else {
                \Zend\Debug\Debug::dump($post);
            }

        }
        
        
        return new ViewModel(array(
            'form'  => $form
        ));
    }
    
}
