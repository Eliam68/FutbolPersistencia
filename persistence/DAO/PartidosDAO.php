<?php

require_once __DIR__ . '/GenericDAO.php';

class PartidosDAO extends GenericDAO {

    private $tableName = 'partidos';

    /**
     * Devuelve todos los partidos.
     * @return array
     */
    public function selectAll() {
        $query = "SELECT `Id` as id, `Equipo-Local-Id` as equipo_local_id, `Equipo-Visitante-Id` as equipo_visitante_id, `Jornada-Id` as jornada_id, `Resultado` as resultado, `Estadio` as estadio FROM `" . $this->tableName . "` ORDER BY `Jornada-Id`, `Id`";
        $result = mysqli_query($this->conn, $query);
        $rows = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * Devuelve un partido por su id.
     */
    public function selectById($id) {
        $query = "SELECT `Id` as id, `Equipo-Local-Id` as equipo_local_id, `Equipo-Visitante-Id` as equipo_visitante_id, `Jornada-Id` as jornada_id, `Resultado` as resultado, `Estadio` as estadio FROM `" . $this->tableName . "` WHERE `Id` = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    /**
     * Borra un partido por id.
     */
    public function delete($id) {
        $query = "DELETE FROM `" . $this->tableName . "` WHERE `Id` = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Obtiene partidos por id de jornada (Jornada-Id).
     */
    public function getByJornada($jornadaId) {
        $query = "SELECT `Id` as id, `Equipo-Local-Id` as equipo_local_id, `Equipo-Visitante-Id` as equipo_visitante_id, `Jornada-Id` as jornada_id, `Resultado` as resultado, `Estadio` as estadio FROM `" . $this->tableName . "` WHERE `Jornada-Id` = ? ORDER BY `Id`";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $jornadaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * Obtiene partidos en los que participa un equipo (por id de equipo local o visitante).
     */
    public function getByEquipo($equipoId) {
        $query = "SELECT `Id` as id, `Equipo-Local-Id` as equipo_local_id, `Equipo-Visitante-Id` as equipo_visitante_id, `Jornada-Id` as jornada_id, `Resultado` as resultado, `Estadio` as estadio FROM `" . $this->tableName . "` WHERE `Equipo-Local-Id` = ? OR `Equipo-Visitante-Id` = ? ORDER BY `Jornada-Id`, `Id`";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $equipoId, $equipoId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * Comprueba si ya existe un partido entre dos equipos en la misma jornada.
     * Se comprueba en ambos órdenes para evitar duplicados (A vs B) y (B vs A).
     * @return bool
     */
    public function partidoExists($equipoAId, $equipoBId, $jornadaId) {
        $query = "SELECT COUNT(*) as cnt FROM `" . $this->tableName . "` WHERE `Jornada-Id` = ? AND ((`Equipo-Local-Id` = ? AND `Equipo-Visitante-Id` = ?) OR (`Equipo-Local-Id` = ? AND `Equipo-Visitante-Id` = ?))";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiiii', $jornadaId, $equipoAId, $equipoBId, $equipoBId, $equipoAId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return isset($row['cnt']) && intval($row['cnt']) > 0;
    }

    /**
     * Inserta un nuevo partido, previa comprobación de duplicados.
     * @param int $equipoLocalId ID del equipo local
     * @param int $equipoVisitanteId ID del equipo visitante
     * @param int $jornadaId ID de la jornada
     * @param string|null $resultado Resultado (1, X, 2, o null)
     * @param string $estadio Estadio donde se juega (No puede ser null, se le asignará el estadio del equipo local)
     * @return bool true si insertado correctamente, false si ya existe o error.
     */
    public function insert($equipoLocalId, $equipoVisitanteId, $jornadaId, $resultado = null, $estadio = '') {
        // Validaciones mínimas
        if ($equipoLocalId == $equipoVisitanteId) {
            return false;
        }
        if ($jornadaId <= 0) {
            return false;
        }
        if (empty($estadio)) {
            return false;
        }

        // Comprobar duplicado (considerando ambos órdenes)
        if ($this->partidoExists($equipoLocalId, $equipoVisitanteId, $jornadaId)) {
            return false;
        }

        $query = "INSERT INTO `" . $this->tableName . "` (`Equipo-Local-Id`, `Equipo-Visitante-Id`, `Jornada-Id`, `Resultado`, `Estadio`) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        
        mysqli_stmt_bind_param($stmt, 'iiiss', $equipoLocalId, $equipoVisitanteId, $jornadaId, $resultado, $estadio);
        $executed = mysqli_stmt_execute($stmt);
        return $executed;
    }

}

?>
