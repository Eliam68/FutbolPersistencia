<?php

require_once __DIR__ . '/GenericDAO.php';

class UserDAO extends GenericDAO
{

  //Se define una constante con el nombre de la tabla
  const USER_TABLE = 'usuarios';

  public function selectAll()
  {
    $query = "SELECT * FROM " . UserDAO::USER_TABLE;
    $result = mysqli_query($this->conn, $query);
    $users = array();
    while ($userBD = mysqli_fetch_array($result)) {
      $user = array(
        'id' => $userBD["id"],
        'email' => $userBD["email"],
        'password' => $userBD["password"],
      );
      array_push($users, $user);
    }
    return $users;
  }



  public function insert($email, $password)
  {
    $query = "INSERT INTO " . UserDAO::USER_TABLE .
      " (email, password) VALUES(?,?)";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    return $stmt->execute();
  }

  public static function checkExists($email, $password)
  {
    $query = "SELECT email, password FROM " . UserDAO::USER_TABLE . " WHERE email=? AND password=?";
    $stmt = mysqli_prepare(PersistentManager::getInstance()->get_connection(), $query);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    if (mysqli_stmt_execute($stmt) > 0)
      return true;
    else
      return false;
  }


  public function selectById($id)
  {
    $query = "SELECT email, password FROM " . UserDAO::USER_TABLE . " WHERE idUser=?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $email, $password);

    while (mysqli_stmt_fetch($stmt)) {
      $user = array(
        'id' => $id,
        'email' => $email,
        'password' => $password
      );
    }

    return $user;
  }

  public function update($id, $email, $password)
  {
    $query = "UPDATE " . UserDAO::USER_TABLE .
      " SET email=?, password=?"
      . " WHERE idUser=?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssi', $email, $password, $id);
    return $stmt->execute();
  }

  public function delete($id)
  {
    $query = "DELETE FROM " . UserDAO::USER_TABLE . " WHERE idUser =?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return $stmt->execute();
  }
}
