<?php
// index.php — Dashboard interactivo con gráficas y datos reales recientes
// Asume que header.php maneja sesión y permisos
require_once 'app/models/dbconexion.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$db = new DBConexion();
$conn = $db->getConnection();

// Catálogos
$quincenas = $catalogo->getAllQuincenas();
$jurisdicciones = $catalogo->getAllJurisdicciones();
$anios = range((int)date('Y') - 1, (int)date('Y') + 1);
$user_juris = $_SESSION['jurisdiccion'] ?? 9; // 9 = todas en tu sistema

function pick(array $row, array $keys, $default=null){ foreach($keys as $k){ if(isset($row[$k]) && $row[$k] !== '') return $row[$k]; } return $default; }
function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ---------- Utilidades de tiempo / quincena actual ----------
$today = new DateTime('now');
$month = (int)$today->format('n');
$day = (int)$today->format('j');
$qnaActual = ($day <= 15) ? ($month * 2 - 1) : ($month * 2); // 1..24
$anioActual = (int)$today->format('Y');

// ---------- Consultas para KPIs y Gráficas (tolerantes a errores) ----------
$kpiEmpleadosActivos = '—';
$kpiLicenciasActuales = '—';
$serieNomina = [];
$serieFaltasLic = [];
$pieAdscripciones = [];
$ultimosMovs = [];

if ($conn) {
    try {
        // KPI: Empleados activos
        $stmt = $conn->query("SELECT COUNT(*) FROM personal WHERE estatus = 'activo'");
        $kpiEmpleadosActivos = (int)$stmt->fetchColumn();

        // KPI: Licencias en la quincena actual (sumando días)
        $stmt = $conn->prepare("SELECT COALESCE(SUM(dias),0) FROM faltas WHERE tipo='licencia' AND quincena = ? AND año = ?");
        $stmt->execute([$qnaActual, $anioActual]);
        $kpiLicenciasActuales = (int)$stmt->fetchColumn();

        // Serie Nómina (últimas 8 quincenas con datos en captura)
        $stmt = $conn->query("SELECT AÑO AS anio, QNA AS quincena, 
                                     COALESCE(SUM(TOTAL_NETO),0) AS total_neto,
                                     COALESCE(SUM(PERCEPCIONES),0) AS percepciones,
                                     COALESCE(SUM(DEDUCCIONES),0) AS deducciones,
                                     COUNT(*) AS empleados
                              FROM captura
                              GROUP BY AÑO, QNA
                              ORDER BY anio DESC, quincena DESC
                              LIMIT 8");
        $serieNomina = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $serieNomina = array_reverse($serieNomina); // para graficar cronológicamente

        // Serie Faltas vs Licencias (últimas 8 quincenas)
        $stmt = $conn->query("SELECT año, quincena,
                                     SUM(CASE WHEN tipo='falta' THEN COALESCE(dias,1) ELSE 0 END) AS faltas_dias,
                                     SUM(CASE WHEN tipo='licencia' THEN COALESCE(dias,1) ELSE 0 END) AS licencias_dias
                              FROM faltas
                              GROUP BY año, quincena
                              ORDER BY año DESC, quincena DESC
                              LIMIT 8");
        $serieFaltasLic = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $serieFaltasLic = array_reverse($serieFaltasLic);

        // Pie: Empleados activos por adscripción (top 6)
        $stmt = $conn->query("SELECT COALESCE(adscripcionnombre, CONCAT('Juris ', adscripcion)) AS etiqueta,
                                     COUNT(*) AS total
                              FROM personal
                              WHERE estatus='activo'
                              GROUP BY adscripcionnombre, adscripcion
                              ORDER BY total DESC
                              LIMIT 6");
        $pieAdscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Últimos movimientos (mix: faltas y licencias más recientes)
        $stmt = $conn->query("SELECT id, nombre, jurisdiccion, quincena, año, tipo, dias, fechas, NOW() AS ts
                              FROM faltas
                              ORDER BY id DESC
                              LIMIT 10");
        $ultimosMovs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        // si algo falla, mantenemos placeholders
    }
}

?>

<style>
    body { background-color: #D9D9D9 !important; }
    .search-input, select{ color: #3d3c3cff; }
    .dashboard-wrapper { max-width: 1280px; margin: 2rem auto; padding: 0 1rem; }
    .search-bar { display: flex; gap: .75rem; align-items: center; }
    .search-input { flex: 1; border: 1px solid #ced4da; border-radius: .75rem; padding: .75rem 1rem; background: #fff; }
    .kpis { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 1rem; margin: 1rem 0 2rem; }
    .kpi-card { background: #fff; border-radius: 1rem; padding: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,.06); display: flex; flex-direction: column; gap: .25rem; }
    .kpi-title { font-size: .9rem; color: #6c757d; }
    .kpi-value { font-size: 1.4rem; font-weight: 700; color: #333; }
    .panel { background: #fff; border-radius: 1rem; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
    .panel-header { padding: 1rem 1.25rem; border-bottom: 1px solid #efefef; display: flex; justify-content: space-between; align-items: center; }
    .panel-title { font-size: 1.1rem; font-weight: 600; color: #333; }
    .panel-body { padding: 1rem 1.25rem; }
    .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .module-card { position: relative; background: #fff; border: 1px solid #f0f0f0; border-radius: 1rem; padding: 1rem; transition: transform .08s ease, box-shadow .08s ease; }
    .module-card:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.08); }
    .module-title { font-weight: 600; color: #333; margin: .25rem 0 .5rem; }
    .module-desc { color: #6c757d; font-size: .95rem; }
    .module-actions { display: flex; gap: .5rem; margin-top: .75rem; }
    .btn-soft { border: 1px solid #e9ecef; background: #f8f9fa; color: #333; padding: .5rem .75rem; border-radius: .75rem; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; }
    .btn-soft:hover { background: #eef2f5; }
    .fav-btn { position: absolute; top: .5rem; right: .5rem; border: none; background: transparent; padding: .25rem; cursor: pointer; }
    .fav-btn[aria-pressed="true"] .star { color: #f7b500; }
    .star { font-size: 1.1rem; color: #c8c8c8; }
    .actions-row { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: .75rem; }
    .action-box { background: #fff; border-radius: .75rem; border: 1px dashed #e3e6ea; padding: .75rem; display: flex; flex-direction: column; gap: .5rem; }
    .action-box label { font-size: .85rem; color: #6c757d; }
    .action-box select, .action-box input[type="number"] { width: 100%; border: 1px solid #ced4da; border-radius: .5rem; padding: .5rem .6rem; background: #fff; }
    .action-buttons { display: flex; gap: .5rem; }
    .btn-primary-soft { background: #0d6efd; color: #fff; border: none; padding: .6rem .9rem; border-radius: .6rem; text-decoration: none; }
    .btn-primary-soft:hover { filter: brightness(0.95); }
    .charts { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; }
    .subcharts { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .table-responsive { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: .5rem .6rem; border-bottom: 1px solid #eee; font-size: .92rem; }
    th { text-align: left; color: #6c757d; font-weight: 600; }
    @media (max-width: 1200px) { .charts { grid-template-columns: 1fr; } }
    @media (max-width: 992px) { .kpis { grid-template-columns: repeat(2, minmax(0,1fr)); } .grid { grid-template-columns: repeat(2, minmax(0,1fr)); } .actions-row { grid-template-columns: 1fr; } }
    @media (max-width: 620px) { .kpis { grid-template-columns: 1fr; } .grid { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrapper">
    <!-- Búsqueda + Acciones rápidas -->
    <div class="panel" role="region" aria-label="Búsqueda y acciones">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-magnifying-glass"></i> Inicio rápido</div>
            <div style="font-size:.9rem;color:#6c757d">Atajo: presiona <kbd>/</kbd> para buscar</div>
        </div>
        <div class="panel-body">
            <div class="search-bar">
                <input id="buscador_modulos" class="search-input" type="search" placeholder="Buscar módulos, acciones…" autocomplete="off">
                <a class="btn-soft" href="personal.php"><i class="fas fa-user-plus"></i><span>Nuevo empleado</span></a>
                <a class="btn-soft" href="licencias.php"><i class="fas fa-calendar-plus"></i><span>Registrar licencia</span></a>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="kpis" aria-label="Indicadores">
        <div class="kpi-card"><div class="kpi-title">Empleados activos</div><div class="kpi-value" id="kpi-empleados"><?php echo esc($kpiEmpleadosActivos); ?></div></div>
        <div class="kpi-card"><div class="kpi-title">Quincena actual</div><div class="kpi-value" id="kpi-quincena"><?php echo esc(str_pad((string)$qnaActual,2,'0',STR_PAD_LEFT)); ?></div></div>
        <div class="kpi-card"><div class="kpi-title">Año</div><div class="kpi-value"><?php echo esc($anioActual); ?></div></div>
    </div>

    <!-- Módulos -->
    <div class="panel" role="region" aria-label="Módulos del sistema">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-grid-2"></i> Módulos</div>
            <div style="font-size:.9rem;color:#6c757d" id="favoritos-info">Tus favoritos primero</div>
        </div>
        <div class="panel-body">
            <div id="modules-grid" class="grid">
                <?php
                $modulos = [
                    ['id'=>'personal','titulo'=>'Personal','desc'=>'Gestionar personal y expedientes','href'=>'personal.php','icon'=>'fa-users','categoria'=>'gestion'],
                    ['id'=>'licencias','titulo'=>'Faltas y Licencias','desc'=>'Registrar y consultar faltas/licencias','href'=>'licencias.php','icon'=>'fa-calendar-xmark','categoria'=>'captura'],
                    ['id'=>'captura','titulo'=>'Captura','desc'=>'Pensiones, deducciones, etc.','href'=>'menu_captura.php','icon'=>'fa-keyboard','categoria'=>'captura'],
                    ['id'=>'contratos','titulo'=>'Contratos','desc'=>'Generar y administrar contratos','href'=>'contratos.php','icon'=>'fa-file-signature','categoria'=>'documentos'],
                    ['id'=>'nomina','titulo'=>'Nómina','desc'=>'Procesos por quincena y cierre','href'=>'nomina.php','icon'=>'fa-sack-dollar','categoria'=>'nomina'],
                    ['id'=>'reportes','titulo'=>'Reportes','desc'=>'Reportes y exportaciones','href'=>'reportes.php','icon'=>'fa-chart-line','categoria'=>'reportes'],
                ];
                foreach ($modulos as $m): ?>
                    <div class="module-card" data-module-id="<?php echo esc($m['id']); ?>" data-categoria="<?php echo esc($m['categoria']); ?>">
                        <button class="fav-btn" type="button" aria-label="Favorito" aria-pressed="false">
                            <i class="fas fa-star star"></i>
                        </button>
                        <div style="font-size:1.4rem;color:#0d6efd"><i class="fas <?php echo esc($m['icon']); ?>"></i></div>
                        <div class="module-title"><?php echo esc($m['titulo']); ?></div>
                        <div class="module-desc"><?php echo esc($m['desc']); ?></div>
                        <div class="module-actions">
                            <a class="btn-soft" href="<?php echo esc($m['href']); ?>"><i class="fas fa-arrow-right"></i> Abrir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <br>

    <!-- Acciones por periodo -->
    <div class="panel" role="region" aria-label="Acciones por periodo">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-calendar"></i> Acciones por periodo</div>
        </div>
        <div class="panel-body">
            <div class="actions-row">
                <div class="action-box">
                    <label for="sel-quincena">Quincena</label>
                    <select id="sel-quincena">
                        <option value="">—</option>
                        <?php if (is_array($quincenas)) {
                            foreach ($quincenas as $q) {
                                $num = is_array($q) ? pick($q, ['quincena','qna','numero','id','id_quincena']) : $q;
                                if ($num === null || $num === '') continue;
                                $label = str_pad((string)$num, 2, '0', STR_PAD_LEFT);
                                echo '<option value="'.esc($num).'">'.esc($label).'</option>';
                            }
                        } else { for ($i=1;$i<=24;$i++) echo '<option value="'.$i.'">'.str_pad((string)$i,2,'0',STR_PAD_LEFT).'</option>'; } ?>
                    </select>
                </div>
                <div class="action-box">
                    <label for="sel-anio">Año</label>
                    <select id="sel-anio"><?php foreach ($anios as $y) { echo '<option value="'.esc($y).'"'.($y==(int)date('Y')?' selected':'').'>'.esc($y).'</option>'; } ?></select>
                </div>
            </div>
            <div class="action-buttons" style="margin-top:.75rem;">
                <a id="btn-ir-nomina" class="btn-primary-soft" href="nomina.php"><i class="fas fa-dollar-sign"></i> Ir a nómina</a>
                <a id="btn-ir-licencias" class="btn-soft" href="licencias.php"><i class="fas fa-calendar-day"></i> Ver licencias</a>
            </div>
        </div>
    </div>

    <br>

    <!-- Gráficas -->
    <div class="panel" role="region" aria-label="Visualizaciones">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-chart-line"></i> Visualizaciones (últimas quincenas)</div>
        </div>
        <div class="panel-body charts">
            <div>
                <canvas id="chartNomina" height="140"></canvas>
            </div>
            <div class="subcharts">
                <canvas id="chartFaltasLic" height="140"></canvas>
                <canvas id="chartPieAds" height="160"></canvas>
            </div>
        </div>
    </div>

    <br>

    <!-- Últimos movimientos -->
    <div class="panel" role="region" aria-label="Recientes">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-clock-rotate-left"></i> Últimos registros (faltas/licencias)</div>
        </div>
        <div class="panel-body table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Juris</th>
                        <th>Qna</th>
                        <th>Año</th>
                        <th>Tipo</th>
                        <th>Días</th>
                        <th>Fechas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ultimosMovs): $i=1; foreach ($ultimosMovs as $r): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo esc($r['nombre']); ?></td>
                            <td><?php echo esc($r['jurisdiccion']); ?></td>
                            <td><?php echo esc(str_pad((string)$r['quincena'],2,'0',STR_PAD_LEFT)); ?></td>
                            <td><?php echo esc($r['año'] ?? $r['anio'] ?? ''); ?></td>
                            <td><?php echo esc($r['tipo']); ?></td>
                            <td><?php echo esc($r['dias']); ?></td>
                            <td><?php echo esc($r['fechas']); ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="8" style="color:#6c757d">No hay registros recientes.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function(){
  // --- Búsqueda rápida ---
  const input = document.getElementById('buscador_modulos');
  const grid = document.getElementById('modules-grid');
  const cards = Array.from(grid.querySelectorAll('.module-card'));
  function filter(){ const q=(input.value||'').trim().toLowerCase(); cards.forEach(c=>{ const t=c.innerText.toLowerCase(); c.style.display = t.includes(q)?'':'none';}); }
  input.addEventListener('input', filter);
  window.addEventListener('keydown', (e)=>{ if (e.key === '/' && document.activeElement !== input) { e.preventDefault(); input.focus(); } });

  // --- Favoritos en localStorage ---
  const KEY_FAVS = 'dashboard_favoritos_v1';
  const favs = new Set(JSON.parse(localStorage.getItem(KEY_FAVS) || '[]'));
  function paintFavState(card){ const id=card.dataset.moduleId; const btn=card.querySelector('.fav-btn'); btn.setAttribute('aria-pressed', favs.has(id)?'true':'false'); }
  function reorder(){ const sorted = cards.slice().sort((a,b)=>{ const af=favs.has(a.dataset.moduleId)?0:1; const bf=favs.has(b.dataset.moduleId)?0:1; if(af!==bf) return af-bf; return a.dataset.moduleId.localeCompare(b.dataset.moduleId);}); sorted.forEach(el=>grid.appendChild(el)); }
  cards.forEach(card=>{ const id=card.dataset.moduleId; paintFavState(card); card.querySelector('.fav-btn').addEventListener('click', ()=>{ if (favs.has(id)) favs.delete(id); else favs.add(id); localStorage.setItem(KEY_FAVS, JSON.stringify(Array.from(favs))); paintFavState(card); reorder(); }); });
  reorder();

  // --- Acciones por periodo (construye URLs según selección) ---
const selQ=document.getElementById('sel-quincena');
const selY=document.getElementById('sel-anio');
const btnNom=document.getElementById('btn-ir-nomina');
const btnLic=document.getElementById('btn-ir-licencias');

function updateLinks(){
  const q=selQ.value;
  const y=selY.value;
  const qp=new URLSearchParams();

  if(q) qp.set('qna', q);
  if(y) qp.set('anio', y);

  const qs=qp.toString();
  btnNom.href='nomina.php'+(qs?('?' + qs):'');
  btnLic.href='licencias.php'+(qs?('?' + qs.replace('qna','quincena')):'');
}

[selQ,selY].forEach(el=>el.addEventListener('change', updateLinks));
updateLinks();


  // --- Datos PHP -> JS ---
  const serieNomina = <?php echo json_encode($serieNomina ?: [], JSON_UNESCAPED_UNICODE); ?>;
  const serieFaltasLic = <?php echo json_encode($serieFaltasLic ?: [], JSON_UNESCAPED_UNICODE); ?>;
  const pieAds = <?php echo json_encode($pieAdscripciones ?: [], JSON_UNESCAPED_UNICODE); ?>;

  // --- Chart Nómina (línea) ---
  try {
    const ctxN = document.getElementById('chartNomina').getContext('2d');
    const labelsN = serieNomina.map(r => `${String(r.quincena).padStart(2,'0')}/${r.anio}`);
    const dataNeto = serieNomina.map(r => Number(r.total_neto||0));
    const dataPer = serieNomina.map(r => Number(r.percepciones||0));
    const dataDed = serieNomina.map(r => Number(r.deducciones||0));
    new Chart(ctxN, {
      type: 'line',
      data: {
        labels: labelsN,
        datasets: [
          { label: 'Total neto', data: dataNeto },
          { label: 'Percepciones', data: dataPer },
          { label: 'Deducciones', data: dataDed },
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        interaction: { mode: 'index', intersect: false },
        scales: { y: { ticks: { callback: (v)=> new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(v) } } }
      }
    });
  } catch(e){}

  // --- Chart Faltas vs Licencias (barras) ---
  try {
    const ctxF = document.getElementById('chartFaltasLic').getContext('2d');
    const labelsF = serieFaltasLic.map(r => `${String(r.quincena).padStart(2,'0')}/${r.año ?? r.anio}`);
    const dataFaltas = serieFaltasLic.map(r => Number(r.faltas_dias||0));
    const dataLic = serieFaltasLic.map(r => Number(r.licencias_dias||0));
    new Chart(ctxF, {
      type: 'bar',
      data: { labels: labelsF, datasets: [ { label: 'Faltas (días)', data: dataFaltas }, { label: 'Licencias (días)', data: dataLic } ] },
      options: { responsive: true, plugins: { legend: { position:'bottom' } }, scales: { x: { stacked:false }, y:{ beginAtZero:true } } }
    });
  } catch(e){}

  // --- Chart Empleados por Adscripción (dona) ---
  try {
    const ctxP = document.getElementById('chartPieAds').getContext('2d');
    const labelsP = pieAds.map(r => r.etiqueta);
    const dataP = pieAds.map(r => Number(r.total||0));
    new Chart(ctxP, { type:'doughnut', data:{ labels: labelsP, datasets:[{ data: dataP }] }, options:{ plugins:{ legend:{ position:'bottom' } } } });
  } catch(e){}
})();
</script>

<?php include 'footer.php'; ?>
