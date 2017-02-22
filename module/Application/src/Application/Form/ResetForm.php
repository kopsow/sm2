<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ResetForm extends Form {
    
    protected $inputFilter;
    
    public function __construct() {
        parent::__construct('login');       
        
       $this->setAttributes(array(
            'action' => '/auth/reset',
            'method' => 'post',
            'name'  => 'form-login'
        ));
                
        
        
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj nowe hasło'
                )
        ));
        
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password-repeat',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz ponownie nowe hasło'
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
    
    public function getInputFilter()
     {
         if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
           
            $inputFilter->add(array(
                 'name'     => 'password',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać hasło',
                                
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                         'options' => array (
                             'min' => '5',
                             'messages' => array(
                                 'stringLengthTooShort' => 'Hasło musi się składać z min 5 znaków'
                             )
                         )
                    ),
                     
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'password-repeat',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wpisać ponownie hasło' 
                            ),
                        ),
                    ),
                    array(
                      'name' =>'Identical', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'token' => 'password',
                            'message' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'Hasła nie są takie same'
                            )
                        ),
                    ),
                    
                     
                    ),
             ));
            
            
           
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    

}