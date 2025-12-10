<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../../public/index.html');
    exit;
}
// permitir sólo instructores (o administradores)
if (!isset($_SESSION['tipo_usuario']) || !in_array(strtolower($_SESSION['tipo_usuario']), ['instructor','admin'])) {
    header('Location: ../../public/index.html');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel de instructor</title>
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
    .instructor-stat{background:linear-gradient(135deg, rgba(139,195,74,0.15), rgba(255,255,255,0.02));padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.05);text-align:center}
    .instructor-stat h3{margin:0;font-size:26px;color:#fff;font-weight:700}
    .instructor-stat p{margin:6px 0 0;font-size:11px;color:#fff}
    .sidebar{background:linear-gradient(180deg, rgba(30,30,30,1), rgba(20,20,20,1));border-radius:12px;padding:18px;border:1px solid rgba(255,255,255,0.05)}
    .nav-pills .nav-link{color:#fff;margin-bottom:6px;padding:8px 12px;font-size:13px}
    .nav-pills .nav-link.active{background:#8bc34a}
    .panel{background:rgba(255,255,255,0.02);padding:20px;border-radius:10px;border:1px solid rgba(255,255,255,0.04)}
    .table-dark{--bs-table-bg:#1a1a1a;--bs-table-border-color:rgba(255,255,255,0.1)}
    .table-dark th{background:rgba(255,255,255,0.05);font-size:12px;padding:8px}
    .table-dark td{font-size:12px;padding:8px}
    .card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(139,195,74,0.3)}
    .accordion-button:not(.collapsed){color:#fff;background:rgba(139,195,74,0.1)}
    .accordion-button:focus{box-shadow:0 0 0 0.25rem rgba(139,195,74,0.25)}
    #diasTabs .nav-link{background:rgba(255,255,255,0.05);color:#fff;border:1px solid rgba(255,255,255,0.1);margin-right:4px;margin-bottom:4px;font-size:12px;padding:6px 12px}
    #diasTabs .nav-link:hover{background:rgba(139,195,74,0.2)}
    #diasTabs .nav-link.active{background:#8bc34a;color:#fff;font-weight:600;border-color:#8bc34a}
  </style>
</head>
<body>
  <div class="container-fluid py-4" style="max-width:1800px">
    <div class="row g-3">
      <div class="col-lg-2 col-md-3">
        <div class="sidebar sticky-top" style="top:20px">
          <div class="text-center mb-4">
            <img src="../../public/images/logo.jpg" alt="logo" class="img-fluid mb-3" style="max-width:100px;border-radius:12px">
            <h5 class="mb-1">Instructor: <?php echo htmlspecialchars($_SESSION['nombre']); ?></h5>
            <p class="small text-muted mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
          </div>
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link active" data-section="dashboard" href="#">📊 Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="clients" href="#">👥 Mis Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="create" href="#">📝 Crear Rutina</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="asignadas" href="#">✅ Rutinas Asignadas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="progreso" href="#">📈 Progreso Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="schedule" href="#">📅 Programación</a>
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
                <h2 class="card-title mb-1">Panel de Instructor</h2>
                <p class="card-text small text-muted">Gestiona tus clientes, rutinas y monitorea el progreso</p>
              </div>
            </div>
            
            <div class="row g-3 mb-3">
              <div class="col-xl col-lg-3 col-md-6">
                <div class="instructor-stat">
                  <h3 id="stat-clientes">0</h3>
                  <p>Clientes Activos</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="instructor-stat">
                  <h3 id="stat-rutinas">0</h3>
                  <p>Rutinas Creadas</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="instructor-stat">
                  <h3 id="stat-activas">0</h3>
                  <p>Rutinas Activas</p>
                </div>
              </div>
              <div class="col-xl col-lg-3 col-md-6">
                <div class="instructor-stat">
                  <h3 id="stat-actividad">0</h3>
                  <p>Activos esta semana</p>
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="card bg-dark text-white border-secondary h-100">
                  <div class="card-header bg-transparent border-secondary">
                    <h5 class="mb-0">Rutinas Más Asignadas</h5>
                  </div>
                  <div class="card-body">
                    <div id="rutinas-populares-container">
                      <div class="text-center text-muted py-3">
                        <small>Cargando...</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card bg-dark text-white border-secondary h-100">
                  <div class="card-header bg-transparent border-secondary">
                    <h5 class="mb-0">Últimos Clientes Asignados</h5>
                  </div>
                  <div class="card-body">
                    <div id="ultimos-clientes-container">
                      <div class="text-center text-muted py-3">
                        <small>Cargando...</small>
                      </div>
                    </div>
                  </div>
                </div>
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

          <!-- Crear Rutina -->
          <section id="section-create" style="display:none">
            <div class="card bg-dark text-white border-secondary" style="max-width:100%">
              <div class="card-body">
                <h2 class="card-title mb-3">Crear Nueva Rutina</h2>
                
                <!-- Paso 1: Información básica -->
                <div id="step1-info">
                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="rtitle" class="form-label">Nombre de la Rutina</label>
                      <input id="rtitle" type="text" class="form-control" placeholder="Ej: Hipertrofia Full Body" required>
                    </div>
                    <div class="col-md-3">
                      <label for="rnivel" class="form-label">Nivel</label>
                      <select id="rnivel" class="form-select">
                        <option value="principiante">Principiante</option>
                        <option value="intermedio" selected>Intermedio</option>
                        <option value="avanzado">Avanzado</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="rduracion" class="form-label">Duración (semanas)</label>
                      <input id="rduracion" type="number" class="form-control" value="8" min="1" max="52">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="rdesc" class="form-label">Descripción</label>
                    <textarea id="rdesc" rows="2" class="form-control" placeholder="Describe el objetivo de esta rutina..."></textarea>
                  </div>
                  <button type="button" class="btn btn-primary" onclick="irAPaso2()">
                    Siguiente: Seleccionar Ejercicios →
                  </button>
                </div>

                <!-- Paso 2: Semanario de ejercicios -->
                <div id="step2-exercises" style="display:none">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Asignar Ejercicios por Día</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="volverAPaso1()">
                      ← Volver
                    </button>
                  </div>

                  <!-- Tabs para días de la semana -->
                  <ul class="nav nav-pills mb-3" id="diasTabs">
                    <li class="nav-item">
                      <button class="nav-link active" data-dia="lunes" onclick="cambiarDia('lunes')">Lunes</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="martes" onclick="cambiarDia('martes')">Martes</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="miercoles" onclick="cambiarDia('miercoles')">Miércoles</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="jueves" onclick="cambiarDia('jueves')">Jueves</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="viernes" onclick="cambiarDia('viernes')">Viernes</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="sabado" onclick="cambiarDia('sabado')">Sábado</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" data-dia="domingo" onclick="cambiarDia('domingo')">Domingo</button>
                    </li>
                  </ul>

                  <!-- Contenedor de ejercicios por día -->
                  <div id="ejercicios-dia-container" class="mb-3">
                    <!-- Se llenará dinámicamente -->
                  </div>

                  <!-- Botones de acción -->
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="guardarRutina()">
                      💾 Guardar Rutina Completa
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="limpiarRutina()">
                      🗑️ Limpiar Todo
                    </button>
                  </div>
                  <div id="routineMsg" class="mt-3"></div>
                </div>
              </div>
            </div>
          </section>

          <!-- Programación -->
          <section id="section-schedule" style="display:none">
            <div class="card bg-dark text-white border-secondary" style="max-width:100%">
              <div class="card-body">
                <h2 class="card-title mb-3">📅 Programación de Clases</h2>
                <div id="programacion-container">
                  <div class="text-center text-muted py-3">
                    <small>Funcionalidad en desarrollo - Aquí podrás programar clases grupales</small>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Rutinas Asignadas -->
          <section id="section-asignadas" style="display:none">
            <div class="card bg-dark text-white border-secondary">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h2 class="card-title mb-0">✅ Rutinas Asignadas a Clientes</h2>
                  <button class="btn btn-sm btn-primary" onclick="cargarRutinasAsignadas()">
                    🔄 Actualizar
                  </button>
                </div>
                <div id="rutinas-asignadas-container">
                  <div class="text-center text-muted py-4">
                    <div class="spinner-border text-success" role="status">
                      <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 small">Cargando rutinas asignadas...</p>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Progreso de Clientes -->
          <section id="section-progreso" style="display:none">
            <div class="card bg-dark text-white border-secondary">
              <div class="card-body">
                <h2 class="card-title mb-3">📈 Progreso de Mis Clientes</h2>
                <div class="mb-3">
                  <label class="form-label small">Selecciona un cliente:</label>
                  <select id="cliente-progreso-select" class="form-select bg-dark text-white" onchange="cargarProgresoCliente(this.value)">
                    <option value="">-- Selecciona un cliente --</option>
                  </select>
                </div>
                <div id="progreso-cliente-container">
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Selecciona un cliente para ver su progreso
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
    // Variables y funciones globales
    let ejerciciosDisponibles = [];
    let semanarioEjercicios = {
      lunes: [], martes: [], miercoles: [], jueves: [], viernes: [], sabado: [], domingo: []
    };
    let diaActual = 'lunes';

    // Funciones globales para onclick
    function irAPaso2() {
      const nombre = document.getElementById('rtitle').value.trim();
      if (!nombre) {
        alert('Por favor ingresa un nombre para la rutina');
        return;
      }
      
      document.getElementById('step1-info').style.display = 'none';
      document.getElementById('step2-exercises').style.display = 'block';
      
      // Renderizar el primer día
      renderizarDia('lunes');
    }

    function volverAPaso1() {
      document.getElementById('step2-exercises').style.display = 'none';
      document.getElementById('step1-info').style.display = 'block';
    }

    function cambiarDia(dia) {
      diaActual = dia;
      document.querySelectorAll('#diasTabs .nav-link').forEach(btn => {
        btn.classList.remove('active');
      });
      event.target.classList.add('active');
      renderizarDia(dia);
    }

    function renderizarDia(dia) {
      const container = document.getElementById('ejerciciosDia');
      const ejercicios = semanarioEjercicios[dia] || [];
      
      if (ejercicios.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay ejercicios asignados para este día. Haz clic en "Agregar Ejercicio" para comenzar.</p>';
        return;
      }
      
      container.innerHTML = ejercicios.map((ej, idx) => `
        <div class="card bg-dark mb-2">
          <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>${ej.nombre}</strong>
                <small class="text-muted d-block">${ej.series} series × ${ej.repeticiones} reps | ${ej.descanso_segundos}s descanso</small>
                ${ej.notas ? `<small class="text-info">${ej.notas}</small>` : ''}
              </div>
              <button class="btn btn-sm btn-outline-danger" onclick="eliminarEjercicioDia('${dia}', ${idx})">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </div>
          </div>
        </div>
      `).join('');
    }

    function eliminarEjercicioDia(dia, idx) {
      semanarioEjercicios[dia].splice(idx, 1);
      renderizarDia(dia);
    }

    // Logout
    document.getElementById('logoutLink')?.addEventListener('click', async function(e){
      e.preventDefault();
      const res = await fetch('../../public/logout.php',{method:'POST'});
      const j = await res.json(); if(j.success) window.location='../../public/index.html';
    });

    // Cargar datos del instructor
    async function loadInstructorData() {
      try {
        const res = await fetch('../../public/api/estadisticas-instructor.php');
        const data = await res.json();
        
        if (data.success && data.estadisticas) {
          const stats = data.estadisticas;
          
          // Actualizar estadísticas principales
          document.getElementById('stat-clientes').textContent = stats.total_clientes || 0;
          document.getElementById('stat-rutinas').textContent = stats.total_rutinas || 0;
          document.getElementById('stat-activas').textContent = stats.rutinas_activas || 0;
          document.getElementById('stat-actividad').textContent = stats.clientes_activos_7d || 0;
          
          // Mostrar rutinas más asignadas
          mostrarRutinasPopulares(stats.rutinas_mas_asignadas || []);
          
          // Mostrar últimos clientes
          mostrarUltimosClientes(stats.ultimos_clientes || []);
        }
      } catch (err) {
        console.error('Error al cargar datos del instructor:', err);
      }
    }

    // Mostrar rutinas más populares
    function mostrarRutinasPopulares(rutinas) {
      const container = document.getElementById('rutinas-populares-container');
      
      if (!rutinas || rutinas.length === 0) {
        container.innerHTML = '<p class="text-muted small">No hay rutinas asignadas aún</p>';
        return;
      }

      container.innerHTML = `
        <div class="list-group list-group-flush">
          ${rutinas.map((r, idx) => `
            <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1" style="color:#fff">${idx + 1}. ${r.nombre}</h6>
                <p class="mb-0 small text-muted">
                  <span class="badge bg-info me-1">${r.nivel || 'Intermedio'}</span>
                  ${r.activas} activa${r.activas === 1 ? '' : 's'} de ${r.total_asignaciones}
                </p>
              </div>
              <span class="badge bg-success rounded-pill">${r.total_asignaciones}</span>
            </div>
          `).join('')}
        </div>
      `;
    }

    // Mostrar últimos clientes asignados
    function mostrarUltimosClientes(clientes) {
      const container = document.getElementById('ultimos-clientes-container');
      
      if (!clientes || clientes.length === 0) {
        container.innerHTML = '<p class="text-muted small">No hay clientes asignados aún</p>';
        return;
      }

      container.innerHTML = `
        <div class="list-group list-group-flush">
          ${clientes.map(c => {
            const fecha = new Date(c.fecha_asignacion);
            const diasAtras = Math.floor((new Date() - fecha) / (1000 * 60 * 60 * 24));
            let tiempoTexto = '';
            
            if (diasAtras === 0) {
              tiempoTexto = 'Hoy';
            } else if (diasAtras === 1) {
              tiempoTexto = 'Ayer';
            } else if (diasAtras < 7) {
              tiempoTexto = `Hace ${diasAtras} días`;
            } else if (diasAtras < 30) {
              tiempoTexto = `Hace ${Math.floor(diasAtras / 7)} semana${Math.floor(diasAtras / 7) === 1 ? '' : 's'}`;
            } else {
              tiempoTexto = fecha.toLocaleDateString('es-MX');
            }
            
            return `
              <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" 
                       style="width: 30px; height: 30px; font-size: 12px;">
                    ${c.nombre.charAt(0)}${c.apellido.charAt(0)}
                  </div>
                  <div>
                    <h6 class="mb-0">${c.nombre} ${c.apellido}</h6>
                  </div>
                </div>
                <small class="text-muted">${tiempoTexto}</small>
              </div>
            `;
          }).join('')}
        </div>
      `;
    }

    async function verRutinaDetalle(rutinaId) {
      try {
        const res = await fetch(`../../public/api/rutina-detalle.php?id=${rutinaId}`);
        const data = await res.json();
        
        if (data.success) {
          mostrarModalRutina(data.rutina, data.ejercicios);
        } else {
          alert('Error al cargar rutina: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar la rutina');
      }
    }

    function mostrarModalRutina(rutina, ejercicios) {
      const modalHTML = `
        <div class="modal fade" id="rutinaModal" tabindex="-1">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <div>
                  <h5 class="modal-title" style="color:#fff">${rutina.nombre}</h5>
                  <p class="small text-muted mb-0">${rutina.descripcion || 'Sin descripción'}</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                ${ejercicios.length === 0 ? 
                  '<div class="text-center text-muted py-4">Esta rutina no tiene ejercicios asignados</div>' :
                  `<div class="row g-3">
                    ${ejercicios.map((e, idx) => `
                      <div class="col-12">
                        <div class="card bg-dark border-secondary">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-md-4">
                                <div class="position-relative" style="background:#000;border-radius:8px;overflow:hidden;min-height:200px">
                                  ${e.gif_url ? 
                                    `<img src="${e.gif_url}" alt="${e.nombre}" class="img-fluid" style="width:100%;height:auto;display:block" onerror="this.src='https://via.placeholder.com/400x300?text=Sin+Imagen'">` :
                                    `<div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                      <div class="text-center">
                                        <i class="bi bi-image" style="font-size:3rem"></i>
                                        <p class="small mt-2">Sin animación</p>
                                      </div>
                                    </div>`
                                  }
                                </div>
                              </div>
                              <div class="col-md-8">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                  <div>
                                    <h6 class="mb-1" style="color:#fff">
                                      <span class="badge bg-secondary me-2">${e.orden}</span>
                                      ${e.nombre}
                                    </h6>
                                    <span class="badge bg-info me-2">${e.grupo_muscular || 'General'}</span>
                                    <span class="badge bg-warning">${e.dificultad || 'intermedio'}</span>
                                  </div>
                                </div>
                                <p class="small text-muted mb-2">${e.descripcion || ''}</p>
                                <div class="row g-2 mb-2">
                                  <div class="col-auto">
                                    <div class="bg-secondary rounded px-2 py-1">
                                      <small><strong>Series:</strong> ${e.series || 3}</small>
                                    </div>
                                  </div>
                                  <div class="col-auto">
                                    <div class="bg-secondary rounded px-2 py-1">
                                      <small><strong>Reps:</strong> ${e.repeticiones || '8-12'}</small>
                                    </div>
                                  </div>
                                  <div class="col-auto">
                                    <div class="bg-secondary rounded px-2 py-1">
                                      <small><strong>Descanso:</strong> ${e.descanso_segundos || 60}s</small>
                                    </div>
                                  </div>
                                </div>
                                ${e.instrucciones ? `
                                  <div class="accordion accordion-flush" id="instrucciones${idx}">
                                    <div class="accordion-item bg-dark border-0">
                                      <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-dark text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${idx}">
                                          <small>📋 Ver instrucciones</small>
                                        </button>
                                      </h2>
                                      <div id="collapse${idx}" class="accordion-collapse collapse" data-bs-parent="#instrucciones${idx}">
                                        <div class="accordion-body small">
                                          <pre class="mb-0" style="color:#fff;white-space:pre-wrap">${e.instrucciones}</pre>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                ` : ''}
                                ${e.notas ? `<p class="small text-info mb-0 mt-2"><strong>Nota:</strong> ${e.notas}</p>` : ''}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    `).join('')}
                  </div>`
                }
              </div>
              <div class="modal-footer border-success">
                <button type="button" class="btn btn-success" onclick="abrirModalAsignarRutina(${rutina.id})">
                  Asignar a Cliente
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remover modal anterior si existe
      const oldModal = document.getElementById('rutinaModal');
      if (oldModal) oldModal.remove();
      
      // Agregar nuevo modal
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      // Mostrar modal
      const modal = new bootstrap.Modal(document.getElementById('rutinaModal'));
      modal.show();
      
      // Animar entrada de ejercicios
      anime({
        targets: '#rutinaModal .card',
        translateY: [30, 0],
        opacity: [0, 1],
        duration: 600,
        delay: anime.stagger(100),
        easing: 'easeOutQuad'
      });
    }

    // Cargar datos al inicio
    loadInstructorData();

    // Cargar ejercicios disponibles
    async function cargarEjerciciosDisponibles() {
      try {
        const res = await fetch('../../public/api/obtener-ejercicios.php');
        const data = await res.json();
        if (data.success) {
          ejerciciosDisponibles = data.ejercicios;
        }
      } catch (err) {
        console.error('Error al cargar ejercicios:', err);
      }
    }

    cargarEjerciciosDisponibles();

    function cambiarDia(dia) {
      diaActual = dia;
      
      // Actualizar tabs activos
      document.querySelectorAll('#diasTabs .nav-link').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.dia === dia) {
          btn.classList.add('active');
        }
      });
      
      renderizarDia(dia);
    }

    function renderizarDia(dia) {
      const container = document.getElementById('ejercicios-dia-container');
      const ejerciciosDia = semanarioEjercicios[dia];
      
      let html = `
        <div class="card bg-dark border-secondary mb-3">
          <div class="card-header bg-success bg-opacity-25">
            <h6 class="mb-0">Ejercicios para ${dia.charAt(0).toUpperCase() + dia.slice(1)}</h6>
          </div>
          <div class="card-body">
            ${ejerciciosDia.length === 0 ? 
              '<p class="text-muted mb-0">No hay ejercicios asignados para este día. Agrega ejercicios abajo.</p>' :
              `<div class="list-group list-group-flush mb-3">
                ${ejerciciosDia.map((ej, idx) => {
                  const ejercicio = ejerciciosDisponibles.find(e => e.id == ej.ejercicio_id);
                  return `
                    <div class="list-group-item bg-dark border-secondary d-flex justify-content-between align-items-center">
                      <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <span class="badge bg-secondary">${idx + 1}</span>
                        <div class="flex-grow-1">
                          <h6 class="mb-0">${ejercicio?.nombre || 'Ejercicio'}</h6>
                          <small class="text-muted">${ej.series} series × ${ej.repeticiones} reps • ${ej.descanso_segundos}s descanso</small>
                        </div>
                      </div>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning" onclick="editarEjercicio('${dia}', ${idx})">✏️</button>
                        <button class="btn btn-outline-danger" onclick="eliminarEjercicio('${dia}', ${idx})">🗑️</button>
                      </div>
                    </div>
                  `;
                }).join('')}
              </div>`
            }
            
            <button class="btn btn-success btn-sm" onclick="abrirModalAgregarEjercicio('${dia}')">
              ➕ Agregar Ejercicio
            </button>
          </div>
        </div>
      `;
      
      container.innerHTML = html;
    }

    function abrirModalAgregarEjercicio(dia) {
      const modalHTML = `
        <div class="modal fade" id="agregarEjercicioModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <h5 class="modal-title">Agregar Ejercicio - ${dia.charAt(0).toUpperCase() + dia.slice(1)}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body" style="max-height:60vh;overflow-y:auto">
                <div class="mb-3">
                  <input type="text" id="buscarEjercicio" class="form-control" placeholder="🔍 Buscar ejercicio...">
                </div>
                <div id="listaEjercicios" class="row g-2">
                  ${ejerciciosDisponibles.map(ej => `
                    <div class="col-md-6 ejercicio-item" data-nombre="${ej.nombre.toLowerCase()}" data-grupo="${ej.grupo_muscular}">
                      <div class="card bg-dark border-secondary h-100" style="cursor:pointer" onclick="seleccionarEjercicio(${ej.id}, '${dia}')">
                        <div class="card-body p-2">
                          <div class="d-flex gap-2 align-items-center">
                            ${ej.gif_url ? 
                              `<img src="${ej.gif_url}" style="width:60px;height:60px;object-fit:cover;border-radius:4px" onerror="this.style.display='none'">` :
                              '<div style="width:60px;height:60px;background:#333;border-radius:4px"></div>'
                            }
                            <div class="flex-grow-1">
                              <h6 class="mb-0 small">${ej.nombre}</h6>
                              <small class="text-muted">${ej.grupo_muscular || 'General'}</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  `).join('')}
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const oldModal = document.getElementById('agregarEjercicioModal');
      if (oldModal) oldModal.remove();
      
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      const modal = new bootstrap.Modal(document.getElementById('agregarEjercicioModal'));
      modal.show();
      
      // Búsqueda en tiempo real
      document.getElementById('buscarEjercicio').addEventListener('input', (e) => {
        const busqueda = e.target.value.toLowerCase();
        document.querySelectorAll('.ejercicio-item').forEach(item => {
          const nombre = item.dataset.nombre;
          item.style.display = nombre.includes(busqueda) ? 'block' : 'none';
        });
      });
    }

    function seleccionarEjercicio(ejercicioId, dia) {
      // Cerrar modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('agregarEjercicioModal'));
      modal.hide();
      
      // Abrir modal de configuración
      abrirModalConfigEjercicio(ejercicioId, dia);
    }

    function abrirModalConfigEjercicio(ejercicioId, dia, editIdx = null) {
      const ejercicio = ejerciciosDisponibles.find(e => e.id == ejercicioId);
      const editando = editIdx !== null;
      const datosActuales = editando ? semanarioEjercicios[dia][editIdx] : null;
      
      const modalHTML = `
        <div class="modal fade" id="configEjercicioModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <h5 class="modal-title">${ejercicio.nombre}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Series</label>
                  <input type="number" id="configSeries" class="form-control" value="${datosActuales?.series || 3}" min="1" max="10">
                </div>
                <div class="mb-3">
                  <label class="form-label">Repeticiones</label>
                  <input type="text" id="configReps" class="form-control" value="${datosActuales?.repeticiones || '8-12'}" placeholder="Ej: 10, 8-12, AMRAP">
                </div>
                <div class="mb-3">
                  <label class="form-label">Descanso (segundos)</label>
                  <input type="number" id="configDescanso" class="form-control" value="${datosActuales?.descanso_segundos || 60}" min="15" step="15">
                </div>
                <div class="mb-3">
                  <label class="form-label">Notas (opcional)</label>
                  <textarea id="configNotas" class="form-control" rows="2" placeholder="Ej: Mantén la espalda recta...">${datosActuales?.notas || ''}</textarea>
                </div>
              </div>
              <div class="modal-footer border-success">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarConfigEjercicio(${ejercicioId}, '${dia}', ${editIdx})">
                  ${editando ? 'Actualizar' : 'Agregar'}
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const oldModal = document.getElementById('configEjercicioModal');
      if (oldModal) oldModal.remove();
      
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      const modal = new bootstrap.Modal(document.getElementById('configEjercicioModal'));
      modal.show();
    }

    function guardarConfigEjercicio(ejercicioId, dia, editIdx) {
      const config = {
        ejercicio_id: ejercicioId,
        series: parseInt(document.getElementById('configSeries').value),
        repeticiones: document.getElementById('configReps').value,
        descanso_segundos: parseInt(document.getElementById('configDescanso').value),
        notas: document.getElementById('configNotas').value
      };
      
      if (editIdx !== null) {
        semanarioEjercicios[dia][editIdx] = config;
      } else {
        semanarioEjercicios[dia].push(config);
      }
      
      const modal = bootstrap.Modal.getInstance(document.getElementById('configEjercicioModal'));
      modal.hide();
      
      renderizarDia(dia);
    }

    function editarEjercicio(dia, idx) {
      const ejercicio = semanarioEjercicios[dia][idx];
      abrirModalConfigEjercicio(ejercicio.ejercicio_id, dia, idx);
    }

    function eliminarEjercicio(dia, idx) {
      if (confirm('¿Eliminar este ejercicio?')) {
        semanarioEjercicios[dia].splice(idx, 1);
        renderizarDia(dia);
      }
    }

    async function guardarRutina() {
      const nombre = document.getElementById('rtitle').value.trim();
      const descripcion = document.getElementById('rdesc').value.trim();
      const nivel = document.getElementById('rnivel').value;
      const duracion = parseInt(document.getElementById('rduracion').value);
      
      if (!nombre) {
        alert('Por favor ingresa un nombre para la rutina');
        volverAPaso1();
        return;
      }
      
      // Combinar todos los ejercicios de todos los días con orden global
      const todosEjercicios = [];
      let ordenGlobal = 1;
      
      Object.keys(semanarioEjercicios).forEach(dia => {
        semanarioEjercicios[dia].forEach(ej => {
          todosEjercicios.push({
            ...ej,
            orden: ordenGlobal++,
            notas: (ej.notas || '') + ` [${dia.charAt(0).toUpperCase() + dia.slice(1)}]`
          });
        });
      });
      
      if (todosEjercicios.length === 0) {
        alert('Agrega al menos un ejercicio a la rutina');
        return;
      }
      
      const msgDiv = document.getElementById('routineMsg');
      msgDiv.innerHTML = '<div class="alert alert-info">Guardando rutina...</div>';
      
      try {
        const res = await fetch('../../public/api/crear-rutina.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            nombre,
            descripcion,
            nivel,
            duracion_semanas: duracion,
            ejercicios: todosEjercicios
          })
        });
        
        const data = await res.json();
        
        if (data.success) {
          msgDiv.innerHTML = '<div class="alert alert-success">✅ Rutina creada exitosamente!</div>';
          setTimeout(() => {
            limpiarRutina();
            loadInstructorData(); // Recargar datos
          }, 1500);
        } else {
          msgDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
        }
      } catch (err) {
        msgDiv.innerHTML = `<div class="alert alert-danger">Error al guardar: ${err.message}</div>`;
      }
    }

    function limpiarRutina() {
      document.getElementById('rtitle').value = '';
      document.getElementById('rdesc').value = '';
      document.getElementById('rnivel').value = 'intermedio';
      document.getElementById('rduracion').value = '8';
      semanarioEjercicios = {
        lunes: [], martes: [], miercoles: [], jueves: [],
        viernes: [], sabado: [], domingo: []
      };
      document.getElementById('routineMsg').innerHTML = '';
      volverAPaso1();
    }

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
        
        // Cargar datos según la sección
        if (section === 'asignadas') {
          cargarRutinasAsignadas();
        } else if (section === 'progreso') {
          cargarClientesEnSelector();
        } else if (section === 'clients') {
          cargarClientes();
        }
        
        anime({
          targets: targetSection,
          translateX: [50, 0],
          opacity: [0, 1],
          duration: 600,
          easing: 'easeOutExpo'
        });
      });
    });
    
    // Función helper para cambiar de sección programáticamente
    function cambiarSeccion(seccionNombre) {
      const link = document.querySelector(`.nav-link[data-section="${seccionNombre}"]`);
      if (link) link.click();
    }

    // Animaciones de entrada
    anime({
      targets: '.sidebar',
      translateX: [-50, 0],
      opacity: [0, 1],
      duration: 800,
      easing: 'easeOutExpo'
    });

    anime({
      targets: '.instructor-stat',
      scale: [0.8, 1],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(100, {start: 300}),
      easing: 'easeOutElastic(1, .6)'
    });

    // Asignar rutina a cliente
    async function abrirModalAsignarRutina(rutinaId) {
      try {
        const res = await fetch('../../public/api/instructor-stats.php');
        const data = await res.json();
        
        if (!data.success || !data.clientes || data.clientes.length === 0) {
          alert('No tienes clientes asignados. Los usuarios deben agregarte como su instructor primero.');
          return;
        }
        
        const modalHTML = `
          <div class="modal fade" id="asignarRutinaModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content bg-dark text-white border-success">
                <div class="modal-header border-success">
                  <h5 class="modal-title">Asignar Rutina a Cliente</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p class="small text-muted mb-3">Selecciona el cliente al que deseas asignar esta rutina:</p>
                  <div class="list-group">
                    ${data.clientes.map(cliente => `
                      <button type="button" class="list-group-item list-group-item-action bg-dark text-white border-secondary" onclick="confirmarAsignacion(${rutinaId}, ${cliente.id}, '${cliente.nombre} ${cliente.apellido}')">
                        <div class="d-flex w-100 justify-content-between">
                          <h6 class="mb-1">${cliente.nombre} ${cliente.apellido}</h6>
                          <small class="text-muted">${cliente.email}</small>
                        </div>
                      </button>
                    `).join('')}
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const oldModal = document.getElementById('asignarRutinaModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('asignarRutinaModal'));
        modal.show();
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar clientes');
      }
    }

    async function confirmarAsignacion(rutinaId, usuarioId, nombreCliente) {
      if (!confirm(`¿Asignar esta rutina a ${nombreCliente}?`)) return;
      
      try {
        const res = await fetch('../../public/api/asignar-rutina.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ rutina_id: rutinaId, usuario_id: usuarioId })
        });
        
        const data = await res.json();
        if (data.success) {
          const modalAsignar = document.getElementById('asignarRutinaModal');
          if (modalAsignar) bootstrap.Modal.getInstance(modalAsignar).hide();
          alert('Rutina asignada correctamente a ' + nombreCliente);
        } else {
          alert('Error: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al asignar rutina');
      }
    }

    // ============ RUTINAS ASIGNADAS ============
    async function cargarRutinasAsignadas() {
      const container = document.getElementById('rutinas-asignadas-container');
      container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success"></div></div>';
      
      try {
        const res = await fetch('../../public/api/listar-rutinas-asignadas.php');
        const data = await res.json();
        
        if (!data.success) {
          container.innerHTML = `<div class="alert alert-danger">${data.error || 'Error al cargar rutinas'}</div>`;
          return;
        }
        
        const asignaciones = data.asignaciones || [];
        
        if (asignaciones.length === 0) {
          container.innerHTML = `
            <div class="text-center text-muted py-5">
              <h4>No hay rutinas asignadas</h4>
              <p class="small">Las rutinas que asignes a tus clientes aparecerán aquí</p>
              <button class="btn btn-primary mt-3" onclick="cambiarSeccion('create')">
                Crear Nueva Rutina
              </button>
            </div>
          `;
          return;
        }
        
        let html = '<div class="table-responsive"><table class="table table-dark table-hover">';
        html += `
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Rutina</th>
              <th>Nivel</th>
              <th>Duración</th>
              <th>Fecha Asignación</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
        `;
        
        asignaciones.forEach(asig => {
          html += `
            <tr data-cliente-id="${asig.cliente_id}">
              <td>
                <strong>${asig.cliente_nombre} ${asig.cliente_apellido}</strong><br>
                <small class="text-muted">${asig.cliente_email}</small>
              </td>
              <td>
                <strong>${asig.rutina_nombre}</strong><br>
                <small class="text-muted">${asig.rutina_descripcion || ''}</small>
              </td>
              <td><span class="badge bg-info">${asig.nivel || 'Intermedio'}</span></td>
              <td>${asig.duracion_semanas || 4} semanas</td>
              <td>${new Date(asig.fecha_asignacion).toLocaleDateString('es-MX')}</td>
              <td>
                <span class="badge ${asig.activo == 1 ? 'bg-success' : 'bg-secondary'}">
                  ${asig.activo == 1 ? '✓ Activa' : 'Inactiva'}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-info" onclick="verDetalleRutina(${asig.rutina_id})">
                  👁️ Ver
                </button>
                ${asig.activo == 1 ? 
                  `<button class="btn btn-sm btn-outline-warning" onclick="desactivarAsignacion(${asig.asignacion_id})">⏸️ Pausar</button>` :
                  `<button class="btn btn-sm btn-outline-success" onclick="activarAsignacion(${asig.asignacion_id})">▶️ Activar</button>`
                }
              </td>
            </tr>
          `;
        });
        
        html += '</tbody></table></div>';
        container.innerHTML = html;
        
      } catch (err) {
        console.error('Error:', err);
        container.innerHTML = '<div class="alert alert-danger">Error al cargar rutinas asignadas</div>';
      }
    }

    async function desactivarAsignacion(asignacionId) {
      if (!confirm('¿Deseas pausar esta rutina para el cliente?')) return;
      
      try {
        const res = await fetch('../../public/api/toggle-rutina-asignada.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ asignacion_id: asignacionId, activo: 0 })
        });
        
        const data = await res.json();
        if (data.success) {
          alert('Rutina pausada correctamente');
          cargarRutinasAsignadas();
        } else {
          alert('Error: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al pausar rutina');
      }
    }

    async function activarAsignacion(asignacionId) {
      try {
        const res = await fetch('../../public/api/toggle-rutina-asignada.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ asignacion_id: asignacionId, activo: 1 })
        });
        
        const data = await res.json();
        if (data.success) {
          alert('Rutina activada correctamente');
          cargarRutinasAsignadas();
        } else {
          alert('Error: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al activar rutina');
      }
    }

    // ============ PROGRESO DE CLIENTES ============
    async function cargarProgresoCliente(usuarioId) {
      const container = document.getElementById('progreso-cliente-container');
      
      if (!usuarioId) {
        container.innerHTML = '<div class="alert alert-info">Selecciona un cliente para ver su progreso</div>';
        return;
      }
      
      container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success"></div></div>';
      
      try {
        const res = await fetch(`../../public/api/obtener-progreso.php?usuario_id=${usuarioId}`);
        const data = await res.json();
        
        if (!data.success) {
          container.innerHTML = `<div class="alert alert-warning">No hay datos de progreso para este cliente</div>`;
          return;
        }
        
        const registros = data.registros || [];
        
        if (registros.length === 0) {
          container.innerHTML = `
            <div class="alert alert-info">
              Este cliente aún no ha registrado su progreso
            </div>
          `;
          return;
        }
        
        // Mostrar último registro y gráfica
        const ultimo = registros[0];
        
        let html = `
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card bg-secondary">
                <div class="card-header bg-dark">
                  <h6 class="mb-0">Última Medición</h6>
                  <small class="text-muted">${new Date(ultimo.fecha_registro).toLocaleDateString('es-MX')}</small>
                </div>
                <div class="card-body">
                  <div class="row g-2">
                    ${ultimo.peso ? `<div class="col-4"><strong>Peso:</strong><br>${ultimo.peso} kg</div>` : ''}
                    ${ultimo.grasa_corporal ? `<div class="col-4"><strong>Grasa:</strong><br>${ultimo.grasa_corporal}%</div>` : ''}
                    ${ultimo.masa_muscular ? `<div class="col-4"><strong>Músculo:</strong><br>${ultimo.masa_muscular} kg</div>` : ''}
                    ${ultimo.circunferencia_pecho ? `<div class="col-4"><strong>Pecho:</strong><br>${ultimo.circunferencia_pecho} cm</div>` : ''}
                    ${ultimo.circunferencia_cintura ? `<div class="col-4"><strong>Cintura:</strong><br>${ultimo.circunferencia_cintura} cm</div>` : ''}
                    ${ultimo.circunferencia_brazo ? `<div class="col-4"><strong>Brazo:</strong><br>${ultimo.circunferencia_brazo} cm</div>` : ''}
                  </div>
                  ${ultimo.notas ? `<div class="mt-2"><small class="text-muted"><strong>Notas:</strong> ${ultimo.notas}</small></div>` : ''}
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card bg-secondary">
                <div class="card-header bg-dark">
                  <h6 class="mb-0">Historial (${registros.length} registros)</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive" style="max-height:300px;overflow-y:auto">
                    <table class="table table-sm table-dark">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Peso</th>
                          <th>Grasa</th>
                          <th>Músculo</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${registros.map(r => `
                          <tr>
                            <td>${new Date(r.fecha_registro).toLocaleDateString('es-MX')}</td>
                            <td>${r.peso || '-'} kg</td>
                            <td>${r.grasa_corporal || '-'}%</td>
                            <td>${r.masa_muscular || '-'} kg</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        container.innerHTML = html;
        
      } catch (err) {
        console.error('Error:', err);
        container.innerHTML = '<div class="alert alert-danger">Error al cargar progreso</div>';
      }
    }

    // Cargar clientes en el selector de progreso
    async function cargarClientesEnSelector() {
      try {
        const res = await fetch('../../public/api/obtener-clientes-instructor.php');
        const data = await res.json();
        
        if (data.success && data.clientes) {
          const select = document.getElementById('cliente-progreso-select');
          data.clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = `${cliente.nombre} ${cliente.apellido}`;
            select.appendChild(option);
          });
        }
      } catch (err) {
        console.error('Error cargando clientes:', err);
      }
    }

    // Cargar clientes del instructor
    async function cargarClientes() {
      try {
        const container = document.getElementById('clientes-container');
        container.innerHTML = '<div class="text-center text-muted py-3"><small>Cargando clientes...</small></div>';

        const response = await fetch('/fitandfuel/src/public/api/estadisticas-clientes.php', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Error al obtener clientes');
        }

        const data = await response.json();

        if (data.success && data.clientes) {
          mostrarClientes(data.clientes);
        } else {
          container.innerHTML = '<div class="text-center text-muted py-3"><small>No tienes clientes asignados.</small></div>';
        }
      } catch (error) {
        console.error('Error al cargar clientes:', error);
        document.getElementById('clientes-container').innerHTML = 
          '<div class="alert alert-danger">Error al cargar clientes. Por favor, intenta de nuevo.</div>';
      }
    }

    // Mostrar listado de clientes con estadísticas
    function mostrarClientes(clientes) {
      const container = document.getElementById('clientes-container');
      
      if (!clientes || clientes.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3"><small>No tienes clientes asignados.</small></div>';
        return;
      }

      container.innerHTML = `
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Email</th>
              <th>Rutinas</th>
              <th>Actividad</th>
              <th>Desde</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${clientes.map(cliente => {
              const diasDesdeActividad = cliente.ultima_actividad ? 
                Math.floor((new Date() - new Date(cliente.ultima_actividad)) / (1000 * 60 * 60 * 24)) : null;
              
              let actividadBadge = '';
              if (diasDesdeActividad === null) {
                actividadBadge = '<span class="badge bg-secondary">Sin actividad</span>';
              } else if (diasDesdeActividad === 0) {
                actividadBadge = '<span class="badge bg-success">Hoy</span>';
              } else if (diasDesdeActividad <= 3) {
                actividadBadge = `<span class="badge bg-success">Hace ${diasDesdeActividad}d</span>`;
              } else if (diasDesdeActividad <= 7) {
                actividadBadge = `<span class="badge bg-warning">Hace ${diasDesdeActividad}d</span>`;
              } else {
                actividadBadge = `<span class="badge bg-danger">Hace ${diasDesdeActividad}d</span>`;
              }

              return `
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" 
                         style="width: 35px; height: 35px; font-size: 14px;">
                      ${cliente.nombre.charAt(0)}${cliente.apellido.charAt(0)}
                    </div>
                    <div>
                      <strong>${cliente.nombre} ${cliente.apellido}</strong>
                    </div>
                  </div>
                </td>
                <td>${cliente.email}</td>
                <td>
                  <span class="badge bg-info">${cliente.rutinas_activas || 0} activa${cliente.rutinas_activas === 1 ? '' : 's'}</span>
                  ${cliente.rutinas_asignadas > cliente.rutinas_activas ? 
                    `<span class="badge bg-secondary ms-1">${cliente.rutinas_asignadas - cliente.rutinas_activas} pausada${(cliente.rutinas_asignadas - cliente.rutinas_activas) === 1 ? '' : 's'}</span>` : 
                    ''
                  }
                </td>
                <td>${actividadBadge}</td>
                <td class="small text-muted">${new Date(cliente.fecha_asignacion).toLocaleDateString('es-MX')}</td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="verProgresoCliente(${cliente.id})" title="Ver Progreso">
                      <i class="fas fa-chart-line"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="verRutinasCliente(${cliente.id})" title="Ver Rutinas">
                      <i class="fas fa-dumbbell"></i>
                    </button>
                  </div>
                </td>
              </tr>
            `;
            }).join('')}
          </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-between align-items-center">
          <div class="text-muted small">
            <i class="fas fa-users"></i> <strong>${clientes.length}</strong> cliente${clientes.length === 1 ? '' : 's'} total${clientes.length === 1 ? '' : 'es'}
          </div>
          <div class="text-muted small">
            <span class="badge bg-success me-1">${clientes.filter(c => c.rutinas_activas > 0).length}</span> con rutinas activas
          </div>
        </div>
      `;
    }

    // Ver progreso del cliente (cambia a la sección de progreso y lo carga)
    function verProgresoCliente(clienteId) {
      // Cambiar a la sección de progreso
      cambiarSeccion('section-progreso');
      
      // Establecer el cliente seleccionado
      const selector = document.getElementById('clienteProgresoSelector');
      if (selector) {
        selector.value = clienteId;
        // Cargar el progreso
        cargarProgresoCliente();
      }
    }

    // Ver rutinas del cliente (cambia a la sección de rutinas asignadas con filtro)
    function verRutinasCliente(clienteId) {
      // Cambiar a la sección de rutinas asignadas
      cambiarSeccion('section-asignadas');
      
      // Esperar a que se carguen las rutinas y luego filtrar
      setTimeout(() => {
        const tabla = document.querySelector('#rutinasAsignadasTable tbody');
        if (tabla) {
          const filas = tabla.querySelectorAll('tr');
          filas.forEach(fila => {
            const clienteIdFila = fila.getAttribute('data-cliente-id');
            if (clienteIdFila && clienteIdFila !== clienteId.toString()) {
              fila.style.display = 'none';
            } else {
              fila.style.display = '';
            }
          });
        }
      }, 500);
    }

    // Inicializar al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
      loadInstructorData();
      cargarEjerciciosDisponibles();
    });
  </script>
</body>
</html>
