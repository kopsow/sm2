<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class TestForm extends Form {
    
   public function __construct() {
        parent::__construct('login');       
        
      
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
                        'name'  => 'Pesel',
                    )
                )
        ));
        
       
        
        $this->add(array(
            'type'  =>  'submit',
            'name'  =>  'submit',
            'attributes' => array(
                 'class'    =>  'btn btn-success btn-block',
                 'value'    =>  'Wyślij'
             )
         ));
        
       
    }
    

}