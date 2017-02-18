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

class IndexController extends AbstractActionController
{
    
    public function __construct() {
        $this->session = new \Zend\Session\Container('login');
    }
    public function indexAction()
    {
        
       if ($this->session->role == 2)
       {
           $this->layout('layout/patient');
           $this->layout()->setVariable('scheduler_active', 'active');
       }
       if($this->session->role == 1)
       {
            $this->layout('layout/admin');
       }
       
        return new ViewModel();
    }
}
