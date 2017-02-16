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
        if ($user->password)
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
        } else {
            $data = array(            
            'name'           => $user->name,
            'surname'        => $user->surname,
            'login'          => $user->login,
            'email'          => $user->email,            
            'role'           => $user->role,
            'verified'       => $user->verified,
                );
        }
            

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
    
    /**
     * Sprawdza czy podany email istnieje w bazie. Funkcja dla ajxa
     * @param type $email
     * @return type
     */
    public function checkEmail($email)
    {
       
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->count();
        
        return $row;
    }
    
    /**
     * Funkcja blokuje (ustawia verified na false) użytkownika
     * @param type $id
     */
    public function blockUsers($id)
    {
         $data = array(
            'verified'  =>  false
             );
        $this->tableGateway->update($data, array('id' => $id));
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
    
    /**
     * Zwraca id ostatniego dodanego rekordu
     * @return type
     */
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
    /**
     * Zwraca wiersz apsujacy do pary login password
     * @param type $login
     * @param type $password
     * @return type
     * @throws \Exception
     */
    public function loginUsers($login,$password)
    {
        $rowset = $this->tableGateway->select(array('login' => $login,'password'=>$password,'verified'=>true));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $login");
        }
        return $row;
    }
       
   
}

