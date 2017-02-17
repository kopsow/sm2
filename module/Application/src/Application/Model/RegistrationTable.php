<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class RegistrationTable {
    
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()    {
        
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getRegistration($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
   
    
    public function getRegistrationScheduler($id)
    {
          $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('scheduler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveRegistration(Registration $registration)
    {
        
            $data = array(            
                'patient_id'           => $registration->patient_id,
                'physician_id'         => $registration->physician_id,
                'visit_date'           => $registration->visit_date,
                'registration_date'    => $registration->tel,
                );

        $id = (int)$registration->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRegistration($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addRegistration(Registration $registration)
    {
        
        $data = array(            
                'patient_id'           => $registration->patient_id,
                'physician_id'         => $registration->physician_id,
                'visit_date'           => $registration->visit_date,
                'registration_date'    => $registration->tel,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deleteRegistration($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    public function lastInsertId() {
        return $this->tableGateway->lastInsertValue;
    }
    
    
}

