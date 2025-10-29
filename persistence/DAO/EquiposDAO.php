<?php 

class EquiposDAO extends GenericDAO {

  private $tableName = "equipos";

    public function selectAll() {
        $query = "SELECT * FROM " . $this->tableName;
        $result = mysqli_query($this->conn, $query);
        $teams = array();
        while ($teamBD = mysqli_fetch_array($result)) {
        $team = array(
            'id' => $teamBD["id"],
            'nombre' => $teamBD["nombre"],
            'estadio' => $teamBD["estadio"],
        );
        array_push($teams, $team);
        }
        return $teams;
    }
    public function selectById($id) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $nombre, $estadio);

        while (mysqli_stmt_fetch($stmt)) {
        $team = array(
            'id' => $id,
            'nombre' => $nombre,
            'estadio' => $estadio,
        );
        }
        return $team;
    }
    public function insert($nombre, $estadio) {
        $query = "INSERT INTO " . $this->tableName . " (nombre, estadio) VALUES(?,?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $nombre, $estadio);
        return mysqli_stmt_execute($stmt);
    }
    public function delete($id) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt);
    }
}


?>
