    <?php
    require_once 'dbconexion.php';

    class UserModel {

        private $conn;

        public function __construct() {
            $db = new DBConexion();
            $this->conn = $db->getConnection();
        }

        public function getAll() {
            $stmt = $this->conn->prepare("
            SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            JOIN roles r ON u.rol = r.id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById($id) {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function save($data) {
            try {
                if (!empty($data['id'])) {
                    // Editar usuario
                    if (!empty($data['contraseña'])) {
                        $stmt = $this->conn->prepare("UPDATE usuarios SET Nombre = ?, usuario = ?, contraseña = ?, rol = ?, juris = ? WHERE id = ?");
                        $hashed = password_hash($data['contraseña'], PASSWORD_DEFAULT);
                        return $stmt->execute([$data['Nombre'], $data['usuario'], $hashed, $data['rol'], $data['juris'], $data['id']]);
                    } else {
                        $stmt = $this->conn->prepare("UPDATE usuarios SET Nombre = ?, usuario = ?, rol = ?, juris = ? WHERE id = ?");
                        return $stmt->execute([$data['Nombre'], $data['usuario'], $data['rol'], $data['juris'], $data['id']]);
                    }
                } else {
                    // Nuevo usuario
                    if (empty($data['contraseña'])) {
                        throw new Exception("La contraseña es obligatoria para crear un nuevo usuario.");
                    }
        
                    $stmt = $this->conn->prepare("INSERT INTO usuarios (Nombre, usuario, contraseña, rol, juris) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$data['Nombre'], $data['usuario'], $data['contraseña'], $data['rol'], $data['juris']]);
                    return true;
                }
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]);
                return false;
            }
        }
        

        public function delete($id) {
            $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = ?");
            return $stmt->execute([$id]);
        }

            public function existByUsuario($usuario){
            $stmt = $this->conn->prepare("SELECT 1 FROM usuarios WHERE usuario = ?");
            $stmt -> execute([$usuario]);
            return $stmt->fetchColumn() !==false;
        }


        public function getRolbyid($id){
            $stmt = $this->conn->prepare("SELECT nombre FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getPaginas(){
            $stmt = $this->conn->prepare("SELECT * FROM paginas");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllRoles() {
            $stmt = $this->conn->prepare("SELECT * FROM roles");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

         // Obtener los permisos actuales para un rol y por usuario
        public function getPermisosPorRol($rol_id) {
            $stmt = $this->conn->prepare("SELECT id_pagina FROM permisos_rol WHERE id_rol = ?");
            $stmt->execute([$rol_id]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_pagina');
        }

        public function getPermisosPorUsuario($id_usuario) {
            $stmt = $this->conn->prepare("SELECT id_pagina FROM permisos_paginas WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_pagina');
        }

        // Guardar permisos para un rol y por usuario
        public function guardarPermisosRol($rol_id, $paginas) {
            // Eliminar permisos actuales
            $this->conn->prepare("DELETE FROM permisos_rol WHERE id_rol = ?")->execute([$rol_id]);
            // Insertar nuevos permisos
            $stmt = $this->conn->prepare("INSERT INTO permisos_rol (id_rol, id_pagina) VALUES (?, ?)");
            foreach ($paginas as $id_pag) {
                $stmt->execute([$rol_id, intval($id_pag)]);
            }
            return true;
        }

        public function guardarPermisosUsuario($id_usuario, $paginas) {
            // Borra permisos actuales
            $this->conn->prepare("DELETE FROM permisos_paginas WHERE id_usuario = ?")->execute([$id_usuario]);
            // Inserta los nuevos
            $stmt = $this->conn->prepare("INSERT INTO permisos_paginas (id_pagina, id_usuario) VALUES (?, ?)");
            foreach ($paginas as $id_pagina) {
                $stmt->execute([$id_pagina, $id_usuario]);
            }
        }

        public function getPaginaIdByArchivo($archivo) {
            $stmt = $this->conn->prepare("SELECT id FROM paginas WHERE ruta = ?");
            $stmt->execute([$archivo]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'] ?? null;
        }

        public function usuarioTienePermiso($user_id, $id_pagina) {
            $stmt = $this->conn->prepare("SELECT 1 FROM permisos_paginas WHERE id_usuario = ? AND id_pagina = ?");
            $stmt->execute([$user_id, $id_pagina]);
            return $stmt->fetchColumn() ? true : false;
        }

        public function rolTienePermiso($rol_id, $id_pagina) {
            $stmt = $this->conn->prepare("SELECT 1 FROM permisos_rol WHERE id_rol = ? AND id_pagina = ?");
            $stmt->execute([$rol_id, $id_pagina]);
            return $stmt->fetchColumn() ? true : false;
        }

////////////////////////////////////////////////////// Responsables de jurisdiccion /////////////////////////////

        public function getResponsablesBYRH($responsable) {
            $stmt = $this->conn->prepare("
            SELECT r.*, 
                u.Nombre, 
                CASE 
                    WHEN r.id_centro = 0 THEN 'Jurisdicción'
                    ELSE c.nombre
                END AS nombre_centro,
                j.nombre AS nombre_juris
            FROM responsables r
            JOIN usuarios u 
                ON r.rh_responsable = u.id
            LEFT JOIN centros c 
                ON r.id_centro = c.id
            JOIN jurisdicciones j 
                ON r.id_juris = j.id
            WHERE r.rh_responsable = :responsable");
            $stmt->execute(['responsable' => $responsable]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllResponsables() {
            $stmt = $this->conn->prepare("
            SELECT 
                r.*,  
                CASE 
                    WHEN r.id_centro = 0 THEN 'Jurisdicción'
                    ELSE c.nombre
                END AS nombre_centro,
                j.nombre AS nombre_juris
            FROM responsables r
            LEFT JOIN centros c 
                ON r.id_centro = c.id
            JOIN jurisdicciones j 
                ON r.id_juris = j.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getRespobsableByJurisdiccion($juris){
            $stmt = $this->conn->prepare("
            SELECT r.*, u.Nombre
            FROM responsables r
            JOIN usuarios u ON r.rh_responsable = u.id
            WHERE r.id_juris = 8
              AND r.id_centro = 0
            LIMIT 1");
            $stmt->execute([$juris]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getResponsableBycentro($juris){
            
        }
        
    }