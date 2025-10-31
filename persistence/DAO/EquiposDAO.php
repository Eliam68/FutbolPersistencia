<?php 

require_once __DIR__ . '/GenericDAO.php';

/**
 * Class EquiposDAO
 *
 * Data Access Object para la entidad "equipos".
 * Provee operaciones básicas de lectura/escritura sobre la tabla `equipos`.
 * Utiliza la conexión mysqli proporcionada por GenericDAO/PersistentManager.
 *
 * Notas de seguridad:
 * - Las consultas con parámetros utilizan sentencias preparadas cuando es necesario
 *   (selectById, insert, delete) para evitar inyección SQL.
 * - Las funciones que retornan datos devuelven arreglos asociativos simples.
 */
class EquiposDAO extends GenericDAO {

  /** @var string Nombre de la tabla en la base de datos */
  private $tableName = "equipos";

    /**
     * Devuelve todos los equipos.
     *
     * Resultado: array de arrays con las claves: id, nombre, estadio.
     * @return array
     */
    public function selectAll() {
        $query = "SELECT * FROM " . $this->tableName;
        $result = mysqli_query($this->conn, $query);
        $teams = array();
        while ($teamBD = mysqli_fetch_array($result)) {
        $team = array(
            'Id' => $teamBD["id"],
            'nombre' => $teamBD["nombre"],
            'estadio' => $teamBD["estadio"],
        );
        array_push($teams, $team);
        }
        return $teams;
    }

    /**
     * Obtiene un equipo por su identificador.
     *
     * @param int $id Identificador del equipo
     * @return array|null Devuelve un array asociativo con las claves (id, nombre, estadio) o null si no existe
     */
    public function selectById($id) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $nombre, $estadio);

        $team = null;
        while (mysqli_stmt_fetch($stmt)) {
        $team = array(
            'id' => $id,
            'nombre' => $nombre,
            'estadio' => $estadio,
        );
        }
        return $team;
    }

    /**
     * Inserta un nuevo equipo en la base de datos.
     *
     * @param string $nombre Nombre del equipo (no debe estar vacío)
     * @param string $estadio Nombre del estadio (puede estar vacío si se desea)
     * @return bool True si la inserción tuvo éxito, false en caso de error
     */
    public function insert($nombre, $estadio) {
        $query = "INSERT INTO " . $this->tableName . " (nombre, estadio) VALUES(?,?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $nombre, $estadio);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Elimina un equipo por su id.
     *
     * @param int $id Identificador del equipo a borrar
     * @return bool True si la eliminación tuvo éxito, false en caso de error
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt);
    }
}


?>
