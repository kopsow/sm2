<?php
namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Test  implements InputFilterAwareInterface {
   
    
    protected $inputFilter;
    
  
    
    public function exchangeArray($data) {
        $this->id               = (isset($data['id']))          ? $data['id']           : null;
        $this->name             = (isset($data['name']))        ? $data['name']         : null;      
        $this->surname          = (isset($data['surname']))     ? $data['surname']      : null;
        $this->login            = (isset($data['login']))       ? $data['login']        : null;
        $this->email            = (isset($data['email']))       ? $data['email']        : null;
        $this->password         = (isset($data['password']))    ? $data['password']     : null;
        $this->role             = (isset($data['role']))        ? $data['role']         : null;
        $this->verified         = (isset($data['verified']))    ? $data['verified']     : null;
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
     {
         throw new \Exception("Not used");
     }

    public function getInputFilter()
     {
         if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
            $inputFilter->add(array(
                 'name'     => 'login',
                 'required' => true,                
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                        'break_chain_on_failure' => false,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać imię' 
                            ),
                        ),
                    ),
                    array(
                      'name' =>'Pesel',
                       
                        'options' => array(
                            
                        ),
                    ),
                    ),
             ));
          
           
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    
}

