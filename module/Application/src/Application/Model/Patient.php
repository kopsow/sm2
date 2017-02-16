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

           /* $inputFilter->add(array(
                 'name'     => 'id',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
             ));

            $inputFilter->add(array(
                 'name'     => 'user_id',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),                     
                 ),
             )); */          

             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    
}

