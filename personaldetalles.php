<?php
require_once 'app/controllers/personalController.php';
include 'header.php';

$controller = new PersonalController();

// Verificar que venga el ID
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>No se especificó el personal a mostrar.</div>";
    exit;
}

$id = $_GET['id'];
$personal = $controller->getPersonalById($id);
$observacion = $controller->getComentsById($id);
$coments = $controller->getComentsById($id);


if (!$personal) {
    echo "<div class='alert alert-warning'>No se encontró el registro solicitado.</div>";
    exit;
}
?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    .card {
        background: #ffffff;
        border-radius: 10px;
        padding: 30px;
        margin-top: 30px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card-title {
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
        font-weight: bold;
    }
    .detail-label {
        font-weight: bold;
        color: #555;
    }
    .detail-value {
        color: #333;
    }
    .detail-coment {
        color: #555;
        background-color:rgba(225, 161, 255, 0.84);
        padding: 6px;
    }
    .regreso {
        margin-top: 10px;
    }
</style>

<div class="container">

    <div class="regreso">
        <span class="menu-title">
            <a class="menu-link" href="personal.php"> <span class="menu-tittle">Personal</span></a> 
            <a class="menu-link" href="altapersonal.php"><span class="menu-tittle">/Gestionar personal</span></a>
            <span class="menu-tittle">/Detalles del Empleado</span>
        </span>
    </div>
    <div class="regreso">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

<!--begin::Basic info-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h4 class="fw-bold m-0">Detalles del personal: <?= htmlspecialchars($personal ['nombre_alta'])?></h3>
        </div>

        <div class="card-title justify-content-end">
            
            <a style="margin-left: 8px;" href="archivodetalle.php?id=<?= urlencode($personal['id']) ?>" class="btn btn-primary">
                <i class="fas fa-folder-open"></i> <span>Archivo Digital</span>
            </a>

            <a style="margin-left: 8px;" href="personalform.php?id=<?= urlencode($personal['id']) ?>" class="btn btn-primary">
                <i class="fas fa-circle-info"></i> <span>Editar informacion</span>
            </a>

            <a style="margin-left: 8px;" href="altaxbaja.php?id=<?= urlencode($personal['id']) ?>" class="btn btn-primary">
                <i class="fas fa-arrow-right-arrow-left"></i> <span>Alta por baja</span>
            </a>

            <a style="margin-left: 8px;" href="bajaform.php?id=<?= urlencode($personal['id']) ?>" class="btn btn-primary">
                <i class="fas fa-arrow-down-long"></i> <span>Baja</span>
            </a>
        </div>
        
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->
    <!--begin::Content-->
    <div id="kt_account_settings_profile_details" class="collapse show">
        <!--begin::Form-->
        <form id="kt_account_profile_details_form" class="form">
            <!--begin::Card body-->
            <div class="card-body border-top p-9">
                 
                <div class="row">
                <div class="col-md-6">
                    <!--end::Input group-->
                    <!--begin::Input group      Estatus-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Estatus</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['estatus'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group       Oficio-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Oficio</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['oficio'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group        Puesto-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-2 col-form-label required fw-semibold fs-6">Puesto</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['puesto'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group         programa -->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-2 col-form-label required fw-semibold fs-6">Programa</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['programa'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                 </div>
                </div>

                <!--begin::Input group             Adscripcion-->
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-2 col-form-label required fw-semibold fs-6">Adscripcion</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9 fv-row">
                        <input type="text" name="company" class="form-control form-control-lg form-control-solid" placeholder="Company name" value="<?= htmlspecialchars($personal['adscripcion'] . ' ' . $personal['centro']) ?>" readonly />
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Input group-->

                <div class="row">
                <div class="col-md-6">
                    <!--begin::Input group            RFC-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">RFC</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['RFC'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group           Sueldo Neto-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Sueldo Neto</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= "$" . number_format($personal ['sueldo_neto'], 2,'.',',')?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group          sueldo bruto-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Sueldo bruto</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= "$" . number_format($personal ['sueldo_bruto'], 2, '.', ',')?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group          Observaciones-->
                    <div class="row mb-6">
                        <div class="col-12">
                            <label class="form-label fw-bold fs-4">Observaciones generales</label>
                            <textarea class="form-control form-control-lg" rows="3" readonly><?= htmlspecialchars($personal['observaciones_alta']) ?></textarea>
                        </div>
                    </div>
                    <!--end::Input group-->
                    
                </div>
                <div class="col-md-6">
                    <!--end::Input group-->
                    <!--begin::Input group          Fecha baja -->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-2 col-form-label required fw-semibold fs-6">Fecha baja</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="<?= htmlspecialchars($personal ['fecha_baja'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group          Quincena alta-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-2 col-form-label required fw-semibold fs-6">QNA Alta</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="<?= htmlspecialchars($personal ['quincena_alta'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group          Inicio de contratacion-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-2 col-form-label required fw-semibold fs-6">Inicio</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="<?= htmlspecialchars($personal ['inicio_contratacion'])?>" readonly />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>

                    <!--begin::Input group          Observaciones del usuario-->
                    <div class="row mb-6">
                        <div class="col-12">
                            <label class="form-label fw-bold fs-4">Observaciones del usuario</label>
                            <textarea class="form-control form-control-lg" rows="3" readonly><?= htmlspecialchars($coments['comentario']) ?></textarea>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                </div>

            </div>
            <!--end::Card body-->
            <!--begin::Actions-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save Changes</button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
<!--end::Basic info-->


<?php include 'footer.php'; ?>