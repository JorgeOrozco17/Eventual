<?php
require_once 'dbconexion.php';

class PersonalModel {
    private $conn;

    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }


    public function registrarLog($id_usuario, $accion, $id_acciones) {
    $sql = "INSERT INTO logs (id_usuario, accion, id_acciones) VALUES (:id_usuario, :accion, :id_acciones)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':accion', $accion, PDO::PARAM_STR);
    $stmt->bindParam(':id_acciones', $id_acciones, PDO::PARAM_STR);
    return $stmt->execute();
}


    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM personal");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmpleadosByJurisdiccion($jurisdiccion) {
        $stmt = $this->conn->prepare("SELECT * FROM personal WHERE id_adscripcion = ?");
        $stmt->execute([$jurisdiccion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAutorizados($responsable){
        $sql = "SELECT id, nombre_alta, RFC, sueldo_neto, sueldo_bruto, puesto, programa, movimiento, centro, CURP, 
                    observaciones_alta, observaciones_baja
                FROM personal 
                WHERE autorizacion = 1";

        // bandera para saber si se va a filtrar
        $filtrar = ($responsable != 10);

        if ($filtrar) {
            $sql .= " AND id_adscripcion = :responsable";
        }

        $stmt = $this->conn->prepare($sql);

        if ($filtrar) {
            $stmt->bindParam(':responsable', $responsable, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getNoAutorizados(){
        $stmt = $this->conn->prepare("SELECT * FROM personal WHERE autorizacion = 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(" SELECT *
        FROM personal 
        WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data) {
        try {
            // Consulta para obtener la clave de recurso
            $stmtCVE = $this->conn->prepare("SELECT cve_recurso FROM recurso WHERE nombre = ?");
            $stmtCVE->execute([$data['programa']]);
            $cve_recurso = $stmtCVE->fetchColumn();

            $stmt = $this->conn->prepare("
                SELECT 
                    j.nombre AS adscripcionnombre,
                    j.ubicacion AS ubicacion,
                    c.nombre AS centronombre,
                    c.clues AS cluenombre,
                    c.ct_art_74 AS art74,
                    p.codigo AS codigo_puesto
                FROM 
                    jurisdicciones j
                    JOIN centros c ON c.id = :centro
                    JOIN puestos p ON p.nombre_puesto = :puesto
                WHERE 
                    j.id = :adscripcion
            ");
            $stmt->execute([
                ':adscripcion' => $data['adscripcion'],
                ':centro' => $data['centro'],
                ':puesto' => $data['puesto']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                $mensajeError = $errorInfo[2];

                // Registrar log de error
                $this->registrarLog($_SESSION['user_id'] ?? 0, "Error: $mensajeError", json_encode($data));
                return false;
            }

            // Variables
            $adscripcionnombre = $result['adscripcionnombre'] ?? null;
            $centronombre      = $result['centronombre'] ?? null;
            $cluenombre        = $result['cluenombre'] ?? null;
            $codigo_puesto     = $result['codigo_puesto'] ?? null;
            $ct_art74          = $result['art74'] ?? null;
            $ubicacion        = $result['ubicacion'] ?? null;

            // Estatus
            $estatus = ($data['movimiento'] === 'alta') ? 'activo' : 'autorizacion';

            // Inserción en personal
            $stmtInsert = $this->conn->prepare("
                INSERT INTO personal (
                    numero_oficio, movimiento, solicita, oficio, puesto, codigo, programa, clave_recurso, rama, id_adscripcion, adscripcion, 
                    id_centro, centro, clues, RFC, CURP, sueldo_bruto, inicio_contratacion, quincena_alta, 
                    nombre_alta, fecha_baja, quincena_baja, cuenta, observaciones_alta, observaciones_baja, 
                    id_usuario_registro, estatus, autorizacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");

            $ok = $stmtInsert->execute([
                $data['numero_oficio'] ?? null,
                $data['movimiento'] ?? null,
                $data['solicita'] ?? null,
                $data['oficio'] ?? null,
                $data['puesto'] ?? null,
                $codigo_puesto ?? null,
                $data['programa'] ?? null,
                $cve_recurso ?? null,
                $data['rama'] ?? null,
                $data['adscripcion'] ?? null,
                $adscripcionnombre ?? null,
                $data['centro'] ?? null,
                $centronombre ?? null,
                $cluenombre ?? null,
                $data['RFC'] ?? null,
                $data['CURP'] ?? null,
                $data['sueldo_bruto'] ?? null,
                $data['inicio_contratacion'] ?? null,
                $data['quincena_alta'] ?? null,
                $data['nombre_alta'] ?? null,
                $data['fecha_baja'] ?? null,
                $data['quincena_baja'] ?? null,
                !empty($data['cuenta']) ? $data['cuenta'] : null,
                $data['observaciones_alta'] ?? null,
                $data['observaciones_baja'] ?? null,
                $_SESSION['user_id'] ?? 1,
                $estatus
            ]);

            if ($ok) {
                $lastInsertId = $this->conn->lastInsertId();

                $stmtart74 = $this->conn->prepare("
                    INSERT INTO art_74 (id_personal, desc_ct_dpto, desc_cen_art74, ct_art_74, juris)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $ok74 =$stmtart74->execute([
                    $lastInsertId,
                    $cluenombre . ' ' . $adscripcionnombre . ' ' . $centronombre,
                    $adscripcionnombre . ' ' . $centronombre,
                    $ct_art74,
                    $adscripcionnombre . '-' . $ubicacion
                ]);

                if ($ok74) {
                    // Registrar log de éxito en art_74
                    $this->registrarLog($_SESSION['user_id'] ?? 0, "Alta en art_74 realizada", $lastInsertId);
                } else {
                $errorInfo = $stmtart74->errorInfo(); 
                    $mensaje = "Error INSERT art_74: " . ($errorInfo[2] ?? 'Desconocido');
                    $this->registrarLog($_SESSION['user_id'] ?? 0, $mensaje, $lastInsertId);
                }

                // Guardar comentario si viene
                if (!empty($data['observaciones_usuario'])) {
                    $stmtComent = $this->conn->prepare("
                        INSERT INTO coments (id_personal, id_usuario, comentario)
                        VALUES (?, ?, ?)
                    ");
                    $stmtComent->execute([
                        $lastInsertId,
                        $_SESSION['user_id'] ?? 1,
                        $data['observaciones_usuario']
                    ]);
                }

                // Registrar log de éxito
                $this->registrarLog($_SESSION['user_id'] ?? 0, "Alta empleado realizada", $lastInsertId);

                return $lastInsertId;
            } else {
                // Registrar log de fallo
                $this->registrarLog($_SESSION['user_id'] ?? 0, "Error: fallo en INSERT personal", "0");
                return false;
            }

        } catch (Exception $e) {
            // Registrar log con el error exacto
            $this->registrarLog($_SESSION['user_id'] ?? 0, "Excepción: " . $e->getMessage(), "0");
            return false;
        }
    }

    public function saveAltaBaja($data) {
        try {
            // Consulta para obtener la clave de recurso
            $stmtCVE = $this->conn->prepare("SELECT cve_recurso FROM recurso WHERE nombre = ?");
            $stmtCVE->execute([$data['programa']]);
            $cve_recurso = $stmtCVE->fetchColumn();

            // Consulta optimizada para obtener los nombres de adscripción, centro y clues, y el código de puesto
            $stmt = $this->conn->prepare("
                SELECT 
                    j.nombre AS adscripcionnombre,
                    j.ubicacion AS ubicacion,
                    c.id AS centroid,
                    c.clues AS cluenombre,
                    c.ct_art_74 AS art74,
                    p.codigo AS codigo_puesto
                FROM 
                    jurisdicciones j
                    JOIN centros c ON c.nombre = :centro
                    JOIN puestos p ON p.nombre_puesto = :puesto
                WHERE 
                    j.id = :adscripcion
            ");
            $stmt->execute([
                ':adscripcion' => $data['adscripcion'],
                ':centro' => $data['centro'],
                ':puesto' => $data['puesto']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $mensajeError = sprintf(
                    "Error: Adscripción/Centro/Puesto no encontrados. Adscripción: %s, Centro: %s, Puesto: %s",
                    $data['adscripcion'],
                    $data['centro'],
                    $data['puesto']
                );

                // Si quieres también agregar info de PDO
                $pdoError = $stmt->errorInfo();
                $mensajeError .= " | SQLSTATE: {$pdoError[0]}, Código: {$pdoError[1]}, Mensaje: {$pdoError[2]}";

                $this->registrarLog($_SESSION['user_id'] ?? 0, $mensajeError, "0");
                return false;
            }

            // Variables
            $adscripcionnombre = $result['adscripcionnombre'];
            $centroid      = $result['centroid'];
            $cluenombre        = $result['cluenombre'];
            $codigo_puesto     = $result['codigo_puesto'];
            $ct_art74          = $result['art74'] ?? null;
            $ubicacion        = $result['ubicacion'];

            // Estatus
            $estatus = ($data['movimiento'] === 'alta') ? 'activo' : 'autorizacion';

            // Inserción en personal
            $stmtInsert = $this->conn->prepare("
                INSERT INTO personal (
                    numero_oficio, movimiento, solicita, oficio, puesto, codigo, programa, clave_recurso, rama, id_adscripcion, adscripcion, 
                    id_centro, centro, clues, RFC, CURP, sueldo_bruto, inicio_contratacion, quincena_alta, 
                    nombre_alta, fecha_baja, quincena_baja, cuenta, observaciones_alta, observaciones_baja, 
                    id_usuario_registro, estatus, autorizacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");

            $ok = $stmtInsert->execute([
                $data['numero_oficio'] ?? null,
                $data['movimiento'],
                $data['solicita'] ?? null,
                $data['oficio'],
                $data['puesto'], ////
                $codigo_puesto, ////
                $data['programa'],
                $cve_recurso,
                $data['rama'],
                $data['adscripcion'],
                $adscripcionnombre,/////
                $centroid,
                $data['centro'],
                $cluenombre,
                $data['RFC'],
                $data['CURP'],
                $data['sueldo_bruto'] ?? null,
                $data['inicio_contratacion'],
                $data['quincena_alta'] ?? null,
                $data['nombre_alta'] ?? null,
                $data['fecha_baja'] ?? null,
                $data['quincena_baja'] ?? null,
                !empty($data['cuenta']) ? $data['cuenta'] : null,
                $data['observaciones_alta'] ?? null,
                $data['observaciones_baja'] ?? null,
                $_SESSION['user_id'] ?? 1,
                $estatus
            ]);

            if ($ok) {
                $lastInsertId = $this->conn->lastInsertId();

                $stmtart74 = $this->conn->prepare("
                    INSERT INTO art_74 (id_personal, desc_ct_dpto, desc_cen_art74, ct_art_74, juris)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $ok74 =$stmtart74->execute([
                    $lastInsertId,
                    $cluenombre . ' ' . $adscripcionnombre . ' ' . $data['centro'],
                    $adscripcionnombre . ' ' . $data['centro'],
                    $ct_art74,
                    $adscripcionnombre . '-' . $ubicacion
                ]);

                if ($ok74) {
                    // Registrar log de éxito en art_74
                    $this->registrarLog($_SESSION['user_id'] ?? 0, "Alta en art_74 realizada", $lastInsertId);
                } else {
                $errorInfo = $stmtart74->errorInfo(); 
                    $mensaje = "Error INSERT art_74: " . ($errorInfo[2] ?? 'Desconocido');
                    $this->registrarLog($_SESSION['user_id'] ?? 0, $mensaje, $lastInsertId);
                }

                // Guardar comentario si viene
                if (!empty($data['observaciones_usuario'])) {
                    $stmtComent = $this->conn->prepare("
                        INSERT INTO coments (id_personal, id_usuario, comentario)
                        VALUES (?, ?, ?)
                    ");
                    $stmtComent->execute([
                        $lastInsertId,
                        $_SESSION['user_id'] ?? 1,
                        $data['observaciones_usuario']
                    ]);
                }

                // Registrar log de éxito
                $this->registrarLog($_SESSION['user_id'] ?? 0, "Alta empleado realizada", $lastInsertId);

                return $lastInsertId;
            } else {
                // Registrar log de fallo
                $this->registrarLog($_SESSION['user_id'] ?? 0, "Error: fallo en INSERT personal", "0");
                return false;
            }

        } catch (Exception $e) {
            // Registrar log con el error exacto
            $this->registrarLog($_SESSION['user_id'] ?? 0, "Excepción: " . $e->getMessage(), "0");
            return false;
        }
    }


    public function CalculoPersonal($id_personal) {
        // 1. Traer sueldo_bruto de la tabla personal
        $stmtPersonal = $this->conn->prepare("SELECT sueldo_bruto FROM personal WHERE id = ?");
        $stmtPersonal->execute([$id_personal]);
        $bruto_mensual = $stmtPersonal->fetchColumn();

        if ($bruto_mensual === false) {
            // No se encontró el registro
            return false;
        }

        // 2. Traer valores de fijos (D_S2, D_S4, D_S5, D_S6, P_01)
        $stmtFijos = $this->conn->query("SELECT concepto, cantidad FROM fijos WHERE concepto IN ('D_S2', 'D_S4', 'D_S5', 'D_S6', 'P_01')");
        $fijos = [];
        while ($row = $stmtFijos->fetch(PDO::FETCH_ASSOC)) {
            $fijos[$row['concepto']] = (float)$row['cantidad'];
        }
        // Default a 0 si no está
        $D_S2 = $fijos['D_S2'] ?? 0;
        $D_S4 = $fijos['D_S4'] ?? 0;
        $D_S5 = $fijos['D_S5'] ?? 0;
        $D_S6 = $fijos['D_S6'] ?? 0;
        $P_01 = $fijos['P_01'] ?? 0;

        // 3. dsctos_issste = suma de los descuentos
        $dsctos_issste = $D_S2 + $D_S4 + $D_S5 + $D_S6;

        // 4. bruto_qna
        $bruto_qna = $bruto_mensual / 2;

        // 5. comp_garantizada
        $comp_garantizada = $bruto_qna - $P_01;

        // 6. sueldo_diario
        $sueldo_diario = $bruto_mensual / 15;

        // 7. isr_mens e isr_qna
        // Buscar el rango en la tabla de isr
        $stmtISR = $this->conn->prepare("
            SELECT lim_inferior, cuota_fija, porcentaje
            FROM anexo8_rmf
            WHERE ? BETWEEN lim_inferior AND lim_superior
            LIMIT 1
        ");
        $stmtISR->execute([$bruto_mensual]);
        $rowISR = $stmtISR->fetch(PDO::FETCH_ASSOC);

        if ($rowISR) {
            $lim_inferior = (float)$rowISR['lim_inferior'];
            $cuota_fija = (float)$rowISR['cuota_fija'];
            $porcentaje = (float)$rowISR['porcentaje'];
            $isr_mens = (($bruto_mensual - $lim_inferior) * ($porcentaje / 100)) + $cuota_fija;
            $isr_qna = $isr_mens / 2;
        } else {
            // Si no hay rango, lo dejas en 0 (puedes ajustar este comportamiento)
            $isr_mens = 0;
            $isr_qna = 0;
        }

        $dsctos_issste_mensual = $dsctos_issste * 2;

        // 8. neto_mensual y neto_qna
        $neto_mensual = ($bruto_mensual - $isr_mens) - $dsctos_issste_mensual;
        $neto_qna = $neto_mensual / 2;

        // 9. P_00
        $P_00 = ($bruto_mensual <= 10171) ? 475 : null;

        // 10. sueldo igual a P_01
        $sueldo = $P_01;

        // 11. Insertar en calculo_personal
        $stmtInsert = $this->conn->prepare("
            INSERT INTO calculo_personal (
                id_personal, sueldo, comp_garantizada, dsctos_issste, neto_qna, neto_mensual, bruto_qna, bruto_mensual,
                sueldo_diario, isr_qna, isr_mens, D_S2, D_S4, D_S5, D_S6, P_01, P_00
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $ok = $stmtInsert->execute([
            $id_personal,
            $sueldo,
            $comp_garantizada,
            $dsctos_issste,
            $neto_qna,
            $neto_mensual,
            $bruto_qna,
            $bruto_mensual,
            $sueldo_diario,
            $isr_qna,
            $isr_mens,
            $D_S2,
            $D_S4,
            $D_S5,
            $D_S6,
            $P_01,
            $P_00
        ]);

        if ($ok) {
        $stmtUpdate = $this->conn->prepare("UPDATE personal SET sueldo_neto = ? WHERE id = ?");
        $stmtUpdate->execute([$neto_mensual, $id_personal]);
    }

    return $ok;
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM personal WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existsRFC($rfc, $movimiento) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM personal WHERE RFC = ? AND movimiento = ?");
        $stmt->execute([$rfc, $movimiento]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function existsCURP($curp, $movimiento) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM personal WHERE CURP = ? AND movimiento = ?");
        $stmt->execute([$curp, $movimiento]);
        return $stmt->fetchColumn() > 0;
    }

    public function getComentsById($id){
        $stmt = $this->conn->prepare("
            SELECT comentario, fecha 
            FROM coments 
            WHERE id_personal = ? 
            ORDER BY fecha DESC 
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $coment = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si no hay comentario, regresamos un array vacío
        return $coment ? $coment : ['comentario' => '', 'fecha' => ''];
    }


    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT id, adscripcion, jurisdiccion FROM personal WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllbyrfc($rfc){
        $stmt = $this->conn->prepare("SELECT * FROM personal WHERE RFC = ?");
        $stmt->execute([$rfc]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function getStatusById($id) {
        $stmt = $this->conn->prepare("SELECT estatus FROM personal WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function updateBaja($data) {
    try {
        $this->conn->beginTransaction();

        $stmt = $this->conn->prepare("
            UPDATE personal 
            SET 
                movimiento = ?,
                fecha_baja = ?,
                solicita = ?, 
                quincena_baja = ?, 
                observaciones_baja = ?, 
                autorizacion = 0,
                estatus = 'inactivo'
            WHERE id = ?
        ");

        $ok = $stmt->execute([
            $data['movimiento'],
            $data['fecha_baja'],
            $data['solicita'],
            $data['quincena_baja'],
            $data['observaciones_baja'] ?? '',
            $data['id']
        ]);

        if ($ok && !empty($data['observaciones_usuario'])) {
            $stmtComent = $this->conn->prepare("
                INSERT INTO coments (id_personal, id_usuario, comentario)
                VALUES (?, ?, ?)
            ");
            $stmtComent->execute([
                $data['id'],
                $_SESSION['user_id'] ?? 1,
                $data['observaciones_usuario']
            ]);
        }

        $this->conn->commit();

        $this->registrarLog($_SESSION['user_id'] ?? 0, "Se actualizó BAJA de personal ID {$data['id']}", $data['id']);
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        $this->registrarLog($_SESSION['user_id'] ?? 0, "Error en updateBaja: " . $e->getMessage(), $data['id'] ?? 0);
        return false;
    }
}



    public function completeEmployee($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE personal 
            SET 
                nacionalidad = ?,
                estado_civil = ?,
                profesion = ?,
                originario = ?,
                calle = ?,
                colonia = ?,
                ciudad = ?,
                estado = ?
            WHERE id = ?");
       
            $ok = $stmt->execute([
                $data['nacionalidad'],
                $data['estado_civil'],
                $data['profesion'],
                $data['originario'],
                $data['calle'],
                $data['colonia'],
                $data['ciudad'],
                $data['estado'],
                $id
            ]);
        return $ok;
    }

    public function altaxbaja($data){
            $stmt = $this->conn->prepare("
            UPDATE personal 
            SET movimiento = ?, quincena_baja = ?, fecha_baja = ?, observaciones_baja = ?, autorizacion = 1, estatus = 'inactivo'
            WHERE id = ?
        ");

        $ok = $stmt->execute([
            $data['movimiento'],
            $data['quincena_baja'],
            $data['fecha_baja'],
            $data['observaciones_baja'] ?? '',
            $data['id']
        ]);

        if ($ok && !empty($data['observaciones_usuario'])) {
            $stmtComent = $this->conn->prepare("
                INSERT INTO coments (id_personal, id_usuario, comentario)
                VALUES (?, ?, ?)
            ");
            $stmtComent->execute([
                $data['id'],
                $_SESSION['user_id'] ?? 1,
                $data['observaciones_usuario']
            ]);
        }
        return $ok;
    }

    public function getPersonalNew($qna, $anio){
    $stmt = $this->conn->prepare("SELECT *,
               (sueldo_neto * 2)  AS sueldo_neto_mensual,
               (sueldo_bruto * 2) AS sueldo_bruto_mensual
        FROM personal
        WHERE (quincena_alta = ? AND YEAR(inicio_contratacion) = ?)
           OR (quincena_baja = ? AND YEAR(fecha_baja) = ?)
    ");
    $stmt->execute([$qna, $anio, $qna, $anio]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnexosById($id){
        $stmt = $this->conn->prepare("
        SELECT * FROM anexos_personal WHERE id_personal = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
