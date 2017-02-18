<?php
namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Users  implements InputFilterAwareInterface {
    public $id;
    public $name;
    public $surname;
    public $login;
    public $email;
    public $password;
    public $role;
    public $verified;
    
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
                 'name'     => 'name',
                 'required' => true,                
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać imię' 
                            ),
                        ),
                    ),
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 3,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'Imię nie może być krótsze niż 3 znaki', 
                                
                            ),
                         ),
                     ),
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'surname',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać nazwisko' 
                            ),
                        ),
                    ),
                     array(
                         'name'    => 'StringLength',
                         'break_chain_on_failure' => true,
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 3,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'Nazwisko nie może być krótsze niż 3 znaki', 
                                
                            ),
                         ),
                     ),
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'login',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać nazwę użytkownika' 
                            ),
                        ),
                    ),
                     /*array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 5,
                             'messages' => array(                                
                                'stringLengthTooShort' => 'Login musi się składać z min. 5 znaków', 
                                
                            ),
                         ),
                     ),*/
                    array(
                        'name'  =>  'Zend\Validator\Db\NoRecordExists',
                        'options' =>array(
                            'table' =>  'users',
                            'field' =>  'login',
                            'adapter' => new \Zend\Db\Adapter\Adapter($this->configArray),
                            'message' => array(
                            \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Podany login jest już zajęty',
                            )
                        ),
                        
                    ),
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'email',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać adres email' 
                            ),
                        ),
                    ),
                    array(
                         'name'    => 'EmailAddress',
                         'options' => array (
                             'message' => array(
                                 \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Nieprawidłowy format adresu email'
                             ),
                         )
                    ),
                    array(
                        'name'  =>  'Zend\Validator\Db\NoRecordExists',
                        'options' =>array(
                            'table' =>  'users',
                            'field' =>  'email',
                            'adapter' => new \Zend\Db\Adapter\Adapter($this->configArray),
                            'message' => array(
                            \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Podany adres email jest już zajęty',
                            )
                        ),
                        
                    ),
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'password',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze podać hasło',
                                
                            ),
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                         'options' => array (
                             'min' => '5',
                             'messages' => array(
                                 'stringLengthTooShort' => 'Hasło musi się składać z min 5 znaków'
                             )
                         )
                    ),
                     
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'password-repeat',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze wpisać ponownie hasło' 
                            ),
                        ),
                    ),
                    array(
                      'name' =>'Identical', 
                        'options' => array(
                            'token' => 'password',
                            'message' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'Hasła nie są takie same'
                            )
                        ),
                    ),
                    
                     
                    ),
             ));
            
            $inputFilter->add(array(
                 'name'     => 'role',
                 'required' => true,                 
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Prosze określić rolę' 
                            ),
                        ),
                    ),
                ),
                
             ));
           
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    
}

