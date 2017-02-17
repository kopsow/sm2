<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class PatientForm extends Form {
    
    
    
    public function __construct() {
        parent::__construct('patient');     
        
        $this->setAttributes(array(
            'action' => '/uzytkownik/patient',
            'method' => 'post',
            'name'  => 'form-login'
        ));
        $this->add(array(
                'type'      =>  'hidden',
                'name'      =>  'user_id',
        )); 
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'pesel',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj pesel'
                )
        ));
        
        $this->add(array(
                'type'      =>  'date',
                'name'      =>  'birthday',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj datę urodzenia'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'tel',
               
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj telefon'
                )
        ));       
        
        $this->add(array(
            'type'  =>  'submit',
            'name'  =>  'submit',
            'attributes' => array(
                 'value'    =>  'Zapisz'
             )
         ));
       
        
        
    }
    
    
}