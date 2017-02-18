<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class OrderForm extends Form {
 
   public function __construct() {
        parent::__construct('user');  
        
       
       
     
        
         $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'order',
               'options'    => array (
                    'label'     => 'Sorotwanie kolumn',
                    'empty_option'  => '--- Posortuj ---',
                    'value_options' => array(
                       'patient'=>'Pacjent',
                       'physician'=>'Lekarz',
                       'visit_date'=>'Data wizyty'
                    ),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPhysician',
                   'class'  =>  'form-control',
               )
        ));
        
        $this->add(array(
                'type'       =>   'submit',
                'name'       =>   'submit',
                'attributes' =>     array(
                    'value' =>  'Sortuj',
                    'class' =>  'btn btn-success'
                )
        ));
       
    }
   

}