<?php
namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Physician  implements InputFilterAwareInterface {
    public $id;
    public $user_id;
    public $npwz;

    
    protected $inputFilter;
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
    
    public function exchangeArray($data) {
        $this->id               = (isset($data['id']))          ? $data['id']           : null;
        $this->user_id          = (isset($data['user_id']))     ? $data['user_id']      : null;
        $this->npwz             = (isset($data['npwz']))        ? $data['npwz']         : null;
        
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
                 'name'     => 'npwz',
                 'required' => true,
                 
                'validators' => array(
                     array(
                      'name' =>'NotEmpty', 
                      'break_chain_on_failure' => true,
                      'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać NPWZ' 
                            ),
                        ),
                    ),
                    array(
                        'name'    => 'StringLength',
                        'break_chain_on_failure' => true,
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 7,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'nieprawidłowy numer NPWZ', 
                                
                            ),
                         ),
                     ),
                    array(
                        'name'  =>  'Digits',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'message' => array(
                            \Zend\Validator\Digits::NOT_DIGITS => 'Tylko cyfry',                      
                            )
                        )
                    ),
                    array(
                        'name'  =>  'Zend\Validator\Db\NoRecordExists',
                        'break_chain_on_failure' => true,
                        'options' =>array(
                            'table' =>  'physician',
                            'field' =>  'npwz',
                            'adapter' => new \Zend\Db\Adapter\Adapter($this->configArray),
                            'message' => array(
                            \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Podany NPWZ już istnieje!',
                            )
                        ),
                        
                    ),
                )
             ));        
            
            
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    
}

