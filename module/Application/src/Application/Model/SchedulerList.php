<?php
namespace Application\Model;


class SchedulerList  
{
    public $id;
    public $name;
    public $date_start;
    public $date_end;    
    
    protected $inputFilter;
    
    public function exchangeArray($data) {
        $this->id               = (isset($data['id']))              ? $data['id']           : null;
        $this->name             = (isset($data['name']))            ? $data['name']         : null;
        $this->date_start       = (isset($data['date_end']))        ? $data['date_start']   : null;
        $this->date_end         = (isset($data['date_end']))        ? $data['date_end']     : null;
        }
    
  
    
}

