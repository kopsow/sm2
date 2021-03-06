<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class PhysicianTable {
    
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
    
    public function getPhysician($id)
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
     * Zwraca id na podstawie user_id
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function getPhysicianUid($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('user_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
   public function getPhysicianList()
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('users', 'physician.user_id = users.id',array('name','surname'),'inner');
        $statmentSql->join('specialization', 'physician.specialization = specialization.id',array('name_specialization'),'left');
        
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }
    
    public function getPhysicianScheduler($id)
    {
          $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('scheduler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function loginPhysician($email,$password)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where('email="'.$email.'"')->where('password="'.$password.'"');
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset->current();
    }
    
    /**
     * Dodajemy zdjęcia, opisy i specjalizację lekarzy
     * @param type $data
     */
    public function savePhysicianDesc($date_desc)
    {
        $id = $this->getPhysicianUid($date_desc['physician'])->id;
        $data = array(
            'specialization' => $date_desc['specialization'],
            'description'    => $date_desc['description'],
            'avatar'         => $date_desc['image']
        );
        $this->tableGateway->update($data, array('id' => $id));
    }
    
    public function savePhysician(Physician $physician)
    {
        
            $data = array(            
            'user_id'           => $physician->user_id,
            'npwz'             => $physician->npwz,
            
                );

        $id = (int)$physician->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPhysician($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addPhysician(Physician $physician)
    {
        
        $data = array(            
           'user_id'           => $physician->user_id,
           'npwz'             => $physician->npwz,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deletePhysician($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    public function lastInsertId() {
        return $this->tableGateway->lastInsertValue;
    }
    
    public function verifiedPhysician($id)
    {
        $data = array(
            'verified'  =>  '1'
        );
        $this->tableGateway->update($data, array('id' => $id));
    }
}

