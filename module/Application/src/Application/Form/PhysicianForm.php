<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class PhysicianForm extends Form {
    
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
        
        $this->setAttributes(array(
            'action' => '/uzytkownik/physician',
            'method' => 'post',
            'name'  => 'form-login'
        ));
        $this->add(array(
                'type'      =>  'hidden',
                'name'      =>  'user_id',    
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'npwz',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Wpisz numer NPWZ lekarza'
                )
        ));
        
        $this->add(array(
                'type'      =>  '\Zend\Form\Element\File',
                'name'      =>  'file',
                'attributes'    =>  array(
                    'class' =>  'form-control',
                    
                )
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'specialization',
                'require'   =>  'false',
            
               'options'    => array (
                    'label'     => 'Wybór pacjent',
                    'empty_option'  => '--- Wybierz specjalizację ---',
                    'value_options' => $this->getSpecialization(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPatient',
                   'class'  =>  'form-control',
               )
        ));
        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Zapisz',
                 'id' => 'submitbutton',
             ),
         ));
    }
    
    private function getSpecialization() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name FROM specialization ORDER BY name');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
}
