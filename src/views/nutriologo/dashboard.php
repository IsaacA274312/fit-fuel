<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../../public/index.html');
    exit;
}
// permitir sólo nutriólogos (o administradores)
if (!isset($_SESSION['tipo_usuario']) || !in_array(strtolower($_SESSION['tipo_usuario']), ['nutriologo','admin'])) {
    header('Location: ../../public/index.html');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel de nutriólogo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
  <style>
    body{background:#1a1a1a;color:#fff;min-height:100vh}
    /* Forzar todo el texto a blanco */
    *{color:#fff!important}
    .text-muted,.text-secondary,.text-primary,.text-success,.text-danger,.text-warning,.text-info{color:#fff!important}
    .small,.badge,small,h1,h2,h3,h4,h5,h6,p,span,div,td,th,li,a,label,input,textarea,select,button{color:#fff!important}
    .nutri-stat{background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(255,255,255,0.02));padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.05);text-align:center}
    .nutri-stat h3{margin:0;font-size:26px;color:#fff;font-weight:700}
    .nutri-stat p{margin:6px 0 0;font-size:11px;color:#fff}
    .sidebar{background:linear-gradient(180deg, rgba(30,30,30,1), rgba(20,20,20,1));border-radius:12px;padding:18px;border:1px solid rgba(255,255,255,0.05)}
    .nav-pills .nav-link{color:#fff;margin-bottom:6px;padding:8px 12px;font-size:13px}
    .nav-pills .nav-link.active{background:#3b82f6}
    .panel{background:rgba(255,255,255,0.02);padding:20px;border-radius:10px;border:1px solid rgba(255,255,255,0.04)}
    .table-dark{--bs-table-bg:#1a1a1a;--bs-table-border-color:rgba(255,255,255,0.1)}
    .table-dark th{background:rgba(255,255,255,0.05);font-size:12px;padding:8px}
    .table-dark td{font-size:12px;padding:8px}
  </style>
</head>
<body>
  <div class="container-fluid py-4" style="max-width:1800px">
    <div class="row g-3">
      <div class="col-lg-2 col-md-3">
        <div class="sidebar sticky-top" style="top:20px">
          <div class="text-center mb-4">
            <img src="../../public/images/logo.jpg" alt="logo" class="img-fluid mb-3" style="max-width:100px;border-radius:12px">
            <h5 class="mb-1">Nutriólogo: <?php echo htmlspecialchars($_SESSION['nombre']); ?></h5>
            <p class="small text-muted mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
          </div>
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link active" data-section="dashboard" href="#">📊 Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="plans" href="#">🥗 Planes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="create" href="#">📝 Crear Plan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="clients" href="#">👥 Clientes</a>
            </li>
          </ul>
          <div class="d-grid gap-2 mt-3">
            <a href="../../public/index.html" class="btn btn-outline-secondary btn-sm">Inicio</a>
            <button id="logoutLink" class="btn btn-danger btn-sm">Cerrar sesión</button>
          </div>
        </div>
      </div>

      <div class="col-lg-10 col-md-9">
        <main>
          <!-- Dashboard Overview -->
          <section id="section-dashboard">
            <div class="card bg-dark text-white mb-3 border-secondary">
              <div class="card-body">
                <h2 class="card-title mb-1">Panel de Nutriólogo</h2>
                <p class="card-text small text-muted">Gestiona planes nutricionales y seguimiento</p>
              </div>
            </div>
            
            <div class="row g-3 mb-3">
              <div class="col-xl col-lg-3 col-md-6">
                <div class="nutri-stat">
                  <h3 id="stat-clientes">0</h3>
                  <p>Clientes</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="nutri-stat">
                  <h3 id="stat-planes">0</h3>
                  <p>Planes</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="nutri-stat">
                  <h3 id="stat-consultas">0</h3>
                  <p>Consultas este mes</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="nutri-stat">
                  <h3 id="stat-activos">0</h3>
                  <p>Planes activos</p>
                </div>
              </div>
            </div>
          </section>

          <!-- Planes -->
          <section id="section-plans" style="display:none">
            <div class="card bg-dark text-white border-secondary" style="max-width:100%">
              <div class="card-body">
                <h2 class="card-title mb-3">Planes Alimenticios</h2>
                <div id="planes-container" class="table-responsive">
                  <div class="text-center text-muted py-3">
                    <small>Cargando planes...</small>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Crear Plan -->
          <section id="section-create" style="display:none">
            <div class="card bg-dark text-white border-secondary" style="max-width:100%">
              <div class="card-body">
                <h2 class="card-title mb-3">Crear Plan Nutricional</h2>
                <form id="planForm">
                  <div class="mb-3">
                    <label for="ptitle" class="form-label">Título</label>
                    <input id="ptitle" name="title" type="text" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label for="pdesc" class="form-label">Descripción</label>
                    <textarea id="pdesc" name="description" rows="3" class="form-control"></textarea>
                  </div>
                  <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Guardar plan</button>
                    <button type="button" class="btn btn-secondary" id="clearPlan">Limpiar</button>
                  </div>
                  <div id="planMsg" class="mt-2"></div>
                </form>
              </div>
            </div>
          </section>

          <!-- Clientes -->
          <section id="section-clients" style="display:none">
            <div class="card bg-dark text-white border-secondary" style="max-width:100%">
              <div class="card-body">
                <h2 class="card-title mb-3">Mis Clientes</h2>
                <div id="clientes-container" class="table-responsive">
                  <div class="text-center text-muted py-3">
                    <small>Cargando clientes...</small>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>
  </div>

  <script>
    // Logout
    document.getElementById('logoutLink').addEventListener('click', async function(e){
      e.preventDefault();
      const res = await fetch('../../public/logout.php',{method:'POST'});
      const j = await res.json(); if(j.success) window.location='../../public/index.html';
    });

    // Cargar datos del nutriólogo
    async function loadNutriologoData() {
      try {
        const res = await fetch('../../public/api/nutriologo-stats.php');
        const data = await res.json();
        
        if (data.success) {
          // Actualizar estadísticas
          document.getElementById('stat-clientes').textContent = data.stats.clientes;
          document.getElementById('stat-planes').textContent = data.stats.planes;
          document.getElementById('stat-consultas').textContent = data.stats.consultas;
          document.getElementById('stat-activos').textContent = data.stats.activos;

          // Guardar datos para las demás secciones
          window.nutriologoData = data;
          
          // Cargar planes
          loadPlanes(data.planes);
          
          // Cargar clientes
          loadClientes(data.clientes);
        } else {
          console.error('Error al cargar datos:', data.error);
        }
      } catch (err) {
        console.error('Error en la petición:', err);
      }
    }

    function loadPlanes(planes) {
      const container = document.getElementById('planes-container');
      
      if (!planes || planes.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3"><small>No tienes planes creados.</small></div>';
        return;
      }

      container.innerHTML = `
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Cliente</th>
              <th>Objetivo</th>
              <th>Calorías</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            ${planes.map(p => {
              const inicio = p.fecha_inicio ? new Date(p.fecha_inicio).toLocaleDateString() : '-';
              const fin = p.fecha_fin ? new Date(p.fecha_fin).toLocaleDateString() : '-';
              return `
                <tr>
                  <td>${p.nombre}</td>
                  <td>${p.cliente_nombre} ${p.cliente_apellido}</td>
                  <td>${p.objetivo || '-'}</td>
                  <td>${p.calorias_diarias || '-'}</td>
                  <td>${inicio}</td>
                  <td>${fin}</td>
                  <td>
                    <span class="badge ${p.activo == 1 ? 'bg-success' : 'bg-secondary'}">
                      ${p.activo == 1 ? 'Activo' : 'Inactivo'}
                    </span>
                  </td>
                </tr>
              `;
            }).join('')}
          </tbody>
        </table>
      `;
    }

    function loadClientes(clientes) {
      const container = document.getElementById('clientes-container');
      
      if (!clientes || clientes.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3"><small>No tienes clientes asignados.</small></div>';
        return;
      }

      container.innerHTML = `
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Email</th>
              <th>Planes Totales</th>
              <th>Último Plan</th>
            </tr>
          </thead>
          <tbody>
            ${clientes.map(c => {
              const ultimoPlan = c.ultimo_plan ? new Date(c.ultimo_plan).toLocaleDateString() : 'Nunca';
              return `
                <tr>
                  <td>${c.nombre} ${c.apellido}</td>
                  <td>${c.email}</td>
                  <td>${c.planes_totales || 0}</td>
                  <td>${ultimoPlan}</td>
                </tr>
              `;
            }).join('')}
          </tbody>
        </table>
      `;
    }

    // Cargar datos al inicio
    loadNutriologoData();

    // Navegación
    document.querySelectorAll('.nav-link[data-section]').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        const section = link.dataset.section;
        document.querySelectorAll('section[id^="section-"]').forEach(s => s.style.display = 'none');
        const targetSection = document.getElementById('section-' + section);
        targetSection.style.display = 'block';
        
        anime({
          targets: targetSection,
          translateX: [50, 0],
          opacity: [0, 1],
          duration: 600,
          easing: 'easeOutExpo'
        });
      });
    });

    // Animaciones de entrada
    anime({
      targets: '.sidebar',
      translateX: [-50, 0],
      opacity: [0, 1],
      duration: 800,
      easing: 'easeOutExpo'
    });

    anime({
      targets: '.nutri-stat',
      scale: [0.8, 1],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(100, {start: 300}),
      easing: 'easeOutElastic(1, .6)'
    });

    // Plan form
    document.getElementById('planForm').addEventListener('submit', async function(e){
      e.preventDefault();
      const btn = this.querySelector('button[type="submit"]');
      btn.disabled=true;
      const msg = document.getElementById('planMsg');
      msg.textContent='Guardando...';
      await new Promise(r=>setTimeout(r,500));
      msg.style.color='lightgreen';
      msg.textContent='Plan guardado (simulado).';
      btn.disabled=false;
      this.reset();
    });
    
    document.getElementById('clearPlan').addEventListener('click', ()=>{
      document.getElementById('planForm').reset();
      document.getElementById('planMsg').textContent='';
    });
  </script>
</body>
</html>
