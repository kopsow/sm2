<?php
namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Patient  implements InputFilterAwareInterface {
    public $id;
    public $user_id;
    public $pesel;
    public $birthday;
    public $tel;
    
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
        $this->pesel            = (isset($data['pesel']))       ? $data['pesel']        : null;
        $this->birthday         = (isset($data['birthday']))    ? $data['birthday']     : null;
        $this->tel              = (isset($data['tel']))         ? $data['tel']          : null;    
        
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
                 'name'     => 'pesel',
                 'required' => true,
                 
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
                    array(
                         'name'    => 'StringLength',
                        'break_chain_on_failure' => true,
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 11,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'nieprawidłowy numer PESEL', 
                                
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
                        'options' =>array(
                            'table' =>  'patient',
                            'field' =>  'pesel',
                            'adapter' => new \Zend\Db\Adapter\Adapter($this->configArray),
                            'message' => array(
                            \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Podany PESEL już istnieje!',
                            )
                        ),
                        
                    ),
                )
             ));        
            
            $inputFilter->add(array(
                'name'      =>  'birthday',
                'required'  =>  false
            ));
            
            $inputFilter->add(array(
               'name'      =>  'tel',
               'required' => true,              
               'validators' => array(
                   array(
                      'name' =>'NotEmpty', 
                         'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wpisać numer telefonu' 
                            ),
                        ),
                    ),
                   array(
                        'name'  =>  'Digits',
                        'options' => array(
                            'message' => array(
                            \Zend\Validator\Digits::NOT_DIGITS => 'Tylko cyfry',                      
                            )
                        )
                    ),
                   array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 9,
                             'max'      => 9,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'Za krótki numer', 
                                \Zend\Validator\StringLength::TOO_LONG => 'Numer za długi'
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

