<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UsersTable {
    
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()    {
        
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUsers($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }    
    
  
    public function saveUsers(Users $user)
    {
        
            $data = array(            
            'name'           => $user->name,
            'surname'        => $user->surname,
            'login'          => $user->login,
            'email'          => $user->email,
            'password'       => $user->password,
            'role'           => $user->role,
            'verified'       => $user->verified,
                );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsers($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    
    public function checkEmail($email)
    {
       
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->count();
        
        return $row;
    }
    public function addUsers(Users $user)
    {
     
        $data = array(            
            'name'           => $user->name,
            'surname'        => $user->surname,
            'login'          => $user->login,
            'email'          => $user->email,
            'password'       => $user->password,
            'role'           => $user->role,
            'verified'       => $user->verified,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deleteUsers($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    public function lastInsertId() {
        return $this->tableGateway->lastInsertValue;
    }
    
    public function verifiedUsers($id)
    {
        $data = array(
            'verified'  =>  '1'
        );
        $this->tableGateway->update($data, array('id' => $id));
    }
    
    public function loginUsers($login,$password)
    {
        $rowset = $this->tableGateway->select(array('login' => $login,'password'=>$password));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $login");
        }
        return $row;
    }
}

