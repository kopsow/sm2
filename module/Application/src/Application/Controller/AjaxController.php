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
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
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
    
    public function listNameAction()
    {
        $request = $this->getRequest();
        //$search = $request->getPost('name');
        $search = 'Woj';
    
        $dbAdapter = new \Zend\Db\Adapter\Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT * FROM users WHERE name like "'.$search.'%"');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
        return new JsonModel(array(
            'msg' => $selectData,
        ));
    }
    
    public function listSurnameAction()
    {
        $request = $this->getRequest();
        $search = $request->getPost('surname');
        $selectData = array();
        if ($search != null)
        {
            $dbAdapter = new \Zend\Db\Adapter\Adapter($this->configArray);
            $statement = $dbAdapter->query('SELECT * FROM users WHERE surname like "'.$search.'%"');
            $result = $statement->execute();
            
        
            foreach ($result as $res) {
  
            $selectData[] = array(
                'id'=>$res['id'],
                'email'=>$res['email'],
                'login' => $res['login']);
                
                
            }
        }
        
    
        
        
        
        return new JsonModel(array(
            json_encode($selectData)
        ));
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }
}
