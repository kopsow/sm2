<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class RegistrationTable {
    
    protected $tableGateway;
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
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
   
    /**
     * Zwraca listę rejestracji dla podanego lekarza
     * @param type $id
     */
    public function getRegistrationPhysician($id)
    {
       $sql = new \Zend\Db\Sql\Sql(new \Zend\Db\Adapter\Adapter($this->configArray));
        $select = $sql->select();
        $select->columns(array(
            'id',
            'visit_date',
            'patient' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as patient FROM users where id=(SELECT user_id FROM patient WHERE id=registration.patient_id))'),
            'physician' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as physician FROM users where id=(SELECT user_id FROM physician WHERE id=registration.physician_id))')));
        $select->where(new \Zend\Db\Sql\Predicate\Expression('physician_id = ?', $id));
        $select->from('registration');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }
    
    /**
     * Na podstawie id rejestracji zwraca dane pacjent
     * @param int $id
     */
    public function getRegistrationUser($id)
    {
        $sql = new \Zend\Db\Sql\Sql(new \Zend\Db\Adapter\Adapter($this->configArray));
        $select = $sql->select();
        $select->columns(array(
            'id',
            'visit_date',
            'name' => new \Zend\Db\Sql\Expression('(SELECT name FROM users where id=(SELECT user_id FROM patient WHERE id=registration.patient_id))'),
            'email' => new \Zend\Db\Sql\Expression('(SELECT email FROM users where id=(SELECT user_id FROM patient WHERE id=registration.patient_id))'),
            'physician' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) FROM users where id=(SELECT user_id FROM physician WHERE id=registration.physician_id))'),
            )
                );
        $select->where(new \Zend\Db\Sql\Predicate\Expression('id = ?', $id)); 
        $select->from('registration');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
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
    
    public function busyHours($physician_id,$visit_date)
    {
         $select = $this->tableGateway->getSql()->select();
         $select->columns(array('visit_date'=>new \Zend\Db\Sql\Expression('TIME(visit_date)')));
         $select->where('physician_id='.$physician_id)->where('DATE(visit_date)="'.$visit_date.'"');
         $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset;
    }   
    
    public function saveRegistration(Registration $registration)
    {
        
            $data = array(            
                'patient_id'           => $registration->patient_id,
                'physician_id'         => $registration->physician_id,
                'visit_date'           => $registration->visit_date,
                'registration_date'    => $registration->registration_date,
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
                'registration_date'    => $registration->registration_date,
            
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
    
    public function myRegistration()
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('users', 'users_id = (SELECT user_id FROM physician WHERE id=registration.physician_id)',array('name','surname'),'join');
        
        
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }
    
    public function listRegistration()
    {
        $sql = new \Zend\Db\Sql\Sql(new \Zend\Db\Adapter\Adapter($this->configArray));
        $select = $sql->select();
        $select->columns(array(
            'id',
            'visit_date',
            'patient' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as patient FROM users where id=(SELECT user_id FROM patient WHERE id=registration.patient_id))'),
            'physician' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as physician FROM users where id=(SELECT user_id FROM physician WHERE id=registration.physician_id))')));
         
        $select->from('registration');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }
    public function filter($patient,$physician,$visit_date,$order)
    {

        
        $sql = new \Zend\Db\Sql\Sql(new \Zend\Db\Adapter\Adapter($this->configArray));
        $select = $sql->select();
        $select->columns(array(
            'id',
            'visit_date',
            'patient' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as patient FROM users where id=(SELECT user_id FROM patient WHERE id=registration.patient_id))'),
            'physician' => new \Zend\Db\Sql\Expression('(SELECT CONCAT(name," ",surname) as physician FROM users where id=(SELECT user_id FROM physician WHERE id=registration.physician_id))'),
            
            ));
        
        if($patient)
        {
            $select->where(new \Zend\Db\Sql\Predicate\Expression('patient_id = ?', new \Zend\Db\Sql\Expression('(SELECT id FROM patient WHERE user_id='.$patient.')'))); 
        }
        if ($physician)
        {
           $select->where(new \Zend\Db\Sql\Predicate\Expression('physician_id = ?', new \Zend\Db\Sql\Expression('(SELECT id FROM physician WHERE user_id='.$physician.')'))); 
        }
        if ($visit_date)
        {
            $select->where(new \Zend\Db\Sql\Predicate\Expression('DATE(visit_date) = ?', $visit_date)); 
        }
        if ($order)
        {
            $select->order($order);
        }
       
        
        $select->from('registration');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
               
    }
   
    
    
}

