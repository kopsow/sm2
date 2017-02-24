<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class UploadForm extends Form
{
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
        parent::__construct('patient');


        
        $this->setAttributes(array(
            'action'    => '/uzytkownik/avatar',
            'method'    => 'post',
            'enctype'   => 'multipart/form-data',
            'name'      => 'form-file'
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'specialization',
               'options'    => array (
                    'label'     => 'Specjalizacja',
                    'empty_option'  => '--- Wybierz specjalizację ---',
                    'value_options' => $this->getSpecialization(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectSpecialization',
                   'class'  =>  'form-control',
               ),
                'validators' => array(
                     array(
                      'name' =>'NotEmpty', 
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać PESEL' 
                            ),
                        ),
                    ),
                )
                
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'physician',
               'options'    => array (
                    'label'     => 'Lekarz',
                    'empty_option'  => '--- Wybierz lekarza ---',
                    'value_options' => $this->getPhysician(),
               ),
               'attributes' =>  array (
                   'id'     =>  'selectPhysician',
                   'class'  =>  'form-control',
               ),
                'validators' => array(
                     array(
                      'name' =>'NotEmpty', 
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wybrać lekarza' 
                            ),
                        ),
                    ),
                )
                
        ));
        
        $this->add(array(
                'type'  =>  'Zend\Form\Element\File',
                'name'  =>  'image-file',
                'options'   =>  array(
                    'label' =>  'Wskaż zdjęcie lekarza',
                ),
                'attributes'    =>  array(
                    'class' =>  'form-control',
                    'id'    =>  'image-file'
                )
            ));
        
        $this->add(array(
                'type'  =>  'textarea',
                'name'  =>  'description',
                'options'   =>  array(
                    'label' =>  'Opis lekarza',
                ),
                'attributes'    =>  array(
                    'class'       =>  'form-control',
                    'placeholer'  =>  'Podaj krótki opis lekarza (min. 10 znaków)',
                    'cols'        =>  '50',
                    'rows'        =>  '5'
                ),
        ));
        
        $this->add(array(
                'type'  =>  'submit',
                'name'  =>  'submit',
                'attributes'    =>  array(
                    'class' =>  'btn btn-success',
                    'value' =>  'Zapisz'
                )
        ));
    }

  
    
     public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $fileInput = new InputFilter\FileInput('image-file');
        $fileInput->setRequired(true);
        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './public/img/avatar.png',
                'randomize' => true,
            )
        );
        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }
    
    private function getSpecialization() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name_specialization FROM specialization');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name_specialization'];
        }
       
        return $selectData;
    }
    
     private function getPhysician() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) as name_specialization FROM users WHERE role = 3');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name_specialization'];
        }
       
        return $selectData;
    }
    
    public function getInputFilter()
     {
         if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
            $inputFilter->add(array(
                 'name'     => 'specialization',
                 'required' => true,                 
                 'validators' => array(
                     array(
                      'name' =>'NotEmpty', 
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wybrać specjalizację' 
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
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wybrać lekarza' 
                            ),
                        ),
                    ),
                )
             ));        
            
            $inputFilter->add(array(
                 'name'     => 'description',
                 'required' => true,                 
                 'validators' => array(
                     array(
                      'name' =>'NotEmpty', 
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Opis jest wymagany' 
                            ),
                        ),
                    ),
                    array(
                         'name'    => 'StringLength',
                        'break_chain_on_failure' => true,
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 10,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'minimum 10 znaków', 
                                
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