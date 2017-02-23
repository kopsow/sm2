<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UsersTable {
    
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
    
    /**
     * funkcja sprawdza czy odany email znjaduje się w bazie
     * @param type $email
     */
    public function checkUsersEmail($email)
    {
        $rowset = $this->tableGateway->select(array('email' => $email));
        
        return $rowset->count();
    }

    /**
     * Funkcja zwraca dane użytkownika na podstawie adresu email
     * @param type $email
     * @return type
     * @throws \Exception
     */
    public function getUsersEmail($email)
    {
        
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $email");
        }
        return $row;
    }
    /**
     * Pobiera użytkowników o określonej roli
     * @param type $role
     * @return type
     * @throws \Exception
     */
    public function getUsersRole($role){
        $id  = (int) $role;
        $rowset = $this->tableGateway->select(array('role' => $id));
        
       
        return $rowset;
    }
  
    public function saveSalt($email,$salt)
    {
        $data = array('salt'=>$salt);
        $this->tableGateway->update($data, array('email' => $email));
    }
    
    public function getUsersSalt($salt)
    {
        $rowset = $this->tableGateway->select(array('salt' => $salt,'id'));
        return $rowset->current();
    }
    
    public function changePasswordUsers($id,$data)
    {
        $this->tableGateway->update($data, array('id' => $id));
    }
    public function saveUsers(Users $user)
    {
        
        
        if ($user->password && $user->email)
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
        } 
        if ($user->email) {
            $data = array(            
            'name'           => $user->name,
            'surname'        => $user->surname,
            'login'          => $user->login, 
            'email'          => $user->email, 
            'role'           => $user->role,
            'verified'       => $user->verified,
                );
        }
        if($user->password && !$user->email) 
        {
            $data = array(            
            'name'           => $user->name,
            'surname'        => $user->surname,
            'login'          => $user->login, 
            'password'       => $user->password,
            'role'           => $user->role,
            'verified'       => $user->verified,
                );
        }
        else {
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
    
    /**
     * Aktywuje użytkownika na podstawie jego numeru @id
     * @param type $id
     */
    public function activeUsers($id)
    {
        $data = array(
            'verified'  =>  true
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
    
    
    /**
     * Weryfikuje użytkowników na podstawie linku aktywacyjnego
     * @param type $email
     */
    public function verifiedUsers($email)
    {
        $data = array(
            'verified'  =>  '1'
        );
        $this->tableGateway->update($data, array('email' => $email));
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
            return false;
        }
        return $row;
    }
    
    /**
     * Funkcja zwracająca dane określone przez filtry dla tabeli user
     * @param type $patient
     * @param type $physician
     * @param type $visit_date
     * @return type
     */
    public function filter($name= null,$surname = null,$login = null,$email = null,$role = null,$verified = null,$sort = null)
    {
        $select = $this->tableGateway->getSql()->select();
        if($name)
        {
            $select->where->like('name',$name.'%');
        }
        if($surname)
        {
            $select->where->like('surname',$surname.'%');
        }
        if($login)
        {
            $select->where->like('login',$login.'%');
        }
        if($email)
        {
            $select->where(array('email'=>$email));
        }
        if($role)
        {
            $select->where(array('role'=>$role));
        }
        if($verified)
        {
            $select->where(array('verified'=>$verified));
        }
        if($sort)
        {
            $select->order($sort);
        }
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset;
        /*
        $this->login    = $login;
        $this->email    = $email;        
        $this->role     = $role;
        $this->verified = $verified;
        $resultSet = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select){
            $select->where->OR->like('email','%'.$this->email.'%');
            //$select->where->OR->like('login','%'.$this->login.'%');
            $select->where->OR->like('role',$this->role);
            $select->where->OR->like('verified',$this->verified);        
    });
        
        return $resultSet;
        
        /*$sql = new \Zend\Db\Sql\Sql(new \Zend\Db\Adapter\Adapter($this->configArray));
        $select = $sql->select();
        $select->columns(array(
            'name',
            'surname',
            'login',
            'email',
            'verified',
            'role',
            ));
        
        if($patient)
        {
            $select->where(new \Zend\Db\Sql\Predicate\Expression('patient_id = ?', new \Zend\Db\Sql\Expression('(SELECT id FROM patient WHERE user_id='.$patient.')'))); 
        }
        if ($physician)
        {
           $select->where(new \Zend\Db\Sql\Predicate\Expression('physician_id = ?', new \Zend\Db\Sql\Expression('(SELECT id FROM physician WHERE user_id='.$physician.')'))); 
        }
        
        
        $select->from('users');
        

        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();*/
               
    }
       
   
}

