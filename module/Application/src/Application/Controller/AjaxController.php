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
use Zend\View\Model\JsonModel;

use Application\Model\Users;

class AjaxController extends AbstractActionController
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
    
    public function emailCheckAction()
    {
        $request = $this->getRequest();
        $result = NULL;
        
        if ($request->isXmlHttpRequest()) {
            
            
            $result = $this->getUsersTable()->checkEmail($request->getPost('email'));
        }
        return new JsonModel(array(
            'msg' => $result,
        ));
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }
}
