<?php
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Scheduler  implements InputFilterAwareInterface {
    public $id;
    public $user_id;
    public $pesel;
    public $birthday;
    public $tel;
    
    protected $inputFilter;
    
    public function exchangeArray($data) {
        $this->id               = (isset($data['id']))              ? $data['id']           : null;
        $this->physician_id     = (isset($data['physician_id']))    ? $data['physician_id'] : null;
        $this->date_start       = (isset($data['date_end']))        ? $data['date_start']   : null;
        $this->date_end         = (isset($data['date_end']))        ? $data['date_end']     : null;
        }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
     {
         throw new \Exception("Not used");
     }

    public function getInputFilter()
     {
         if (!$this->inputFilter) {
            
            
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
    
}

