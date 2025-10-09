<?php 
require_once 'dbconexion.php';

class Capturamodel{
    private $conn;

    public function __construct(){
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    public function getAll(){
        $stmt = $this->conn->prepare("SELECT * FROM captura");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPeriodo($id_nomina){
        $stmt = $this->conn->prepare("SELECT * FROM captura WHERE id_nomina = :id_nomina");
        $stmt->bindParam(':id_nomina', $id_nomina);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNominaById($id){
        $stmt = $this->conn->prepare("SELECT * FROM captura WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO:: FETCH_ASSOC);
    }

    ////////////////                                    Funciones de calculo de nomina.php                  ////////////////
    public function getAllNomina() {
        $stmt = $this->conn->prepare("SELECT * FROM nominas");
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    public function deleteNomina($id){
        $stmt = $this->conn->prepare("DELETE FROM nominas WHERE id = ?");
        return $stmt->execute([$id]);
    }


    //////////////////////////////////////////    Funciones para procesar la nomina  //////////////////////////////////////////

    public function insertNomina ($tipo, $quincena, $anio){
        $stmt = $this->conn->prepare("INSERT INTO nominas (qna, año, tipo, total_registros,total_percepciones, total_deducciones, total_neto, estatus)
        VALUES (?, ?, ?, 0, 0.00, 0.00, 0.00, 0)");
        $stmt->execute([$quincena, $anio, $tipo]);

        return $this->conn->lastInsertId();
    }

    //Funcion para verificar si ya existe una nomina generada
    public function nominaExistente($quincena, $anio) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM nominas WHERE qna = ? AND año = ?");
        $stmt->execute([$quincena, $anio]);

        // Si existe una nómina con esa quincena y año, devuelve true
        if ($stmt->fetchColumn() > 0) {
            return true;
        }

        // Si no existe, devuelve false
        return false;
    }


    public function generarCaptura($quincena, $anio, $id_nomina) {
        // Iniciar transacción
        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("
                SELECT 
                p.id AS id_personal, 
                p.RFC, 
                p.CURP, 
                p.nombre_alta, 
                p.clues, 
                p.adscripcion,
                p.centro, 
                p.inicio_contratacion, 
                p.programa,
                p.clave_recurso,
                p.rama,
                p.puesto,
                p.cuenta,
                p.codigo,
                c.D_S2, 
                c.D_S4,
                c.D_S5,
                c.D_S6,
                c.P_01,
                c.P_00, 
                c.comp_garantizada, 
                c.isr_qna, 
                c.sueldo_diario,
                a.desc_ct_dpto,
                a.desc_cen_art74,
                a.ct_art_74,
                a.juris
            FROM personal p
            LEFT JOIN calculo_personal c ON c.id_personal = p.id
            LEFT JOIN art_74 a ON a.id_personal = p.id
            WHERE p.estatus = 'activo'
            ");
            $stmt->execute();
            $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Preparar la consulta de inserción para la tabla captura
            $stmtInsert = $this->conn->prepare(" 
                INSERT INTO captura (
                    id_nomina, id_personal, NOM, RFC, CURP, NOMBRE, CLUES, JUR, DESCRIPCION_CLUES, CODIGO, QNA,
                    AÑO, STATUS, FECHA_INGRESO, DESC_TNOMINA, RECURSO, CVE_RECURSO, DESC_CATEGORIAS, RAMA,
                    D_S2, D_S4, D_S5, D_S6, P_01, P_26, D_01, SD, CUENTA, P_00, DESC_CT_DEPTO, DESC_CEN_ART74, CT_ART_74, JURIS
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // Insertar cada empleado en la tabla captura
            foreach ($empleados as $emp) {
                $D_S2 = $emp['D_S2'] ?? 0;
                $D_S4 = $emp['D_S4'] ?? 0;
                $D_S5 = $emp['D_S5'] ?? 0;
                $D_S6 = $emp['D_S6'] ?? 0;
                $P_01 = $emp['P_01'] ?? 0;
                $P_26 = $emp['comp_garantizada'] ?? 0;
                $D_01 = $emp['isr_qna'] ?? 0;
                $SD = $emp['sueldo_diario'] ?? 0;
                $P_00 = $emp['P_00'] ?? 0;

                // Insertar en la tabla captura
                $stmtInsert->execute([
                    $id_nomina,
                    $emp['id_personal'],
                    'EVE',
                    $emp['RFC'] ?? null,
                    $emp['CURP'] ?? null,
                    $emp['nombre_alta'] ?? null,
                    $emp['clues'] ?? null,
                    $emp['adscripcion'] ?? null,
                    $emp['centro'] ?? null,
                    $emp['codigo'] ?? null,
                    $quincena  ?? null,
                    $anio ?? null,
                    'ACTIVO' ?? null,
                    $emp['inicio_contratacion'] ?? null,
                    $emp['desc_tnomina'] ?? null,
                    $emp['programa'] ?? null,
                    $emp['clave_recurso'] ?? null,
                    $emp['puesto'] ?? null,
                    $emp['rama'] ?? null,
                    $D_S2,
                    $D_S4,
                    $D_S5,
                    $D_S6,
                    $P_01,
                    $P_26,
                    $D_01,
                    $SD,
                    $emp['cuenta'] ?? 0,
                    $P_00,
                    $emp['desc_ct_dpto'] ?? null,
                    $emp['desc_cen_art74'] ?? null,
                    $emp['ct_art_74'] ?? null,
                    $emp['juris'] ?? null
                ]);
            }

            // Una vez insertados todos los datos, actualizar las columnas de pensión
            $this->UpdatePensionACaptura($quincena, $anio);
            $this->UpdateTemporales($quincena, $anio);

            // Confirmar la transacción
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Si ocurre algún error, revertir la transacción
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function generarCapturaExtra($quincena, $anio, $id_nomina) {
        // Iniciar transacción
        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id AS id_personal, 
                    p.RFC, 
                    p.CURP, 
                    p.nombre_alta, 
                    p.clues, 
                    p.adscripcion,
                    p.centro, 
                    p.inicio_contratacion, 
                    p.programa,
                    p.clave_recurso,
                    p.rama,
                    p.puesto,
                    p.cuenta,
                    p.codigo,
                    c.D_S2, 
                    c.D_S4,
                    c.D_S5,
                    c.D_S6,
                    c.P_01,
                    c.P_00, 
                    c.comp_garantizada, 
                    c.isr_qna, 
                    c.sueldo_diario,
                    a.desc_ct_dpto,
                    a.desc_cen_art74,
                    a.ct_art_74,
                    a.juris
                FROM personal p
                LEFT JOIN calculo_personal c ON c.id_personal = p.id
                LEFT JOIN art_74 a ON a.id_personal = p.id
                WHERE p.estatus = 'activo'
                AND p.id NOT IN (
                    SELECT id_personal 
                    FROM captura ca
                    INNER JOIN nominas n ON n.id = ca.id_nomina
                    WHERE n.qna = :qna
                        AND n.año = :anio
                        AND n.tipo = 'Ordinaria'
                )
            ");
            $stmt->bindParam(':qna', $quincena, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();
            $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Preparar la consulta de inserción para la tabla captura
            $stmtInsert = $this->conn->prepare(" 
                INSERT INTO captura (
                    id_nomina, id_personal, NOM, RFC, CURP, NOMBRE, CLUES, JUR, DESCRIPCION_CLUES, CODIGO, QNA,
                    AÑO, STATUS, FECHA_INGRESO, DESC_TNOMINA, RECURSO, CVE_RECURSO, DESC_CATEGORIAS, RAMA,
                    D_S2, D_S4, D_S5, D_S6, P_01, P_26, D_01, SD, CUENTA, P_00, DESC_CT_DEPTO, DESC_CEN_ART74, CT_ART_74, JURIS
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // Insertar cada empleado en la tabla captura
            foreach ($empleados as $emp) {
                $D_S2 = $emp['D_S2'] ?? 0;
                $D_S4 = $emp['D_S4'] ?? 0;
                $D_S5 = $emp['D_S5'] ?? 0;
                $D_S6 = $emp['D_S6'] ?? 0;
                $P_01 = $emp['P_01'] ?? 0;
                $P_26 = $emp['comp_garantizada'] ?? 0;
                $D_01 = $emp['isr_qna'] ?? 0;
                $SD = $emp['sueldo_diario'] ?? 0;
                $P_00 = $emp['P_00'] ?? 0;

                // Insertar en la tabla captura
                $stmtInsert->execute([
                    $id_nomina,
                    $emp['id_personal'],
                    'EVE',
                    $emp['RFC'] ?? null,
                    $emp['CURP'] ?? null,
                    $emp['nombre_alta'] ?? null,
                    $emp['clues'] ?? null,
                    $emp['adscripcion'] ?? null,
                    $emp['centro'] ?? null,
                    $emp['codigo'] ?? null,
                    $quincena  ?? null,
                    $anio ?? null,
                    'ACTIVO' ?? null,
                    $emp['inicio_contratacion'] ?? null,
                    $emp['desc_tnomina'] ?? null,
                    $emp['programa'] ?? null,
                    $emp['clave_recurso'] ?? null,
                    $emp['puesto'] ?? null,
                    $emp['rama'] ?? null,
                    $D_S2,
                    $D_S4,
                    $D_S5,
                    $D_S6,
                    $P_01,
                    $P_26,
                    $D_01,
                    $SD,
                    $emp['cuenta'] ?? 0,
                    $P_00,
                    $emp['desc_ct_dpto'] ?? null,
                    $emp['desc_cen_art74'] ?? null,
                    $emp['ct_art_74'] ?? null,
                    $emp['juris'] ?? null
                ]);
            }

            // Una vez insertados todos los datos, actualizar las columnas de pensión
            $this->UpdatePensionACaptura($quincena, $anio);
            $this->UpdateTemporales($quincena, $anio);

            // Confirmar la transacción
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Si ocurre algún error, revertir la transacción
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function UpdateLicencias($quincena, $anio) {
        $sql = "
        UPDATE captura c
        JOIN faltas f ON c.id_personal = f.id_personal
        JOIN calculo_personal cp ON c.id_personal = cp.id_personal
        SET 
    
            c.D_S1 = (cp.sueldo_diario * f.responsabilidades)

        WHERE c.QNA = :qna
        AND c.AÑO = :anio
        AND f.quincena = :qna
        AND f.año = :anio
        AND c.id_personal = f.id_personal
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qna', $quincena);
        $stmt->bindParam(':anio', $anio);
        $ok = $stmt->execute();

        if ($ok) {
            $this->log_accion($_SESSION['user_id'] ?? 0, "UpdateLicencias exitoso para QNA $quincena/$anio", $quincena);
        } else {
            $error = $stmt->errorInfo();
            $this->log_accion($_SESSION['user_id'] ?? 0, "Error UpdateLicencias: {$error[2]}", $quincena);
        }

        return $ok;
    }

    public function UpdatePensionACaptura($quincena, $anio) {
        // Actualizar las columnas PENSION, BENEFICIARIA, CUENTA_BENEFICIARIA y D_62 de la tabla captura
        $sql = "
            UPDATE captura c
            JOIN pension p ON c.id_personal = p.id_personal
            JOIN calculo_personal cp ON c.id_personal = cp.id_personal
            SET 
                c.PENSION = p.porcentaje,
                c.BENEFICIARIA = p.beneficiaria,
                c.CUENTA_BENEFICIARIA = p.cuenta_beneficiaria,
                c.D_62 = ((cp.bruto_qna - (c.D_01 + cp.D_S2 + cp.D_S4 + cp.D_S5 + cp.D_S6)) * p.porcentaje)
            WHERE c.QNA = :qna
            AND c.AÑO = :anio
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qna', $quincena);
        $stmt->bindParam(':anio', $anio);
        $ok = $stmt->execute();

        if ($ok) {
            $this->log_accion($_SESSION['user_id'] ?? 0, "UpdatePensionACaptura exitoso para QNA $quincena/$anio", $quincena);
        } else {
            $error = $stmt->errorInfo();
            $this->log_accion($_SESSION['user_id'] ?? 0, "Error UpdatePensionACaptura: {$error[2]}", $quincena);
        }

        return $ok;
    }

    public function UpdateFaltas($quincena, $anio){
        $sql = "
        UPDATE captura c
        JOIN faltas f ON c.id_personal = f.id_personal
        JOIN calculo_personal cp ON c.id_personal = cp.id_personal
        SET 
            c.DIAS = f.faltas,
            c.DSCTO = (cp.sueldo_diario * f.faltas),
            c.D_05 = (cp.sueldo_diario * f.faltas)

        WHERE c.QNA = :qna
        AND c.AÑO = :anio
        AND f.quincena = :qna
        AND f.año = :anio
        AND c.id_personal = f.id_personal
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qna', $quincena);
        $stmt->bindParam(':anio', $anio);
        $ok = $stmt->execute();

        if ($ok) {
            $this->log_accion($_SESSION['user_id'] ?? 0, "UpdateFaltas exitoso para QNA $quincena/$anio", $quincena);
        } else {
            $error = $stmt->errorInfo();
            $this->log_accion($_SESSION['user_id'] ?? 0, "Error UpdateFaltas: {$error[2]}", $quincena);
        }

        return $ok;
    }

    public function deleteCaptura($qna, $anio){
        $stmt = $this->conn->prepare("DELETE FROM captura WHERE QNA = ? AND AÑO = ?");
        return $stmt->execute([$qna, $anio]);
    }

    public function calcularTotales($id_nomina, $qna, $anio) {

        $this->UpdateFaltas($qna, $anio);
        $this->UpdateLicencias($qna, $anio);

        // Listado de columnas deducciones y percepciones según tu tabla
        $deduccionesCampos = [
            'D_01', 'D_04', 'D_05', 'D_62', 'D_64', 'D_65', 'D_R1', 'D_R2',
            'D_R3', 'D_R4', 'D_AS', 'D_AM', 'D_S1', 'D_S2', 'D_S4', 'D_S5',
            'D_S6', 'D_O1'
        ];
        $percepcionesCampos = ['P_00', 'P_01', 'P_06', 'P_26'];

        // 1. Obtener todas las filas de captura para el periodo indicado
        $stmtSelect = $this->conn->prepare("SELECT id, " . implode(", ", array_merge($deduccionesCampos, $percepcionesCampos)) . " FROM captura WHERE id_nomina = ?");
        $stmtSelect->execute([$id_nomina]);
        $filas = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        // 2. Preparar sentencia para actualizar
        $stmtUpdate = $this->conn->prepare("
            UPDATE captura SET
                PERCEPCIONES = ?,
                DEDUCCIONES = ?,
                TOTAL_NETO = ?
            WHERE id = ?
        ");

        // 3. Calcular por cada fila y actualizar
        foreach ($filas as $fila) {
            $totalDeducciones = 0;
            foreach ($deduccionesCampos as $campo) {
                $totalDeducciones += floatval($fila[$campo] ?? 0);
            }

            $totalPercepciones = 0;
            foreach ($percepcionesCampos as $campo) {
                $totalPercepciones += floatval($fila[$campo] ?? 0);
            }

            $totalNeto = $totalPercepciones - $totalDeducciones;

            // Actualizar la fila con los totales calculados
            $stmtUpdate->execute([
                $totalPercepciones,
                $totalDeducciones,
                $totalNeto,
                $fila['id']
            ]);
        }

        return true;
    }

    public function insertartotales($id_nomina) {

        // Sumar percepciones, deducciones, total neto y contar registros en captura para periodo dado
        $sql = "
            SELECT 
                COUNT(*) AS total_registros,
                SUM(PERCEPCIONES) AS total_percepciones,
                SUM(DEDUCCIONES) AS total_deducciones,
                SUM(TOTAL_NETO) AS total_neto
            FROM captura
            WHERE id_nomina = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_nomina]);
        $totales = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$totales) {
            return false;
        }

        // Actualizar tabla nominas con los totales calculados
        $sqlUpdate = "
            UPDATE nominas SET
                total_registros = ?,
                total_percepciones = ?,
                total_deducciones = ?,
                total_neto = ?,
                estatus = ?
            WHERE id = ?
        ";

        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        return $stmtUpdate->execute([
            $totales['total_registros'],
            $totales['total_percepciones'],
            $totales['total_deducciones'],
            $totales['total_neto'],
            1,
            $id_nomina
        ]);
    }

   public function updateCapturaManual($id, $data) {
        $sql = "UPDATE captura SET
            D_01 = :D_01, D_04 = :D_04, D_05 = :D_05, D_62 = :D_62, D_64 = :D_64, D_65 = :D_65,
            D_R1 = :D_R1, D_R2 = :D_R2, D_R3 = :D_R3, D_R4 = :D_R4, D_AS = :D_AS, D_AM = :D_AM,
            D_S1 = :D_S1, D_S2 = :D_S2, D_S4 = :D_S4, D_S5 = :D_S5, D_S6 = :D_S6, D_O1 = :D_O1,
            P_00 = :P_00, P_01 = :P_01, P_06 = :P_06, P_26 = :P_26
            WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $field => $value) {
            if (is_null($value)) {
                $stmt->bindValue(":$field", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":$field", $value);
            }
        }

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }



    ////////////////////////////////////////////////////////////////////// generar excel  //////////////////////////////////

    public function datosCaptura($id_nomina){
        $stmt = $this->conn->prepare("SELECT c.*, n.tipo FROM captura c JOIN nominas n ON c.id_nomina = n.id WHERE c.id_nomina = :id_nomina");
        $stmt->execute([':id_nomina' => $id_nomina]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


///////////////////////////////////////////////////////////////////////// generar recibo ///////////////////////////////

   // Para todas
    public function getCapturaPorQuincenaSinJurisdiccion($quincena, $anio) {
        $stmt = $this->conn->prepare("SELECT * FROM captura WHERE QNA = ? AND AÑO = ? ORDER BY NOMBRE ASC");
        $stmt->execute([$quincena, $anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Para una jurisdicción específica
    public function getCapturaPorQuincena($quincena, $anio, $jurisdiccion) {
        $stmt = $this->conn->prepare("SELECT * FROM captura WHERE QNA = ? AND AÑO = ? AND JURIS = ? ORDER BY DESC_CT_DEPTO ASC, NOMBRE ASC");
        $stmt->execute([$quincena, $anio, $jurisdiccion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

///////////////////////////////////////// logs ///////////////////////////////////////////////////

    public function log_accion($id_usuario, $accion, $id_acciones) {
        $stmt = $this->conn->prepare("INSERT INTO logs (id_usuario, id_acciones, accion) VALUES (?, ?, ?)");
        return $stmt->execute([$id_usuario, $id_acciones, $accion]);
    }

////////////////////////////////////////// pension //////////////////////////////////////

    public function SavePension($datos) {
        $stmt = $this->conn->prepare("INSERT INTO pension (id_personal, beneficiaria, cuenta_beneficiaria, porcentaje, id_usuario) VALUES (?, ?, ?, ?, ?)");

        $porcentaje = $datos['porcentaje'] / 100;

        return $stmt->execute([
            $datos['id_personal'],
            $datos['beneficiaria'],
            $datos['cuenta_beneficiaria'],
            $porcentaje,
            $datos['id_usuario']
        ]);
    }

    public function getPensionById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pension WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePension($id, $datos) {
        $stmt = $this->conn->prepare("UPDATE pension 
            SET beneficiaria = ?, cuenta_beneficiaria = ?, porcentaje = ?, id_usuario = ? 
            WHERE id = ?");
        $porcentaje = $datos['porcentaje'] / 100; // almacena como decimal
        return $stmt->execute([
            $datos['beneficiaria'],
            $datos['cuenta_beneficiaria'],
            $porcentaje,
            $datos['id_usuario'],
            $id
        ]);
    }


/////////////////////////////////////////////////// tabla totales ///////////////////////////////////////

    public function getTablaTotales($qna, $anio, $programa, $rama){
    $sql = "
        SELECT 
            SUM(P_00) AS p_00,
            SUM(P_01) AS p_01,
            SUM(P_06) AS p_06,
            SUM(P_26) AS p_26,

            SUM(D_01) AS d_01,
            SUM(D_04) AS d_04,
            SUM(D_05) AS d_05,
            SUM(D_62) AS d_62,
            SUM(D_64) AS d_64,
            SUM(D_65) AS d_65,
            SUM(D_R1) AS d_r1,
            SUM(D_R2) AS d_r2,
            SUM(D_R3) AS d_r3,
            SUM(D_R4) AS d_r4,
            SUM(D_AS) AS d_as,
            SUM(D_AM) AS d_am,
            SUM(D_S1) AS d_s1,
            SUM(D_S2) AS d_s2,
            SUM(D_S4) AS d_s4,
            SUM(D_S5) AS d_s5,
            SUM(D_S6) AS d_s6,
            SUM(D_O1) AS d_o1,

            SUM(PERCEPCIONES) AS total_percepciones,
            SUM(DEDUCCIONES) AS total_deducciones,
            SUM(TOTAL_NETO) AS total_neto
        FROM captura
        WHERE QNA = :qna
          AND AÑO = :anio
          AND RECURSO = :programa
          AND RAMA = :rama
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':qna', $qna, PDO::PARAM_INT);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt->bindParam(':programa', $programa, PDO::PARAM_STR);
    $stmt->bindParam(':rama', $rama, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//////////////////////////////////////////////// calculares las deducciones temporales //////////////////////////////////////////////////

    public function UpdateTemporales($quincena, $anio) {
        try {
            // 1. Obtener inicio y fin de la quincena
            $stmt = $this->conn->prepare("SELECT inicio, fin FROM quincenas WHERE id = ?");
            $stmt->execute([$quincena]);
            $quincenaData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$quincenaData) {
                throw new Exception("No se encontró la quincena $quincena en la tabla quincenas.");
            }

            $fechaInicio = DateTime::createFromFormat('d/m/Y', $quincenaData['inicio'].'/'.$anio)->format('Y-m-d');
            $fechaFin    = DateTime::createFromFormat('d/m/Y', $quincenaData['fin'].'/'.$anio)->format('Y-m-d');


            // 2. Buscar deducciones temporales dentro del rango
            $sql = "SELECT id_personal, concepto, monto
                    FROM deducciones_temporales
                    WHERE fecha_inicio <= :fechaFin
                    AND fecha_fin >= :fechaInicio
                    AND estado = 'ACTIVA'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            $stmt->execute();
            $deducciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Preparar statement dinámico para actualizar captura
            foreach ($deducciones as $ded) {
                $concepto = $ded['concepto']; // ejemplo: D_64
                $idPersonal = $ded['id_personal'];
                $monto = $ded['monto'];

                // Armar SQL dinámico para insertar el monto en la columna correcta
                $sqlUpdate = "UPDATE captura
                            SET $concepto = :monto
                            WHERE id_personal = :id_personal
                                AND QNA = :qna
                                AND AÑO = :anio";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':monto', $monto);
                $stmtUpdate->bindParam(':id_personal', $idPersonal, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':qna', $quincena, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':anio', $anio, PDO::PARAM_INT);
                $ok = $stmtUpdate->execute();

                if ($ok) {
                    $this->log_accion($_SESSION['user_id'] ?? 0, "UpdateTemporales exitoso: $concepto aplicado a personal $idPersonal", $idPersonal);
                } else {
                    $error = $stmtUpdate->errorInfo();
                    $this->log_accion($_SESSION['user_id'] ?? 0, "Error UpdateTemporales ($concepto, id_personal=$idPersonal): {$error[2]}", $idPersonal);
                }
            }

            return true;
        } catch (Exception $e) {
            $this->log_accion($_SESSION['user_id'] ?? 0, "Excepción UpdateTemporales: ".$e->getMessage(), $quincena);
            return false;
        }
    }
}