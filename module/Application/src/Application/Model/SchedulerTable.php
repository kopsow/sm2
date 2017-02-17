<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;


class SchedulerTable 
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
    
    public function showScheduler($order = null,$search = null)
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('users', 'scheduler.physician_id = users.id',array('name','surname'),'inner');
        if (!is_null($order)){
             $statmentSql->order($order.' ASC');
             
        } else {
            $statmentSql->order('date_start ASC');
        }
       
        if($search != 0) {
            $statmentSql->where('physician_id='.$search);
        }
        
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }

    
    public function listScheduler()
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('users', 'scheduler.physician_id = users.id',array('name','surname'),'inner');
        if (!is_null($order)){
             $statmentSql->order($order.' ASC');
             
        } else {
            $statmentSql->order('date_start ASC');
        }
       
        if($search != 0) {
            $statmentSql->where('physician_id='.$search);
        }
        
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
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

    public function saveScheduler(Scheduler $scheduler)
    {
        $data = array(            
            'physician_id'      => $scheduler->physician_id,
            'date_start'        => $scheduler->date_start,
            'date_end'          => $scheduler->date_end,
              
        );

        $id = (int)$scheduler->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getScheduler($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addScheduler(Scheduler $scheduler)
    {
        
        $data = array(            
            'physician_id'      => $scheduler->physician_id,
            'date_start'        => $scheduler->date_start,
            'date_end'          => $scheduler->date_end,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deleteScheduler($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function lastInsertId() {
        return $this->tableGateway->lastInsertValue;
    }
    
    public function checkDate($physician_id,$date_start) {
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('physician_id' => 4))->where(array('date_start'=>$date_start));
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset->count();
    }
    
    public function getSchedulerPhysician($id,$month)
    {
        
        $select = $this->tableGateway->getSql()->select();
        $select->where(array(
            'physician_id'  =>$id,))->where('MONTH(date_start)='.$month);
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset;
    }
    
    public function getSchedulerPhysicianHours($physician_id,$visit_date)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('date_start' => new \Zend\Db\Sql\Expression('TIME(date_start)'),'date_end' => new \Zend\Db\Sql\Expression('TIME(date_end)')));
        $select->where(array(
            'physician_id'  =>$physician_id,))->where('DATE(date_start)="'.$visit_date.'"');
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset->current();
    }
}