<?php
require_once 'app/controllers/PersonalController.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$controller = new PersonalController();
$catalogo = new CatalogoController();

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>No se especificó el ID del personal.</div>";
    exit;
}

$id = $_GET['id'];
$personal = $controller->getPersonalById($id);
$archivos = $catalogo->getArchivosById($id);

// Si hay más de un resultado (por error en modelo), tomamos el primero:
if (isset($archivos[0])) {
    $archivos = $archivos[0];
}

if (!$personal) {
    echo "<div class='alert alert-warning'>No se encontró el registro solicitado.</div>";
    exit;
}
?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
    h2 {
    color: #1e293b;
    font-weight: bold;
    letter-spacing: 1px;
}
.card {
    background: rgba(255,255,255,0.8);
    box-shadow: 0 8px 24px rgba(0,0,0,0.09), 0 1.5px 6px rgba(0,0,0,0.02);
    border-radius: 24px;
    border: none;
    padding: 26px 28px 24px 28px;
    margin-bottom: 28px;
    backdrop-filter: blur(8px);
    transition: box-shadow 0.25s, background 0.2s;
}
.card:hover {
    background: rgba(240,246,255,0.95);
    box-shadow: 0 12px 30px rgba(80,80,160,0.18);
}
.card-header {
    background: transparent;
    border-bottom: none;
    margin-bottom: 16px;
}
.menu-title h2 {
    margin-bottom: 0;
}
.archivo-card {
    background: rgba(248,250,255, 0.75);
    border-radius: 16px;
    border: 1.5px solid #e0e7ef;
    padding: 18px 14px 16px 18px;
    margin-bottom: 18px;
    box-shadow: 0 2px 8px rgba(30,41,59,0.06);
    transition: box-shadow 0.18s, transform 0.15s;
    position: relative;
}
.archivo-card:hover {
    box-shadow: 0 8px 24px rgba(66,120,246,0.11);
    transform: translateY(-4px) scale(1.01);
}
.archivo-label {
    color: #26324b;
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 0.2px;
}
.badge.bg-success {
    background: linear-gradient(90deg,#3ed670 60%,#179e4a 100%);
    color: #fff;
    padding: 0.43em 0.95em;
    border-radius: 100px;
    font-weight: 500;
    font-size: 0.91rem;
    margin-right: 0.7em;
    box-shadow: 0 1px 6px rgba(50,205,113,0.15);
}
a.archivo-label {
    text-decoration: none;
    color: #2571fc;
    font-weight: 500;
    word-break: break-all;
    transition: color 0.18s;
}
a.archivo-label:hover {
    color: #fd7e14;
    text-decoration: underline;
}
input[type="file"].form-control {
    background: rgba(255,255,255,0.86);
    border: 1.2px solid #b8c1ec;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(100,150,255,0.04);
    padding: 7px;
    font-size: 0.95em;
    cursor: pointer;
    margin-top: 6px;
    margin-bottom: 0;
}
input[type="file"].form-control:hover {
    border-color: #4f8bff;
}
.btn-primary, .btn-info {
    border-radius: 22px;
    font-weight: 600;
    letter-spacing: 0.3px;
    padding: 10px 22px;
    border: none;
    transition: background 0.18s, box-shadow 0.16s;
    box-shadow: 0 1.5px 7px rgba(80,130,255,0.08);
}

.btn-primary:hover {
    background: linear-gradient(90deg,#2257a8 30%,#24c2ed 100%);
}
.btn-info:hover {
    background: linear-gradient(90deg,#1c927c 40%,#2257a8 100%);
    color: #fff;
}
@media (max-width: 768px) {
    .archivo-card { padding: 11px 6px 10px 11px; }
    .card { padding: 13px 7px 15px 7px; }
}
</style>

<div class="container mt-4">

   <div class="regreso">
        <span class="menu-title">
            <a class="menu-link" href="personal.php"> <span class="menu-tittle">Personal</span></a> 
            <a class="menu-link" href="altapersonal.php"><span class="menu-tittle">/Gestionar personal</span></a>
            <span class="menu-tittle">/Archivo digital del Empleado</span>
        </span>
    </div>

    <div class="regreso">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="menu-title">
                <h2>Empleado: <?= htmlspecialchars($personal['nombre_alta']) ?></h2>
            </div>
            <div class="card-toolbar">
                <a href="generar_pdf_archivos.php?id=<?= $id ?>" class="btn btn-sm btn-info me-2" target="_blank">
                    <i class="fas fa-file-pdf"></i> Descargar PDF Completo
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="guardar_archivos.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_personal" value="<?= $id ?>">

                <div class="row">
                <?php
                

                $nombres_campos = [
                    'autorizacion' => 'Autorizacion',
                    'baja' => 'Baja',
                    'Solicitud' => 'Solicitud',
                    'curriculum' => 'Currículum',
                    'acta_nacimiento' => 'Acta de Nacimiento',
                    'curp' => 'CURP',
                    'cartilla_militar' => 'Cartilla Militar',
                    'certificado_estudios' => 'Certificado de Estudios',
                    'titulo_profesional' => 'Título Profesional',
                    'cedula_profesional' => 'Cédula Profesional',
                    'certificacion_cedula' => 'Certificación de Cédula',
                    'titulo_cedula_especialidad' => 'Título/Cédula Especialidad',
                    'cursos_capacitacion' => 'Cursos de Capacitación',
                    'ine' => 'INE',
                    'domicilio' => 'Comprobante de Domicilio',
                    'cartas_recomendacion' => 'Cartas de Recomendación',
                    'carta_protesta' => 'Carta Protesta',
                    'compatibilidad_horario' => 'Compatibilidad de Horario',
                    'carta_compromiso' => 'Carta Compromiso',
                    'certificado_medico' => 'Certificado Médico',
                    'no_antecedentes' => 'No Antecedentes',
                    'no_inhabilitado' => 'No Inhabilitado',
                    'situacion_fiscal' => 'Situación Fiscal',
                    'acuse_declaracion' => 'Acuse Declaración',
                    'comprobante_banco' => 'Comprobante de Banco',
                ];
                
                $campos_bloqueados = ['autorizacion', 'baja'];

                foreach ($nombres_campos as $campo => $label):
                ?>
                    <div class="col-md-6">
                        <div class="archivo-card">
                            <h6 class="archivo-label"><?= $label ?>:</h6>
                            <?php if (!empty($archivos[$campo])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success">Archivo subido</span>
                                    <a class="archivo-label" href="uploads/<?= htmlspecialchars($archivos[$campo]) ?>" target="_blank">
                                        <?= htmlspecialchars($archivos[$campo]) ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php if (in_array($campo, $campos_bloqueados)): ?>
                                    <input type="file" class="form-control mt-2" disabled>
                                    <span class="text-muted small ms-2" style="color:#888!important;">
                                        <i class="fas fa-lock"></i> Solo personal autorizado puede subir este documento.
                                    </span>
                                <?php else: ?>
                                    <input type="file" name="<?= $campo ?>" class="form-control mt-2">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary mt-4" onclick="return confirm('Una vez cargados los archvivos NO se podran borra o modificar. ¿Desea guardar los cambios?')">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
