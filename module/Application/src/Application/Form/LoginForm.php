<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class LoginForm extends Form {
    
   public function __construct() {
        parent::__construct('login');       
        
       $this->setAttributes(array(
            'action' => '/auth',
            'method' => 'post',
            'name'  => 'form-login'
        ));
                
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'login', 
                'required' => true,
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj nazwę użytkowika'
                ),
                'validators' =>array(
                    array(
                        'name'  => 'StringLength',
                        'min'   => '5'
                    )
                )
        ));
        
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj hasło'
                )
        ));
        
        $this->add(array(
            'type'  =>  'submit',
            'name'  =>  'submit',
            'attributes' => array(
                 'value'    =>  'Wyślij'
             )
         ));
        
       
    }
    

}