<?php
namespace Application\Model;


class Registration  {
    public $id;
    public $patient_id;
    public $physician_id;
    public $visit_date;
    public $registration_date;
    public $end_date;

    public function exchangeArray($data) {
        $this->id                    = (isset($data['id']))                 ? $data['id']                : null;
        $this->patient_id            = (isset($data['patient_id']))         ? $data['patient_id']        : null;
        $this->physician_id          = (isset($data['physician_id']))       ? $data['physician_id']      : null;
        $this->visit_date            = (isset($data['visit_date']))         ? $data['visit_date']        : null;
        $this->registration_date     = (isset($data['registration_date']))  ? $data['registration_date'] : null;  
        $this->end_date              = (isset($data['end_date']))           ? $data['end_date']          : null;
        
        }
 
    
}

