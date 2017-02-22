<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;


class SchedulerListTable 
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
   
    /**
     * Pobiera listę harmongramów dla wskazanego lekarza
     * @param type $id
     */
    public function getSchedulerPhysicianName($name)
    {

        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $name");
        }
        return $rowset;
    }
    public function getScheduler($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function deleteScheduler($id)
    {
         $this->tableGateway->delete(array('id' => $id));
    }

   
}