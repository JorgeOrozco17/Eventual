                    </div> <!-- Cierre de #kt_app_content -->
                </div> <!-- Cierre de app-content -->
            </div> <!-- Cierre de app-main -->
        </div> <!-- Cierre de app-wrapper -->
    </div> <!-- Cierre de app-page -->
</div> <!-- Cierre de app-root -->


<!-- Pie de página (ajustado para evitar solapamiento) -->
<div class="kt-app__footer ms-lg-225px"> <!-- Margen izquierdo en desktop -->
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        <div class="text-dark order-2 order-md-1">
            
        </div>
        
        <div class="order-1 order-md-2">
            <p href="http://www.pricoahuila.org/" target="_blank" class="text-muted fw-semibold me-1">SECRETARÍA DE SALUD COAHUILA</>
            <span class="text-muted fw-semibold me-1">2025 &copy;</span>
            <span class="text-muted">Coded with ❤</span>
        </div>
    </div>
</div>

<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->
		<script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
		<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="assets/js/widgets.bundle.js"></script>
		<script src="assets/js/custom/widgets.js"></script>
		<script src="assets/js/custom/apps/chat/chat.js"></script>
		<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
		<script src="assets/js/custom/utilities/modals/create-app.js"></script>
		<script src="assets/js/custom/utilities/modals/users-search.js"></script>

		<!-- Scripts globales -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@6.4.2/js/mdb.min.js"></script>

		<!-- DataTables + Bootstrap 5 integration JS -->
		<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

		<script src="public/js/script.js"></script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->


		<script>
			console.log("juris usuario: <?= $_SESSION['juris'] ?>")
		</script>
	</body>
</html>
<?php
ob_end_flush();
?>
