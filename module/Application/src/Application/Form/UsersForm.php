<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class UsersForm extends Form {
    
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
    
    public function __construct() {
        parent::__construct('physician');       
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'name',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj imię'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'surname',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj nazwisko'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'login',
               
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj login'
                )
        ));       
       
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'email',
                
                'attributes'=>  array(
                    'id'            =>  'email',
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj adres email'
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
                'type'      =>  'password',
                'name'      =>  'password-repeat',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Powtórz hasło'
                )
        ));
        
               
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'role',
               'options'    => array (   
                    'value_options' => $this->getRole(), 
                    'empty_option'  => '--- Wybierz role ---',
                    'disable_inarray_validator' => true,
                                       
               ),
               'attributes' =>  array (
                   'id'     =>  'role',
                   'class'  =>  'form-control',
                   
               )
        ));
        
        $this->add(array(
            'type'  =>  'checkbox',
            'name'  =>  'verified',
            'attributes' => array(
                'label' => 'Zweryfikowany'
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
    
    public function getRole()
    {
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name FROM role');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
}