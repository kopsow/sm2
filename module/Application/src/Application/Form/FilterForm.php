<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Db\Adapter\Adapter;


class FilterForm extends Form {
    protected $inputFilter;
    
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
   public function __construct() {
        parent::__construct('login');       
        
       $this->add(array(
                'type'  =>  'text',
                'name'  =>  'login',
                'options'   =>  array(
                    'label' =>  'Login',
                ),
                'attributes'    =>  array(
                    'id'            =>  'loginText',
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz nazwę użytkownika'
                )
       ));
       
       $this->add(array(
                'type'  =>  'text',
                'name'  =>  'name',
                'options'   =>  array(
                    'label' =>  'Imię',
                ),
                'attributes'    =>  array(
                    'id'            =>  'loginText',
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz imię'
                )
       ));
       
       $this->add(array(
                'type'  =>  'text',
                'name'  =>  'surname',
                'options'   =>  array(
                    'label' =>  'Nazwisko',
                ),
                'attributes'    =>  array(
                    'id'            =>  'loginText',
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz nazwisko'
                )
       ));
       
       
       $this->add(array(
                'type'  =>  'checkbox',
                'name'  =>  'verified',
                'options'   =>  array(
                    'label' =>  'Aktywny'
                ),
                'attributes'    =>  array(
                    'id'    =>  'verifiedCheckbox',
                    'class' =>  'checkbox',
                ),
       ));
       
       $this->add(array(
                'type'  =>  'email',
                'name'  =>  'email',
                'options'   =>  array(
                    'label' =>  'Email',
                ),
                'attributes'    =>  array(
                    'id'            =>  'loginText',
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz adres email'
                )
       ));
       
       $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'physician',
               'options'    => array (
                    'label'     => 'Wybór lekarza',
                    'empty_option'  => '--- Wybierz lekarza ---',
                    'value_options' => $this->getPhysician(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPhysician',
                   'class'  =>  'form-control',
                   'placeholder'    =>  'Wpisz nazwę użytkownika'
               )
        ));
       
       $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'patient',
               
               'options'    => array (
                    'label'     => 'Wybór pacjent',
                    'empty_option'  => '--- Wybierz pacjenta ---',
                    'value_options' => $this->getPatient(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPatient',
                   'class'  =>  'form-control',
               )
        ));
       
       $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'role',
               'options'    => array (
                    'label'     => 'Typ konta',
                    'empty_option'  => '--- Wybierz role ---',
                    'value_options' => $this->getRole(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPatient',
                   'class'  =>  'form-control',
               )
        ));
       $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'sort',
               'options'    => array (
                    'label'     => 'Sortowanie',
                    'empty_option'  => '--- Wybierz kolumnę ---',
                    'value_options' => array(
                        'name'      =>  'Imię',
                        'surname'   =>  'Nazwisko',
                        'login'     =>  'Login',
                        'email'     =>  'Email',
                        'role'      =>  'Typ konta',
                        'verified'  =>  'Aktywny'
                    )
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPatient',
                   'class'  =>  'form-control',
               )
        )); 
       
        $this->add(array(
               'type'       =>  'date',
               'name'       =>  'date',
               'options'    => array (
                    'label'     => 'Data wizyty',
                  
               ),
               'attributes' =>  array (
                   'id'     =>  'dateVisit',
                   'class'  =>  'form-control',

               )
        ));
        
        $this->add(array(
                'type'       =>   'submit',
                'name'       =>   'submit',
                'attributes' =>     array(
                    'value' =>  'Filtruj',
                    'class' =>  'btn btn-success ',
                )
        ));
        
        
       
    }
     private function getPhysician() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) AS name FROM users WHERE role = 3');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
    
    private function getPatient() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT users.id,CONCAT(name," ",surname," ",birthday) AS name FROM users RIGHT JOIN patient ON users.id = patient.user_id WHERE role = 2');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
    
    private function getRole() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name FROM role');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
    
    public function getInputFilter()
     {
         if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
           
            $inputFilter->add(array(
                'name'     => 'patient',
                'required' => true,
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Proszę wybrać pacjenta' 
                            ),
                        ),
                    ),
                )                
                
             ));
            $inputFilter->add(array(
                'name'     => 'physician',
                'required' => true,
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Proszę wybrać lekarza' 
                            ),
                        ),
                    ),
                )
             ));
           
           
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
}