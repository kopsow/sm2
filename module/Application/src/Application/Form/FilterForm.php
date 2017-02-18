<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class FilterForm extends Form {
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
               )
        ));
       
       $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'patient',
               'options'    => array (
                    'label'     => 'Wybór pacjent',
                    'empty_option'  => '--- Wybierz pacienta ---',
                    'value_options' => $this->getPatient(),
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
                   'value'  =>  date('Y-m-d')
               )
        ));
        
        $this->add(array(
                'type'       =>   'submit',
                'name'       =>   'submit',
                'attributes' =>     array(
                    'value' =>  'Filtruj',
                    'class' =>  'btn btn-success'
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
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) AS name FROM users WHERE role = 2');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }

}