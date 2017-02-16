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
use Zend\Mvc\MvcEvent;
use Application\Model\Users;
class UserController extends AbstractActionController
{
    private $usersTable;
    
    public function onDispatch(MvcEvent $e) {
        $this->session = new \Zend\Session\Container('login');
        
        if (!$this->session->role)
        {
            echo $this->session->role;
            $this->redirect()->toRoute('login');
        }
       
        return parent::onDispatch($e);
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
       
       
        return new ViewModel();
    }
    
    public function addAction()
    {
        $form = new \Application\Form\UsersForm();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $user = new \Application\Model\Users;
            
            $form->setData($request->getPost());
            $form->setInputFilter($user->getInputFilter());
            
            if ($form->isValid())
            {
               $user = new Users();
               $user->exchangeArray($form->getData());
               $this->getUsersTable()->addUsers($user);
               $this->redirect()->toRoute('user',array('action'=>'list'));
            }
        }
        return new ViewModel(array(
            'form'  =>  $form
        ));
    }
    
    public function listAction()
    {
        $users = $this->getUsersTable()->fetchAll();
        
        return new ViewModel(array(
            'users' =>  $users
        ));
    }
    
    
    public function editAction()
    {
        $request = $this->getRequest();
        
        $id = (int) $this->params()->fromRoute('id');
        $data = $this->getUsersTable()->getUsers($id);
        $form = new \Application\Form\UsersForm();
        $form->setData((array)$data);
        
        if($request->isPost())
        {
            $user = new Users();
            
            $user->exchangeArray($request->getPost());
            
            $this->getUsersTable()->saveUsers($user);
            $this->redirect()->toRoute('user',array('action'=>'list'));
        }
        
        return new ViewModel(array(
            'form'  =>  $form,
        ));
    }
    
    public function blockAction()
    {
        $this->getUsersTable()->blockUsers((int) $this->params()->fromRoute('id'));
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
    
    public function deleteAction()
    {
         $this->getUsersTable()->deleteUsers((int) $this->params()->fromRoute('id'));
        $this->redirect()->toRoute('user',array('action'=>'list'));
    }
}
