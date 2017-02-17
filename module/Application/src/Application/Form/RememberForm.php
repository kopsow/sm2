<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class RememberForm extends Form {
    
   public function __construct() {
        parent::__construct('login');       
        
       $this->setAttributes(array(
            'action' => '/auth/remember',
            'method' => 'post',
            'name'  => 'form-login'
        ));
                
        
       
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'email', 
                'required' => true,
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj adres email'
                ),
                'validators' =>array(
                    array(
                        'name'  => 'StringLength',
                        'min'   => '5'
                    )
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