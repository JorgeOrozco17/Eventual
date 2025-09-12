<?php
include 'seguridad.php';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme-mode="dark">
<head>
    <base href="" />
    <title>Nomina Eventual</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />\
	<base href="https://nomina.soed.mx/Eventual/">
    <meta property="og:locale" content="es_ES" />
    <meta property="og:type" content="article" />
    <link rel="shortcut icon" href="public/img/ss.jpg" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="//cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@6.4.2/css/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/public/css/styles.css">
    <!-- Reemplaza con esta versión adaptada a Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">


	<style>
        body{
            background-color: #CFCFCF;
        }
		.menu-icon i {
			font-size: 1.5rem;
		}

		.menu-title {
			margin-top: 5px;
			font-size: 1rem;
			font-weight: bold;
		}

		.menu-tittle{
			margin-top: 50px;
			font-size: 2.5rem;
			font-weight: bold;
		}

        .text-muted{
            font-size: 1.1rem;
        }
        .text-dark{
            margin-left: 50px;
        }

		.menu-accordion{
			margin: 6px 0;
		}
    
        h2 {
            color: #333333;
        }
        h3 {
            color: #333333;
        }
		h4 {
			color: #333333;
		}
        p {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: medium;
            color: rgb(0, 0, 0);
        }
        input[type="date"] {
        background-color:rgb(73, 73, 73);
        color: #D9D9D9;
        border: 1px solid #ccc;
        }

        label {
            color:#333333
        } 

		.card {
			padding: 20px;
			box-shadow: 7px 8px 7px rgba(0, 0, 0, 0.5);
		}
		.table, 
		.table th, 
		.table td, 
		.table tr {
			border: none !important;
		}

		.table > thead > tr > th {
			border-bottom: 1px solid #e5e5e5 !important; /* deja solo la raya debajo de los encabezados */
		}

		.table > tbody > tr {
			border-bottom: none !important;
		}
	</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body data-kt-app-sidebar-minimize="on" id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<script>
    var defaultThemeMode = "dark";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        // if (themeMode === "system") {
        // 	themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        // }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
</script>
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
        <!-- Header (Navbar) -->
        <div id="kt_app_header" class="app-header">
            <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
                <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                        <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                    <a href="#" class="d-lg-none">
                        <img alt="Logo" src="assets/media/logos/salud-08.png" class="h-30px" />
                    </a>
                </div>
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 mb-5 mb-lg-0">
                        <h1 class="page-heading d-flex fw-bold fs-3 flex-column justify-content-center my-0">
                            Administración de Nóminas Eventuales
                        </h1>
                    </div>
                    <div class="app-navbar flex-shrink-0">
            <!--Fin menu tema-->
            <!--begin::User menu-->
                    <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                        <!--begin::Menu wrapper-->
                        <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            <img alt="Foto" src="/public/img/ss.jpg" class="rounded-circle" style="object-fit: cover;" />
                        </div>
                        <!--begin::User account menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Foto" src="/public/img/ss.jpg" class="rounded-circle" style="object-fit: cover;" />
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Username-->
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold d-flex align-items-center fs-5"><?php echo $usuario; ?>
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"><?php echo $rol; ?></span></div>
                                    </div>
                                    <!--end::Username-->
                                </div>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator my-2"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item tema-->
                            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                <a href="#" class="menu-link">
                                    <span class="menu-link">Mode 
                                    <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                        <i class="ki-duotone ki-night-day theme-light-show fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                            <span class="path7"></span>
                                            <span class="path8"></span>
                                            <span class="path9"></span>
                                            <span class="path10"></span>
                                        </i>
                                    </span></span>
                                </a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-0">
                                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                            <span class="menu-icon" data-kt-element="icon">
                                                <i class="ki-duotone ki-night-day fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                    <span class="path6"></span>
                                                    <span class="path7"></span>
                                                    <span class="path8"></span>
                                                    <span class="path9"></span>
                                                    <span class="path10"></span>
                                                </i>
                                            </span>
                                            <span class="menu-link px-5">Light</span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-0">
                                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                            <span class="menu-icon" data-kt-element="icon">
                                                <i class="ki-duotone ki-moon fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span class="menu-link px-5">Dark</span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                </div>
                                <!--end::Menu-->
                            </div>
                            <!--end::Menu item tema-->
                            <!--begin::Menu item user config-->
                            <div class="menu-item px-5 my-1">
                                <a href="cuenta.php?id=<?php echo $user_id; ?>" class="menu-link">
                                    <span class="menu-link">Configurar cuenta</span>
                                </a>
                            </div>
                            <!--end::Menu item  user config-->
                            <!--begin::Menu item cerrar sesion-->
                            <div class="menu-item px-5">
                                <a href="logout.php" class="menu-link">
									<span class="menu-link">Cerrar sesión</span></a>
                            </div>
                            <!--end::Menu item cerrar sesion-->
                        </div>
                        <!--end::User account menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::User menu-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Wrapper principal -->
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
            <!-- Sidebar -->
            <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
						<!--begin::Logo-->
						<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
							<!--begin::Logo image-->
							<a href="index.php">
								<img alt="Logo" src="public/img/logoss.jpg" class="h-50px app-sidebar-logo-default"  style="border-radius: 10px; margin-left: 10px;" />
								<img alt="Logo" src="assets/media/logos/salud-08.png" class="h-50px app-sidebar-logo-minimize"  style="border-radius: 5px;" />
							</a>
							<!--end::Logo image-->
							<!--begin::Sidebar toggle-->
							<div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
								<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</div>
							<!--end::Sidebar toggle-->
						</div>
						<!--end::Logo-->
						<!--begin::sidebar menu-->
						<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
							<!--begin::Menu wrapper-->
							<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
								<!--begin::Scroll wrapper-->
								<div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
									<!--begin::Menu-->
									<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
										<!--begin:Menu item-->
										<div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">
											<!--begin:Menu link-->
											<span class="menu-link">
												<span class="menu-icon">
													<i class="ki-duotone ki-element-11 fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
													</i> 
												</span>
												<span class="menu-title">Dashboards</span>
												<span class="menu-arrow"></span>
											</span>
											<!--end:Menu link-->
											<!--begin:Menu sub  dashboards-->
											<div class="menu-sub menu-sub-accordion">
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link active" href="index.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Default</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
											</div>
											<!--end:Menu sub dashboards-->
										</div>
										<!--end:Menu item-->



										<!--begin:Menu item         titulo de menu-->
										<div class="menu-item pt-5">
											<!--begin:Menu content-->
											<div class="menu-content">
												<span class="menu-heading fw-bold text-uppercase fs-7">Pages</span>
											</div>
											<!--end:Menu content-->
										</div>
										<!--end:Menu item            titulo de menu-->


										<!--begin:Menu item  catalogos -------------------------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<a class="menu-link" href="catalogos.php">
												<span class="menu-icon">
													<i class="fab fa-buffer"></i>
												</span>
												<span class="menu-title">Catalogos</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item   catalogos -------------------------------------->


										<!--begin:Menu item   altas y bajas de personal---------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<span class="menu-link">
												<span class="menu-icon">
                                                    <i class="fas fa-user-plus"></i>
												</span>
												<span class="menu-title">Personal</span>
												<span class="menu-arrow"></span>
											</span>
											<!--end:Menu link-->
											<!--begin:Menu sub-->
											<div class="menu-sub menu-sub-accordion">
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="personalform.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Alta de personal</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="altapersonal.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Gestionar personal Activo</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="autorizapersonal.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Personal sin autorizar</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="contratos.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Contratos</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="archivo.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Archivo Digital</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
											</div>
											<!--end:Menu sub-->
										</div>
										<!--end:Menu item   altas y bajas de personal --------------------->


										<!--begin:Menu item  Captura --------------------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<span class="menu-link">
												<span class="menu-icon">
													<i class="fas fa-hand-holding-dollar"></i>
												</span>
												<span class="menu-title">Captura</span>
												<span class="menu-arrow"></span>
											</span>
											<!--end:Menu link-->
											<!--begin:Menu sub-->
											<div class="menu-sub menu-sub-accordion">
												<!--begin:Menu item      gestion de nominas-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="nomina.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Gestion de nominas</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item          gestion de nominas-->
												<!--begin:Menu item          pension-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="pension.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Gestion de pensiones</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item           pension-->
												<!--begin:Menu item           Recibos-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="recibos.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Imprimir formatos de nomina</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item               Recibos-->
											</div>
											<!--end:Menu sub-->
										</div>
										<!--end:Menu item     Capptura ----------------------------------->


										<!--begin:Menu item          Ausencias--------------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<span class="menu-link">
												<span class="menu-icon">
													<i class="far fa-calendar-xmark"></i>
												</span>
												<span class="menu-title">Ausencias</span>
												<span class="menu-arrow"></span>
											</span>
											<!--end:Menu link-->
											<!--begin:Menu sub-->
											<div class="menu-sub menu-sub-accordion">
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="Incidencias.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Incidencias</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="licencias.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Licencias</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
											</div>
											<!--end:Menu sub-->
										</div>
										<!--end:Menu item            Ausencias--------------------------->


										<!--begin:Menu item  Reportes -------------------------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<a class="menu-link" href="reportes.php">
												<span class="menu-icon">
													<i class="fas fa-book-open"></i>
												</span>
												<span class="menu-title">Reportes</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item   Reportes -------------------------------------->

                                        <!--begin:Menu item  catalogos -------------------------------------->
										<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
											<!--begin:Menu link-->
											<span class="menu-link">
												<span class="menu-icon">
													<i class="fas fa-gear"></i>
												</span>
												<span class="menu-title">Configuraciones</span>
												<span class="menu-arrow"></span>
											</span>
											<!--end:Menu link-->
											<!--begin:Menu sub-->
											<div class="menu-sub menu-sub-accordion">
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="permisos.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Permisos</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
												<!--begin:Menu item-->
												<div class="menu-item">
													<!--begin:Menu link-->
													<a class="menu-link" href="nuevousuario.php">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
														<span class="menu-title">Usuarios del sistema</span>
													</a>
													<!--end:Menu link-->
												</div>
												<!--end:Menu item-->
											</div>
										</div>
										<!--end:Menu item   catalogos -------------------------------------->


									</div>
									<!--end::Menu-->
								</div>
								<!--end::Scroll wrapper-->
							</div>
							<!--end::Menu wrapper-->
						</div>
						<!--end::sidebar menu-->
						<!--begin::Footer
						<div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
							<a href="https://preview.keenthemes.com/html/metronic/docs" class="btn btn-flex flex-center btn-custom btn-primary overflow-hidden text-nowrap px-0 h-40px w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="200+ in-house components and 3rd-party plugins">
								<span class="btn-label">Docs & Components</span>
								<i class="ki-duotone ki-document btn-icon fs-2 m-0">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</a>
						</div>
						end::Footer-->
					</div>
					<!--end::Sidebar-->

            <!-- Contenedor de contenido dinámico -->
            <div class="app-main flex-column flex-row-fluid">
                <div class="app-content flex-column-fluid">
                    <div class="app-container container-fluid">
                        <!-- El contenido de las páginas se insertará aquí -->