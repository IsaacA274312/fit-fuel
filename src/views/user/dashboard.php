<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../public/index.html');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel de Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    body{background:#1a1a1a;color:#fff;min-height:100vh}
    /* Forzar todo el texto a blanco */
    *{color:#fff!important}
    .text-muted,.text-secondary,.text-primary,.text-success,.text-danger,.text-warning,.text-info{color:#fff!important}
    .small,.badge,small,h1,h2,h3,h4,h5,h6,p,span,div,td,th,li,a,label,input,textarea,select,button{color:#fff!important}
    .user-stat{background:linear-gradient(135deg, rgba(240,112,8,0.15), rgba(255,255,255,0.02));padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.05);text-align:center}
    .user-stat h3{margin:0;font-size:26px;color:#fff;font-weight:700}
    .user-stat p{margin:6px 0 0;font-size:11px;color:#fff}
    .sidebar{background:linear-gradient(180deg, rgba(30,30,30,1), rgba(20,20,20,1));border-radius:12px;padding:18px;border:1px solid rgba(255,255,255,0.05)}
    .calendar{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-top:10px;max-width:600px}
    .calendar-day{aspect-ratio:1;background:rgba(255,255,255,0.03);border-radius:6px;display:flex;flex-direction:column;justify-content:center;align-items:center;font-size:10px;border:1px solid rgba(255,255,255,0.05);cursor:pointer;padding:4px}
    .calendar-day.header{background:rgba(255,255,255,0.08);font-weight:600;cursor:default}
    .calendar-day.attended{background:linear-gradient(135deg, rgba(34,197,94,0.25), rgba(34,197,94,0.15));border-color:rgba(34,197,94,0.4)}
    .calendar-day.training{background:linear-gradient(135deg, rgba(240,112,8,0.25), rgba(240,112,8,0.15));border-color:rgba(240,112,8,0.4)}
    .calendar-day.both{background:linear-gradient(135deg, rgba(59,130,246,0.25), rgba(59,130,246,0.15));border-color:rgba(59,130,246,0.4)}
    .schedule-item{background:rgba(255,255,255,0.03);padding:10px 12px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;border-left:3px solid var(--accent);gap:12px}
    .schedule-item .time{font-weight:600;color:#fff;font-size:12px;min-width:70px}
    .schedule-item .activity h4{margin:0 0 2px;font-size:13px;color:#fff}
    .schedule-item .activity p{margin:0;font-size:11px;color:#fff}
  </style>
</head>
<body>
  <div class="container-fluid py-4" style="max-width:1800px">
    <div class="row g-3">
      <div class="col-lg-2 col-md-3">
        <div class="sidebar sticky-top" style="top:20px">
          <div class="text-center mb-4">
            <img src="../../public/images/logo.jpg" alt="logo" class="img-fluid mb-3" style="max-width:100px;border-radius:12px">
            <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . ($_SESSION['apellido'] ?? '')); ?></h5>
            <p class="small text-muted mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p class="small text-muted mt-1">Usuario</p>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-outline-primary btn-sm position-relative" onclick="abrirNotificaciones()">
              🔔 Notificaciones
              <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none">0</span>
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="abrirProgreso()">
              📊 Mi Progreso
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="verMisRutinas()">
              💪 Mis Rutinas
            </button>
            <button class="btn btn-outline-success btn-sm fw-bold" onclick="iniciarEntrenamiento()" style="background:linear-gradient(135deg,rgba(240,112,8,0.2),transparent);border-color:#f07008">
              🏋️ EMPEZAR ENTRENO
            </button>
            <button class="btn btn-outline-warning btn-sm" onclick="abrirTienda()">
              🛒 Tienda <span id="carrito-badge" class="badge bg-danger" style="display:none">0</span>
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="verHistorialPedidos()">
              📦 Mis Pedidos
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="abrirNutricion()">
              🍎 Nutrición
            </button>
            <a href="../../public/index.html" class="btn btn-outline-secondary btn-sm">Inicio</a>
            <button id="logoutLink" class="btn btn-danger btn-sm">Cerrar sesión</button>
          </div>
        </div>
      </div>

      <div class="col-lg-10 col-md-9">
        <main>
         
          
          <div class="row g-3 mb-3">
            <div class="col-xl col-lg-3 col-md-6">
              <div class="user-stat">
                <h3 id="stat-sesiones">0</h3>
                <p>Sesiones</p>
              </div>
            </div>
            <div class="col-xl col-lg-3 col-md-6">
              <div class="user-stat">
                <h3 id="stat-dias">0</h3>
                <p>Días activos</p>
              </div>
            </div>
            <div class="col-xl col-lg-3 col-md-6">
              <div class="user-stat">
                <h3 id="stat-pendientes">0</h3>
                <p>Pendientes</p>
              </div>
            </div>
            <div class="col-xl col-lg-3 col-md-6">
              <div class="user-stat">
                <h3 id="stat-cumplimiento">0%</h3>
                <p>Cumplimiento</p>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-lg-6">
              <div class="card bg-dark text-white border-secondary">
                <div class="card-body">
                  <h3 class="card-title h5 mb-3">Calendario</h3>
                  <div class="calendar" id="calendar">
                    <div class="calendar-day header">L</div>
                    <div class="calendar-day header">M</div>
                    <div class="calendar-day header">X</div>
                    <div class="calendar-day header">J</div>
                    <div class="calendar-day header">V</div>
                    <div class="calendar-day header">S</div>
                    <div class="calendar-day header">D</div>
                    <div class="calendar-day"></div>
                    <div class="calendar-day"></div>
                    <div class="calendar-day"></div>
                    <div class="calendar-day">1</div>
                    <div class="calendar-day training">2</div>
                    <div class="calendar-day">3</div>
                    <div class="calendar-day both">4</div>
                    <div class="calendar-day attended">5</div>
                    <div class="calendar-day training">6</div>
                    <div class="calendar-day">7</div>
                    <div class="calendar-day both">8</div>
                    <div class="calendar-day attended">9</div>
                    <div class="calendar-day">10</div>
                    <div class="calendar-day">11</div>
                    <div class="calendar-day attended">12</div>
                    <div class="calendar-day training">13</div>
                    <div class="calendar-day">14</div>
                    <div class="calendar-day both">15</div>
                    <div class="calendar-day attended">16</div>
                    <div class="calendar-day">17</div>
                    <div class="calendar-day">18</div>
                    <div class="calendar-day training">19</div>
                    <div class="calendar-day attended">20</div>
                    <div class="calendar-day">21</div>
                    <div class="calendar-day both">22</div>
                    <div class="calendar-day">23</div>
                    <div class="calendar-day">24</div>
                    <div class="calendar-day training">25</div>
                    <div class="calendar-day attended">26</div>
                    <div class="calendar-day">27</div>
                    <div class="calendar-day both">28</div>
                    <div class="calendar-day training" style="border:2px solid var(--accent)">29</div>
                    <div class="calendar-day">30</div>
                  </div>
                  <div class="mt-3 d-flex gap-3 small">
                    <span><span style="display:inline-block;width:12px;height:12px;background:rgba(34,197,94,0.3);border-radius:3px"></span> Asistido</span>
                    <span><span style="display:inline-block;width:12px;height:12px;background:rgba(240,112,8,0.3);border-radius:3px"></span> Programado</span>
                    <span><span style="display:inline-block;width:12px;height:12px;background:rgba(59,130,246,0.3);border-radius:3px"></span> Ambos</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card bg-dark text-white border-secondary">
                <div class="card-body">
                  <h3 class="card-title h5 mb-3">Entrenamientos Programados</h3>
                  <div id="entrenamientos-list" class="d-flex flex-column gap-2">
                    <div class="text-center text-muted py-3">
                      <small>Cargando entrenamientos...</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-3">
            <div class="col-lg-6">
              <div class="card bg-dark text-white border-secondary">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="card-title h5 mb-0">Mi Instructor</h3>
                    <button class="btn btn-sm btn-outline-success" onclick="mostrarModalInstructores()">Cambiar</button>
                  </div>
                  <div id="instructor-info">
                    <p class="text-muted small">Cargando...</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card bg-dark text-white border-secondary">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="card-title h5 mb-0">Mi Nutriólogo</h3>
                    <button class="btn btn-sm btn-outline-success" onclick="mostrarModalNutriologos()">Cambiar</button>
                  </div>
                  <div id="nutriologo-info">
                    <p class="text-muted small">Cargando...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </main>

        <!-- Modal de Entrenamiento en Vivo (Hevy Style) -->
        <div class="modal fade" id="entrenamientoModal" tabindex="-1" data-bs-backdrop="static">
          <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark text-white">
              <div class="modal-header border-secondary" style="background:linear-gradient(180deg,rgba(240,112,8,0.1),transparent)">
                <div class="flex-grow-1">
                  <h5 class="modal-title mb-0">🏋️ <span id="nombre-entrenamiento">Entrenamiento</span></h5>
                  <div class="small text-muted">
                    <span id="timer-entrenamiento">00:00</span> • 
                    <span id="ejercicios-completados">0</span>/<span id="ejercicios-totales">0</span> ejercicios
                  </div>
                </div>
                <button type="button" class="btn btn-outline-danger me-2" onclick="cancelarEntrenamiento()">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="finalizarEntrenamiento()">Finalizar</button>
              </div>
              <div class="modal-body" style="overflow-y:auto">
                <div id="lista-ejercicios-workout">
                  <!-- Se llenará dinámicamente -->
                </div>

                <!-- Temporizador de Descanso -->
                <div id="rest-timer" class="position-fixed bottom-0 start-50 translate-middle-x mb-3" style="display:none;z-index:9999">
                  <div class="card bg-dark border-success text-center shadow-lg" style="min-width:300px">
                    <div class="card-body py-4">
                      <h3 class="display-4 text-success mb-0" id="rest-countdown">90</h3>
                      <p class="small text-muted mb-2">Descanso</p>
                      <button class="btn btn-sm btn-outline-success" onclick="saltarDescanso()">Saltar</button>
                      <button class="btn btn-sm btn-outline-secondary" onclick="agregarTiempo(15)">+15s</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal de Nutrición -->
        <div class="modal fade" id="nutricionModal" tabindex="-1" data-bs-backdrop="static">
          <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark text-white">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">🍎 Nutrición - <span id="fecha-nutricion"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <!-- Resumen Diario -->
                  <div class="col-lg-4">
                    <div class="card bg-dark border-secondary h-100">
                      <div class="card-header bg-transparent border-secondary">
                        <h6 class="mb-0">📊 Resumen del Día</h6>
                      </div>
                      <div class="card-body">
                        <!-- Calorías -->
                        <div class="mb-4">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Calorías</span>
                            <span class="small"><span id="cal-consumidas">0</span> / <span id="cal-objetivo">2000</span></span>
                          </div>
                          <div class="progress" style="height:20px">
                            <div id="cal-progress" class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
                          </div>
                          <div class="text-center mt-1">
                            <span id="cal-restantes" class="small text-success">2000 restantes</span>
                          </div>
                        </div>

                        <!-- Macros -->
                        <div class="row g-2 mb-4">
                          <div class="col-4">
                            <div class="text-center p-2 rounded" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3)">
                              <div class="fs-5 fw-bold text-success"><span id="prot-consumidas">0</span>g</div>
                              <div class="small text-muted">Proteína</div>
                              <div class="small"><span id="prot-objetivo">150</span>g</div>
                            </div>
                          </div>
                          <div class="col-4">
                            <div class="text-center p-2 rounded" style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3)">
                              <div class="fs-5 fw-bold text-primary"><span id="carbs-consumidos">0</span>g</div>
                              <div class="small text-muted">Carbos</div>
                              <div class="small"><span id="carbs-objetivo">200</span>g</div>
                            </div>
                          </div>
                          <div class="col-4">
                            <div class="text-center p-2 rounded" style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.3)">
                              <div class="fs-5 fw-bold text-warning"><span id="grasas-consumidas">0</span>g</div>
                              <div class="small text-muted">Grasas</div>
                              <div class="small"><span id="grasas-objetivo">65</span>g</div>
                            </div>
                          </div>
                        </div>

                        <!-- Agua -->
                        <div class="mb-3">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">💧 Agua</span>
                            <span class="small"><span id="agua-vasos">0</span> / 8 vasos</span>
                          </div>
                          <div class="d-flex gap-1" id="agua-visual">
                            <!-- Se llenará con JavaScript -->
                          </div>
                          <button class="btn btn-sm btn-outline-info w-100 mt-2" onclick="registrarAgua()">
                            + Agregar Vaso
                          </button>
                        </div>

                        <!-- Botón de metas -->
                        <button class="btn btn-outline-success w-100" onclick="abrirConfigMetas()">
                          ⚙️ Configurar Metas
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Registro de Comidas -->
                  <div class="col-lg-8">
                    <div class="card bg-dark border-secondary mb-3">
                      <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">🍽️ Comidas del Día</h6>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-secondary" onclick="cambiarDia(-1)">◀</button>
                          <button class="btn btn-outline-success" onclick="cambiarDia(0)">Hoy</button>
                          <button class="btn btn-outline-secondary" onclick="cambiarDia(1)">▶</button>
                        </div>
                      </div>
                      <div class="card-body">
                        <!-- Desayuno -->
                        <div class="mb-4">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">🌅 Desayuno</h6>
                            <button class="btn btn-sm btn-success" onclick="agregarComida('desayuno')">+ Agregar</button>
                          </div>
                          <div id="lista-desayuno">
                            <p class="small text-muted">No hay alimentos registrados</p>
                          </div>
                        </div>

                        <!-- Comida -->
                        <div class="mb-4">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">☀️ Comida</h6>
                            <button class="btn btn-sm btn-success" onclick="agregarComida('comida')">+ Agregar</button>
                          </div>
                          <div id="lista-comida">
                            <p class="small text-muted">No hay alimentos registrados</p>
                          </div>
                        </div>

                        <!-- Cena -->
                        <div class="mb-4">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">🌙 Cena</h6>
                            <button class="btn btn-sm btn-success" onclick="agregarComida('cena')">+ Agregar</button>
                          </div>
                          <div id="lista-cena">
                            <p class="small text-muted">No hay alimentos registrados</p>
                          </div>
                        </div>

                        <!-- Snacks -->
                        <div class="mb-4">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">🍪 Snacks</h6>
                            <button class="btn btn-sm btn-success" onclick="agregarComida('snack')">+ Agregar</button>
                          </div>
                          <div id="lista-snack">
                            <p class="small text-muted">No hay alimentos registrados</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    // Configuración de la API
    const API_BASE_URL = '../../public/api';
    const AUTH_TOKEN = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
    
    // Helper para fetch con autenticación
    async function apiFetch(endpoint, options = {}) {
      const defaultOptions = {
        headers: {
          'Content-Type': 'application/json'
        }
      };
      
      const finalOptions = {
        ...defaultOptions,
        ...options,
        headers: {
          ...defaultOptions.headers,
          ...options.headers
        }
      };
      
      const response = await fetch(`${API_BASE_URL}${endpoint}`, finalOptions);
      return response;
    }
    
    // Logout
    document.getElementById('logoutLink').addEventListener('click', async function(e){
      e.preventDefault();
      try{
        const res = await fetch('../../public/logout.php', { method: 'POST' });
        const j = await res.json();
        if(j.success) window.location = '../../public/index.html';
      }catch(err){ alert('Error al cerrar sesión'); }
    });

    // Cargar datos del usuario
    async function loadUserData() {
      try {
        // Cargar rutinas primero
        await cargarRutinasCalendario();
        
        const res = await fetch('../../public/api/user-stats.php');
        const data = await res.json();
        
        if (data.success) {
          // Actualizar estadísticas
          document.getElementById('stat-sesiones').textContent = data.stats.sesiones;
          document.getElementById('stat-dias').textContent = data.stats.diasActivos;
          document.getElementById('stat-pendientes').textContent = data.stats.pendientes;
          document.getElementById('stat-cumplimiento').textContent = data.stats.cumplimiento + '%';

          // Actualizar calendario
          updateCalendar(data.calendario);

          // Actualizar entrenamientos programados (usa rutinasUsuario ya cargadas)
          updateEntrenamientos(data.entrenamientos);
        } else {
          console.error('Error al cargar datos:', data.error);
        }
      } catch (err) {
        console.error('Error en la petición:', err);
      }
    }
    
    // Cargar notificaciones periódicamente
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 30000); // Cada 30 segundos

    function updateCalendar(calendario) {
      const days = document.querySelectorAll('.calendar-day:not(.header)');
      days.forEach((day, index) => {
        const dayNum = day.textContent.trim();
        if (dayNum && calendario[dayNum]) {
          const info = calendario[dayNum];
          day.classList.remove('attended', 'training', 'both');
          
          if (info.asistio && info.programado) {
            day.classList.add('both');
          } else if (info.asistio) {
            day.classList.add('attended');
          } else if (info.programado) {
            day.classList.add('training');
          }
        }
      });
    }

    function updateEntrenamientos(entrenamientos) {
      const container = document.getElementById('entrenamientos-list');
      
      // Obtener día de hoy (1=Lunes, 7=Domingo)
      const hoy = new Date();
      let diaHoy = hoy.getDay();
      diaHoy = diaHoy === 0 ? 7 : diaHoy;
      
      // Buscar ejercicios para hoy en las rutinas activas
      let ejerciciosHoy = [];
      rutinasUsuario.forEach(rutina => {
        if (rutina.activo == 1 && rutina.ejercicios) {
          const ejercicios = rutina.ejercicios.filter(ej => ej.dia_semana == diaHoy);
          if (ejercicios.length > 0) {
            ejerciciosHoy.push({
              rutina: rutina.nombre,
              ejercicios: ejercicios
            });
          }
        }
      });
      
      if (ejerciciosHoy.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3"><small>No tienes entrenamientos programados para hoy.</small></div>';
        return;
      }

      const diasNombres = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
      
      container.innerHTML = `
        <div class="alert alert-success mb-3">
          <strong>📅 ${diasNombres[diaHoy]}</strong> - Tienes ${ejerciciosHoy.reduce((sum, r) => sum + r.ejercicios.length, 0)} ejercicios programados
        </div>
        ${ejerciciosHoy.map(item => `
          <div class="card bg-secondary mb-2">
            <div class="card-body p-2">
              <h6 class="mb-2 small">${item.rutina}</h6>
              <div class="d-flex flex-column gap-1">
                ${item.ejercicios.slice(0, 3).map(ej => `
                  <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-dark" style="min-width:30px">${ej.orden}</span>
                    <small class="flex-grow-1">${ej.nombre}</small>
                    <small class="text-success">${ej.series}x${ej.repeticiones}</small>
                  </div>
                `).join('')}
                ${item.ejercicios.length > 3 ? `<small class="text-muted">+${item.ejercicios.length - 3} más...</small>` : ''}
              </div>
            </div>
          </div>
        `).join('')}
        <button class="btn btn-primary btn-sm w-100 mt-2" onclick="verMisRutinas()">
          Ver Rutina Completa
        </button>
      `;
    }

    function confirmarAsistencia(claseId) {
      if (confirm('¿Confirmar tu asistencia a esta clase?')) {
        // Aquí se puede agregar la lógica para confirmar asistencia
        alert('Asistencia confirmada (funcionalidad por implementar)');
      }
    }

    // Cargar datos al inicio
    loadUserData();

    // Animaciones de entrada
    anime({
      targets: '.sidebar',
      translateX: [-50, 0],
      opacity: [0, 1],
      duration: 800,
      easing: 'easeOutExpo'
    });

    anime({
      targets: '.user-stat',
      scale: [0.8, 1],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(100, {start: 300}),
      easing: 'easeOutElastic(1, .6)'
    });

    anime({
      targets: '.card.bg-dark',
      translateY: [20, 0],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(150, {start: 500}),
      easing: 'easeOutQuad'
    });

    anime({
      targets: '.schedule-item',
      translateX: [30, 0],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(100, {start: 800}),
      easing: 'easeOutQuad'
    });

    // Interactividad del calendario
    let rutinasUsuario = [];
    
    // Cargar rutinas para el calendario
    async function cargarRutinasCalendario() {
      try {
        const res = await fetch('../../public/api/obtener-rutinas-usuario.php');
        const data = await res.json();
        if (data.success) {
          rutinasUsuario = data.rutinas || [];
        }
      } catch (err) {
        console.error('Error cargando rutinas:', err);
      }
    }
    
    cargarRutinasCalendario();
    
    document.querySelectorAll('.calendar-day:not(.header)').forEach(day => {
      day.addEventListener('click', function(){
        const dayNum = parseInt(this.textContent.trim());
        if(!dayNum) return;
        
        // Obtener día de la semana (1=Lunes, 7=Domingo)
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const fecha = new Date(hoy.getFullYear(), hoy.getMonth(), dayNum);
        let diaSemana = fecha.getDay(); // 0=Domingo, 1=Lunes...
        diaSemana = diaSemana === 0 ? 7 : diaSemana; // Convertir a 1-7
        
        const hasAttended = this.classList.contains('attended') || this.classList.contains('both');
        const hasTraining = this.classList.contains('training') || this.classList.contains('both');
        
        // Buscar ejercicios para este día
        let ejerciciosDelDia = [];
        rutinasUsuario.forEach(rutina => {
          if (rutina.activo == 1 && rutina.ejercicios) {
            const ejercicios = rutina.ejercicios.filter(ej => ej.dia_semana == diaSemana);
            if (ejercicios.length > 0) {
              ejerciciosDelDia.push({
                rutina: rutina.nombre,
                ejercicios: ejercicios
              });
            }
          }
        });
        
        // Mostrar modal con detalles
        mostrarDetallesDia(dayNum, diaSemana, hasAttended, hasTraining, ejerciciosDelDia);
      });
    });
    
    function mostrarDetallesDia(dia, diaSemana, asistio, programado, ejerciciosDelDia) {
      const diasNombres = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
      
      let contenido = `
        <div class="mb-3">
          <h6>📅 ${diasNombres[diaSemana]} ${dia}</h6>
          ${asistio ? '<span class="badge bg-success me-2">✅ Asistencia registrada</span>' : ''}
          ${programado ? '<span class="badge bg-warning">🏋️ Entrenamiento programado</span>' : ''}
        </div>
      `;
      
      if (ejerciciosDelDia.length === 0) {
        contenido += '<p class="text-muted">No hay ejercicios programados para este día.</p>';
      } else {
        ejerciciosDelDia.forEach(item => {
          contenido += `
            <div class="card bg-secondary mb-3">
              <div class="card-header bg-dark">
                <h6 class="mb-0">${item.rutina}</h6>
              </div>
              <div class="card-body">
                ${item.ejercicios.map(ej => `
                  <div class="d-flex gap-2 align-items-center mb-2 p-2 bg-dark rounded">
                    ${ej.gif_url ? 
                      `<img src="${ej.gif_url}" style="width:50px;height:50px;object-fit:cover;border-radius:6px" onerror="this.style.display='none'">` :
                      ''
                    }
                    <div>
                      <strong>${ej.nombre}</strong><br>
                      <small class="text-success">${ej.series} series x ${ej.repeticiones} | Descanso: ${ej.descanso_seg}s</small><br>
                      <small class="text-muted">${ej.grupo_muscular || ''}</small>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
          `;
        });
      }
      
      const modalHTML = `
        <div class="modal fade" id="diaModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">Entrenamiento del Día</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                ${contenido}
              </div>
              <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const oldModal = document.getElementById('diaModal');
      if (oldModal) oldModal.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      const modal = new bootstrap.Modal(document.getElementById('diaModal'));
      modal.show();
    }

    // Cargar instructor y nutriólogo
    async function cargarInstructorNutriologo() {
      try {
        const [resInstructores, resNutriologos] = await Promise.all([
          fetch('../../public/api/listar-instructores.php'),
          fetch('../../public/api/listar-nutriologos.php')
        ]);
        
        const dataInstructores = await resInstructores.json();
        const dataNutriologos = await resNutriologos.json();
        
        if (dataInstructores.success) {
          const miInstructor = dataInstructores.instructores.find(i => i.es_mi_instructor);
          const instructorInfo = document.getElementById('instructor-info');
          if (miInstructor) {
            instructorInfo.innerHTML = `
              <div class="d-flex align-items-center gap-3">
                <div class="flex-grow-1">
                  <h6 class="mb-1">${miInstructor.nombre} ${miInstructor.apellido}</h6>
                  <p class="small text-muted mb-0">${miInstructor.email}</p>
                  <p class="small text-muted mb-0">${miInstructor.total_clientes} clientes</p>
                </div>
                <span class="badge bg-success">Asignado</span>
              </div>
            `;
          } else {
            instructorInfo.innerHTML = '<p class="text-muted small mb-0">No tienes instructor asignado. Haz clic en "Cambiar" para elegir uno.</p>';
          }
        }
        
        if (dataNutriologos.success) {
          const miNutriologo = dataNutriologos.nutriologos.find(n => n.es_mi_nutriologo);
          const nutriologoInfo = document.getElementById('nutriologo-info');
          if (miNutriologo) {
            nutriologoInfo.innerHTML = `
              <div class="d-flex align-items-center gap-3">
                <div class="flex-grow-1">
                  <h6 class="mb-1">${miNutriologo.nombre} ${miNutriologo.apellido}</h6>
                  <p class="small text-muted mb-0">${miNutriologo.email}</p>
                  <p class="small text-muted mb-0">${miNutriologo.total_clientes} clientes</p>
                </div>
                <span class="badge bg-success">Asignado</span>
              </div>
            `;
          } else {
            nutriologoInfo.innerHTML = '<p class="text-muted small mb-0">No tienes nutriólogo asignado. Haz clic en "Cambiar" para elegir uno.</p>';
          }
        }
      } catch (err) {
        console.error('Error al cargar profesionales:', err);
      }
    }

    window.mostrarModalInstructores = async function() {
      try {
        const res = await fetch('../../public/api/listar-instructores.php');
        const data = await res.json();
        
        if (!data.success) {
          alert('Error al cargar instructores');
          return;
        }
        
        const modalHTML = `
          <div class="modal fade" id="instructoresModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content bg-dark text-white border-success">
                <div class="modal-header border-success">
                  <h5 class="modal-title">Seleccionar Instructor</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-3">
                    ${data.instructores.map(instructor => `
                      <div class="col-md-6">
                        <div class="card bg-dark border-${instructor.es_mi_instructor ? 'success' : 'secondary'} h-100">
                          <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                              <div>
                                <h6 class="mb-1">${instructor.nombre} ${instructor.apellido}</h6>
                                <p class="small text-muted mb-1">${instructor.email}</p>
                                <p class="small text-muted mb-0">${instructor.total_clientes} clientes</p>
                              </div>
                              ${instructor.es_mi_instructor ? '<span class="badge bg-success">Actual</span>' : ''}
                            </div>
                            ${!instructor.es_mi_instructor ? `
                              <button class="btn btn-sm btn-success w-100 mt-2" onclick="asignarInstructor(${instructor.id})">
                                Seleccionar
                              </button>
                            ` : ''}
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
        
        const oldModal = document.getElementById('instructoresModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('instructoresModal'));
        modal.show();
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar instructores');
      }
    }

    window.asignarInstructor = async function(instructorId) {
      try {
        const res = await fetch('../../public/api/asignar-instructor.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ instructor_id: instructorId })
        });
        
        const data = await res.json();
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('instructoresModal')).hide();
          await cargarInstructorNutriologo();
          alert('Instructor asignado correctamente');
        } else {
          alert('Error: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al asignar instructor');
      }
    }

    window.mostrarModalNutriologos = async function() {
      try {
        const res = await fetch('../../public/api/listar-nutriologos.php');
        const data = await res.json();
        
        if (!data.success) {
          alert('Error al cargar nutriólogos');
          return;
        }
        
        const modalHTML = `
          <div class="modal fade" id="nutriologosModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content bg-dark text-white border-success">
                <div class="modal-header border-success">
                  <h5 class="modal-title">Seleccionar Nutriólogo</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-3">
                    ${data.nutriologos.map(nutriologo => `
                      <div class="col-md-6">
                        <div class="card bg-dark border-${nutriologo.es_mi_nutriologo ? 'success' : 'secondary'} h-100">
                          <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                              <div>
                                <h6 class="mb-1">${nutriologo.nombre} ${nutriologo.apellido}</h6>
                                <p class="small text-muted mb-1">${nutriologo.email}</p>
                                <p class="small text-muted mb-0">${nutriologo.total_clientes} clientes</p>
                              </div>
                              ${nutriologo.es_mi_nutriologo ? '<span class="badge bg-success">Actual</span>' : ''}
                            </div>
                            ${!nutriologo.es_mi_nutriologo ? `
                              <button class="btn btn-sm btn-success w-100 mt-2" onclick="asignarNutriologo(${nutriologo.id})">
                                Seleccionar
                              </button>
                            ` : ''}
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
        
        const oldModal = document.getElementById('nutriologosModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('nutriologosModal'));
        modal.show();
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar nutriólogos');
      }
    }

    window.asignarNutriologo = async function(nutriologoId) {
      try {
        const res = await fetch('../../public/api/asignar-nutriologo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ nutriologo_id: nutriologoId })
        });
        
        const data = await res.json();
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('nutriologosModal')).hide();
          await cargarInstructorNutriologo();
          alert('Nutriólogo asignado correctamente');
        } else {
          alert('Error: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al asignar nutriólogo');
      }
    }

    // Cargar todo al inicio
    loadUserData();
    cargarInstructorNutriologo();

    // Sistema de Carrito de Compras
    let carrito = JSON.parse(localStorage.getItem('carrito_fitandfuel')) || [];

    function actualizarBadgeCarrito() {
      const badge = document.getElementById('carrito-badge');
      const total = carrito.reduce((sum, item) => sum + item.cantidad, 0);
      if (total > 0) {
        badge.textContent = total;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }
    }

    window.agregarAlCarrito = function(tipo, id, nombre, precio, cantidad = 1) {
      const itemExistente = carrito.find(item => item.tipo === tipo && item.id === id);
      
      if (itemExistente) {
        itemExistente.cantidad += cantidad;
      } else {
        carrito.push({ tipo, id, nombre, precio, cantidad });
      }
      
      localStorage.setItem('carrito_fitandfuel', JSON.stringify(carrito));
      actualizarBadgeCarrito();
      
      // Notificación
      const toast = document.createElement('div');
      toast.className = 'position-fixed bottom-0 end-0 p-3';
      toast.style.zIndex = '9999';
      toast.innerHTML = `
        <div class="toast show" role="alert">
          <div class="toast-header bg-success text-white">
            <strong class="me-auto">✓ Agregado al carrito</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
          </div>
          <div class="toast-body bg-dark text-white">
            ${nombre} (x${cantidad})
          </div>
        </div>
      `;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
    }

    window.abrirTienda = async function() {
      try {
        const resProductos = await apiFetch('/obtener-productos.php');
        
        if (!resProductos.ok) {
          alert('Error al cargar productos');
          return;
        }
        
        const dataProductos = await resProductos.json();
        const productos = dataProductos.productos || dataProductos.data || dataProductos || [];
        
        const modalHTML = `
          <div class="modal fade" id="tiendaModal" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
              <div class="modal-content bg-dark text-white">
                <div class="modal-header border-success">
                  <h5 class="modal-title">🛒 Tienda FitAndFuel</h5>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-warning" onclick="verCarrito()">
                      Ver Carrito <span class="badge bg-danger">${carrito.reduce((sum, item) => sum + item.cantidad, 0)}</span>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                </div>
                <div class="modal-body" style="overflow-y:auto">
                  <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                      <button class="nav-link active">
                        🛍️ Productos y Suplementos
                      </button>
                    </li>
                  </ul>
                  
                  <div class="tab-content">
                    <div class="tab-pane fade show active">
                      ${productos.length === 0 ? 
                        '<p class="text-muted text-center py-5">No hay productos disponibles</p>' :
                        `<div class="row g-3">
                          ${productos.map(p => `
                            <div class="col-md-4 col-lg-3">
                              <div class="card bg-dark border-secondary h-100">
                                ${p.imagen_url ? `
                                  <img src="${p.imagen_url}" class="card-img-top" alt="${p.nombre}" style="height:200px;object-fit:cover">
                                ` : `
                                  <div class="bg-secondary" style="height:200px;display:flex;align-items:center;justify-content:center">
                                    <span class="text-muted">Sin imagen</span>
                                  </div>
                                `}
                                <div class="card-body">
                                  <h6 class="card-title">${p.nombre}</h6>
                                  <p class="card-text small text-muted">${p.descripcion || ''}</p>
                                  <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-success mb-0">$${parseFloat(p.precio).toFixed(2)}</span>
                                    <small class="text-muted">Stock: ${p.stock || 0}</small>
                                  </div>
                                  <button class="btn btn-success btn-sm w-100 mt-2" onclick="agregarAlCarrito('producto', ${p.id}, '${(p.nombre || '').replace(/'/g, "\\'")}', ${p.precio})">
                                    🛒 Agregar al carrito
                                  </button>
                                </div>
                              </div>
                            </div>
                          `).join('')}
                        </div>`
                      }
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const oldModal = document.getElementById('tiendaModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('tiendaModal'));
        modal.show();
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar la tienda');
      }
    }

    window.verCarrito = function() {
      if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
      }
      
      const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
      
      const modalHTML = `
        <div class="modal fade" id="carritoModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <h5 class="modal-title">🛒 Mi Carrito</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="table-responsive">
                  <table class="table table-dark">
                    <thead>
                      <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      ${carrito.map((item, idx) => `
                        <tr>
                          <td>${item.nombre}</td>
                          <td><span class="badge bg-${item.tipo === 'producto' ? 'primary' : 'success'}">${item.tipo === 'producto' ? 'Suplemento' : 'Plan'}</span></td>
                          <td>$${parseFloat(item.precio).toFixed(2)}</td>
                          <td>
                            ${item.tipo === 'producto' ? `
                              <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${idx}, -1)">-</button>
                                <span class="btn btn-outline-secondary disabled">${item.cantidad}</span>
                                <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${idx}, 1)">+</button>
                              </div>
                            ` : '1'}
                          </td>
                          <td>$${(item.precio * item.cantidad).toFixed(2)}</td>
                          <td>
                            <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${idx})">
                              🗑️
                            </button>
                          </td>
                        </tr>
                      `).join('')}
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th class="text-success">$${total.toFixed(2)}</th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                
                <div class="mt-3">
                  <label class="form-label">Dirección de envío (opcional)</label>
                  <textarea id="direccion-envio" class="form-control bg-dark text-white" rows="2" placeholder="Calle, número, colonia, ciudad..."></textarea>
                </div>
                
                <div class="mt-3">
                  <label class="form-label">Cupón de descuento (opcional)</label>
                  <div class="input-group">
                    <input type="text" id="cupon-codigo" class="form-control bg-dark text-white" placeholder="Ingresa código...">
                    <button class="btn btn-outline-warning" onclick="aplicarCupon()">Aplicar</button>
                  </div>
                  <div id="cupon-mensaje" class="small mt-1"></div>
                </div>
              </div>
              <div class="modal-footer border-success">
                <button class="btn btn-danger" onclick="vaciarCarrito()">Vaciar Carrito</button>
                <button class="btn btn-success" onclick="procesarPedido()">Procesar Pedido</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const oldModal = document.getElementById('carritoModal');
      if (oldModal) oldModal.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      const modal = new bootstrap.Modal(document.getElementById('carritoModal'));
      modal.show();
    }

    window.cambiarCantidad = function(index, delta) {
      if (carrito[index].cantidad + delta < 1) return;
      carrito[index].cantidad += delta;
      localStorage.setItem('carrito_fitandfuel', JSON.stringify(carrito));
      verCarrito();
      actualizarBadgeCarrito();
    }

    window.eliminarDelCarrito = function(index) {
      if (confirm('¿Eliminar este producto del carrito?')) {
        carrito.splice(index, 1);
        localStorage.setItem('carrito_fitandfuel', JSON.stringify(carrito));
        actualizarBadgeCarrito();
        if (carrito.length === 0) {
          bootstrap.Modal.getInstance(document.getElementById('carritoModal')).hide();
        } else {
          verCarrito();
        }
      }
    }

    window.vaciarCarrito = function() {
      if (confirm('¿Vaciar todo el carrito?')) {
        carrito = [];
        localStorage.setItem('carrito_fitandfuel', JSON.stringify(carrito));
        actualizarBadgeCarrito();
        bootstrap.Modal.getInstance(document.getElementById('carritoModal')).hide();
      }
    }

    window.procesarPedido = async function() {
      if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
      }
      
      const direccion = document.getElementById('direccion-envio').value.trim();
      const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
      let descuento = 0;
      
      if (cuponAplicado) {
        if (cuponAplicado.tipo_descuento === 'porcentaje') {
          descuento = (subtotal * cuponAplicado.valor_descuento) / 100;
        } else {
          descuento = Math.min(cuponAplicado.valor_descuento, subtotal);
        }
      }
      
      const total = subtotal - descuento;
      
      let mensajeConfirmacion = `¿Confirmar pedido?\n\nSubtotal: $${subtotal.toFixed(2)}`;
      if (descuento > 0) {
        mensajeConfirmacion += `\nDescuento: -$${descuento.toFixed(2)}`;
      }
      mensajeConfirmacion += `\nTotal: $${total.toFixed(2)}`;
      
      if (!confirm(mensajeConfirmacion)) {
        return;
      }
      
      try {
        const res = await apiFetch('/pedidos', {
          method: 'POST',
          body: JSON.stringify({
            items: carrito,
            direccion: direccion || null,
            metodo_pago: 'pendiente',
            cupon_id: cuponAplicado ? cuponAplicado.id : null
          })
        });
        
        const data = await res.json();
        
        if (data.success) {
          let mensaje = `¡Pedido creado exitosamente!\n\nID: ${data.pedido_id}\nSubtotal: $${data.subtotal.toFixed(2)}`;
          if (data.descuento > 0) {
            mensaje += `\nDescuento: -$${data.descuento.toFixed(2)}`;
          }
          mensaje += `\nTotal: $${data.total.toFixed(2)}\n\nRecibirás una confirmación por correo.`;
          
          alert(mensaje);
          carrito = [];
          cuponAplicado = null;
          localStorage.setItem('carrito_fitandfuel', JSON.stringify(carrito));
          actualizarBadgeCarrito();
          bootstrap.Modal.getInstance(document.getElementById('carritoModal')).hide();
          const tiendaModal = document.getElementById('tiendaModal');
          if (tiendaModal) bootstrap.Modal.getInstance(tiendaModal).hide();
        } else {
          alert('Error al procesar pedido: ' + data.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al procesar el pedido');
      }
    }

    // Inicializar badge del carrito
    actualizarBadgeCarrito();
    
    // Sistema de cupones
    let cuponAplicado = null;
    
    window.aplicarCupon = async function() {
      const codigo = document.getElementById('cupon-codigo').value.trim();
      const mensaje = document.getElementById('cupon-mensaje');
      
      if (!codigo) {
        mensaje.innerHTML = '<span class="text-danger">⚠️ Ingresa un código</span>';
        return;
      }
      
      try {
        const res = await fetch(`../../public/api/validar-cupon.php?codigo=${encodeURIComponent(codigo)}`);
        const data = await res.json();
        
        if (data.success) {
          cuponAplicado = data.cupon;
          
          const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
          
          if (subtotal < cuponAplicado.monto_minimo) {
            mensaje.innerHTML = `<span class="text-warning">⚠️ Monto mínimo: $${cuponAplicado.monto_minimo.toFixed(2)}</span>`;
            cuponAplicado = null;
            return;
          }
          
          let descuento = 0;
          if (cuponAplicado.tipo_descuento === 'porcentaje') {
            descuento = (subtotal * cuponAplicado.valor_descuento) / 100;
          } else {
            descuento = Math.min(cuponAplicado.valor_descuento, subtotal);
          }
          
          mensaje.innerHTML = `<span class="text-success">✓ Cupón aplicado: -$${descuento.toFixed(2)} (${cuponAplicado.descripcion})</span>`;
          verCarrito();
        } else {
          mensaje.innerHTML = `<span class="text-danger">⚠️ ${data.error}</span>`;
          cuponAplicado = null;
        }
      } catch (err) {
        console.error('Error:', err);
        mensaje.innerHTML = '<span class="text-danger">⚠️ Error al validar cupón</span>';
      }
    }
    
    // Historial de pedidos
    window.verHistorialPedidos = async function() {
      try {
        const res = await fetch('../../public/api/historial-pedidos.php');
        const data = await res.json();
        
        if (!data.success) {
          alert('Error al cargar pedidos: ' + data.error);
          return;
        }
        
        const pedidos = data.pedidos;
        let pedidosHTML = '';
        
        if (pedidos.length === 0) {
          pedidosHTML = '<div class="alert alert-info">No tienes pedidos aún</div>';
        } else {
          pedidos.forEach(pedido => {
            const estadoClass = {
              'pendiente': 'warning',
              'procesando': 'info',
              'enviado': 'primary',
              'entregado': 'success',
              'cancelado': 'danger'
            }[pedido.estado] || 'secondary';
            
            let itemsHTML = '';
            pedido.items.forEach(item => {
              itemsHTML += `
                <tr>
                  <td>${item.producto_nombre || 'Plan alimenticio'}</td>
                  <td>${item.cantidad}</td>
                  <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                  <td>$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
              `;
            });
            
            pedidosHTML += `
              <div class="card bg-dark text-white mb-3 border-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <span><strong>Pedido #${pedido.id}</strong> - ${new Date(pedido.fecha_pedido).toLocaleDateString('es-MX')}</span>
                  <span class="badge bg-${estadoClass}">${pedido.estado.toUpperCase()}</span>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-sm table-dark">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th>Cant.</th>
                          <th>Precio</th>
                          <th>Subtotal</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${itemsHTML}
                      </tbody>
                    </table>
                  </div>
                  ${pedido.subtotal ? `<p class="mb-1"><strong>Subtotal:</strong> $${parseFloat(pedido.subtotal).toFixed(2)}</p>` : ''}
                  ${pedido.descuento > 0 ? `<p class="mb-1 text-success"><strong>Descuento ${pedido.cupon_codigo ? '('+pedido.cupon_codigo+')' : ''}:</strong> -$${parseFloat(pedido.descuento).toFixed(2)}</p>` : ''}
                  <p class="mb-1"><strong>Total:</strong> $${parseFloat(pedido.total).toFixed(2)}</p>
                  ${pedido.direccion_envio ? `<p class="mb-0 small text-muted"><strong>Dirección:</strong> ${pedido.direccion_envio}</p>` : ''}
                </div>
              </div>
            `;
          });
        }
        
        const modalHTML = `
          <div class="modal fade" id="historialModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content bg-dark text-white border-info">
                <div class="modal-header border-info">
                  <h5 class="modal-title">📦 Historial de Pedidos</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  ${pedidosHTML}
                </div>
                <div class="modal-footer border-info">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const oldModal = document.getElementById('historialModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('historialModal'));
        modal.show();
        
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar historial de pedidos');
      }
    }
    
    // ============================================
    // SISTEMA DE PROGRESO Y MÉTRICAS
    // ============================================
    
    window.abrirProgreso = async function() {
      const modalHTML = `
        <div class="modal fade" id="progresoModal" tabindex="-1">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <h5 class="modal-title">📊 Mi Progreso y Métricas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                  <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-graficas">Gráficas</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-registros">Registros</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-objetivos">Objetivos</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-nuevo">Nuevo Registro</button>
                  </li>
                </ul>
                
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="tab-graficas">
                    <div id="estadisticas-resumen" class="row g-3 mb-4"></div>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="card bg-dark border-secondary">
                          <div class="card-body">
                            <h6 class="card-title">Evolución de Peso</h6>
                            <canvas id="chart-peso"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card bg-dark border-secondary">
                          <div class="card-body">
                            <h6 class="card-title">% Grasa Corporal</h6>
                            <canvas id="chart-grasa"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card bg-dark border-secondary">
                          <div class="card-body">
                            <h6 class="card-title">Medidas Corporales</h6>
                            <canvas id="chart-medidas"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card bg-dark border-secondary">
                          <div class="card-body">
                            <h6 class="card-title">IMC</h6>
                            <canvas id="chart-imc"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="tab-pane fade" id="tab-registros">
                    <div id="lista-registros"></div>
                  </div>
                  
                  <div class="tab-pane fade" id="tab-objetivos">
                    <button class="btn btn-success btn-sm mb-3" onclick="mostrarFormObjetivo()">+ Nuevo Objetivo</button>
                    <div id="lista-objetivos"></div>
                  </div>
                  
                  <div class="tab-pane fade" id="tab-nuevo">
                    <form id="form-progreso" onsubmit="guardarProgreso(event)">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">Fecha</label>
                          <input type="date" name="fecha_registro" class="form-control bg-dark text-white" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Peso (kg)</label>
                          <input type="number" step="0.1" name="peso" class="form-control bg-dark text-white" placeholder="75.5">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Altura (cm)</label>
                          <input type="number" step="0.1" name="altura" class="form-control bg-dark text-white" placeholder="175">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">% Grasa Corporal</label>
                          <input type="number" step="0.1" name="porcentaje_grasa" class="form-control bg-dark text-white" placeholder="18.5">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Pecho (cm)</label>
                          <input type="number" step="0.1" name="pecho" class="form-control bg-dark text-white" placeholder="100">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Cintura (cm)</label>
                          <input type="number" step="0.1" name="cintura" class="form-control bg-dark text-white" placeholder="85">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Cadera (cm)</label>
                          <input type="number" step="0.1" name="cadera" class="form-control bg-dark text-white" placeholder="95">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Brazo Derecho (cm)</label>
                          <input type="number" step="0.1" name="brazo_derecho" class="form-control bg-dark text-white" placeholder="35">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Brazo Izquierdo (cm)</label>
                          <input type="number" step="0.1" name="brazo_izquierdo" class="form-control bg-dark text-white" placeholder="35">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Pierna Derecha (cm)</label>
                          <input type="number" step="0.1" name="pierna_derecha" class="form-control bg-dark text-white" placeholder="55">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Pierna Izquierda (cm)</label>
                          <input type="number" step="0.1" name="pierna_izquierda" class="form-control bg-dark text-white" placeholder="55">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Masa Muscular (kg)</label>
                          <input type="number" step="0.1" name="masa_muscular" class="form-control bg-dark text-white" placeholder="55">
                        </div>
                        <div class="col-12">
                          <label class="form-label">Notas</label>
                          <textarea name="notas" class="form-control bg-dark text-white" rows="3" placeholder="Cómo te sientes, cambios notados, etc..."></textarea>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-success mt-3">Guardar Registro</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const oldModal = document.getElementById('progresoModal');
      if (oldModal) oldModal.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      const modal = new bootstrap.Modal(document.getElementById('progresoModal'));
      modal.show();
      
      // Cargar datos
      await cargarDatosProgreso();
      await cargarObjetivos();
    }
    
    async function cargarDatosProgreso() {
      try {
        const res = await fetch('../../public/api/obtener-progreso.php');
        const data = await res.json();
        
        if (!data.success) {
          console.error('Error:', data.error);
          return;
        }
        
        const registros = data.registros;
        const stats = data.estadisticas;
        
        // Mostrar estadísticas resumidas
        let statsHTML = '';
        if (stats.total_registros > 0) {
          statsHTML = `
            <div class="col-md-3">
              <div class="card bg-secondary text-center">
                <div class="card-body py-2">
                  <h6 class="mb-0">${stats.total_registros}</h6>
                  <small class="text-muted">Registros</small>
                </div>
              </div>
            </div>
          `;
          
          if (stats.cambio_peso !== null) {
            const colorPeso = stats.cambio_peso < 0 ? 'success' : 'warning';
            const signo = stats.cambio_peso > 0 ? '+' : '';
            statsHTML += `
              <div class="col-md-3">
                <div class="card bg-secondary text-center">
                  <div class="card-body py-2">
                    <h6 class="mb-0 text-${colorPeso}">${signo}${stats.cambio_peso} kg</h6>
                    <small class="text-muted">Cambio Peso</small>
                  </div>
                </div>
              </div>
            `;
          }
          
          if (stats.cambio_grasa !== null) {
            const colorGrasa = stats.cambio_grasa < 0 ? 'success' : 'warning';
            const signo = stats.cambio_grasa > 0 ? '+' : '';
            statsHTML += `
              <div class="col-md-3">
                <div class="card bg-secondary text-center">
                  <div class="card-body py-2">
                    <h6 class="mb-0 text-${colorGrasa}">${signo}${stats.cambio_grasa}%</h6>
                    <small class="text-muted">Cambio Grasa</small>
                  </div>
                </div>
              </div>
            `;
          }
          
          if (stats.cambio_cintura !== null) {
            const colorCintura = stats.cambio_cintura < 0 ? 'success' : 'warning';
            const signo = stats.cambio_cintura > 0 ? '+' : '';
            statsHTML += `
              <div class="col-md-3">
                <div class="card bg-secondary text-center">
                  <div class="card-body py-2">
                    <h6 class="mb-0 text-${colorCintura}">${signo}${stats.cambio_cintura} cm</h6>
                    <small class="text-muted">Cambio Cintura</small>
                  </div>
                </div>
              </div>
            `;
          }
        }
        document.getElementById('estadisticas-resumen').innerHTML = statsHTML;
        
        // Crear gráficas
        if (registros.length > 0) {
          crearGraficas(registros);
          mostrarListaRegistros(registros);
        } else {
          document.getElementById('chart-peso').parentElement.innerHTML = '<p class="text-muted text-center py-4">No hay registros aún. ¡Agrega tu primer registro!</p>';
        }
        
      } catch (err) {
        console.error('Error:', err);
      }
    }
    
    function crearGraficas(registros) {
      const registrosOrdenados = [...registros].reverse();
      const fechas = registrosOrdenados.map(r => new Date(r.fecha_registro).toLocaleDateString('es-MX', {month: 'short', day: 'numeric'}));
      
      const chartConfig = {
        type: 'line',
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { 
              beginAtZero: false,
              ticks: { color: '#fff' },
              grid: { color: 'rgba(255,255,255,0.1)' }
            },
            x: { 
              ticks: { color: '#fff' },
              grid: { color: 'rgba(255,255,255,0.1)' }
            }
          }
        }
      };
      
      // Gráfica de Peso
      const pesos = registrosOrdenados.map(r => r.peso).filter(p => p !== null);
      if (pesos.length > 0) {
        new Chart(document.getElementById('chart-peso'), {
          ...chartConfig,
          data: {
            labels: fechas.slice(0, pesos.length),
            datasets: [{
              label: 'Peso (kg)',
              data: pesos,
              borderColor: '#f07008',
              backgroundColor: 'rgba(240,112,8,0.1)',
              tension: 0.4,
              fill: true
            }]
          }
        });
      }
      
      // Gráfica de Grasa
      const grasa = registrosOrdenados.map(r => r.porcentaje_grasa).filter(g => g !== null);
      if (grasa.length > 0) {
        new Chart(document.getElementById('chart-grasa'), {
          ...chartConfig,
          data: {
            labels: fechas.slice(0, grasa.length),
            datasets: [{
              label: '% Grasa',
              data: grasa,
              borderColor: '#ef4444',
              backgroundColor: 'rgba(239,68,68,0.1)',
              tension: 0.4,
              fill: true
            }]
          }
        });
      }
      
      // Gráfica de Medidas
      const cintura = registrosOrdenados.map(r => r.cintura).filter(c => c !== null);
      const pecho = registrosOrdenados.map(r => r.pecho).filter(p => p !== null);
      if (cintura.length > 0 || pecho.length > 0) {
        new Chart(document.getElementById('chart-medidas'), {
          ...chartConfig,
          data: {
            labels: fechas,
            datasets: [
              {
                label: 'Cintura (cm)',
                data: registrosOrdenados.map(r => r.cintura),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                tension: 0.4
              },
              {
                label: 'Pecho (cm)',
                data: registrosOrdenados.map(r => r.pecho),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.1)',
                tension: 0.4
              }
            ]
          },
          options: {
            ...chartConfig.options,
            plugins: { legend: { display: true, labels: { color: '#fff' } } }
          }
        });
      }
      
      // Gráfica de IMC
      const imc = registrosOrdenados.map(r => r.imc).filter(i => i !== null);
      if (imc.length > 0) {
        new Chart(document.getElementById('chart-imc'), {
          ...chartConfig,
          data: {
            labels: fechas.slice(0, imc.length),
            datasets: [{
              label: 'IMC',
              data: imc,
              borderColor: '#a855f7',
              backgroundColor: 'rgba(168,85,247,0.1)',
              tension: 0.4,
              fill: true
            }]
          }
        });
      }
    }
    
    function mostrarListaRegistros(registros) {
      let html = '<div class="table-responsive"><table class="table table-sm table-dark table-striped"><thead><tr><th>Fecha</th><th>Peso</th><th>% Grasa</th><th>Cintura</th><th>IMC</th><th>Notas</th></tr></thead><tbody>';
      
      registros.forEach(r => {
        html += `
          <tr>
            <td>${new Date(r.fecha_registro).toLocaleDateString('es-MX')}</td>
            <td>${r.peso ? r.peso + ' kg' : '-'}</td>
            <td>${r.porcentaje_grasa ? r.porcentaje_grasa + '%' : '-'}</td>
            <td>${r.cintura ? r.cintura + ' cm' : '-'}</td>
            <td>${r.imc ? r.imc.toFixed(1) : '-'}</td>
            <td class="small">${r.notas || '-'}</td>
          </tr>
        `;
      });
      
      html += '</tbody></table></div>';
      document.getElementById('lista-registros').innerHTML = html;
    }
    
    window.guardarProgreso = async function(event) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      
      // Convertir valores vacíos a null
      Object.keys(data).forEach(key => {
        if (data[key] === '') data[key] = null;
      });
      
      try {
        const res = await fetch('../../public/api/guardar-progreso.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (result.success) {
          alert('✓ Registro guardado exitosamente' + (result.imc ? `\\nIMC: ${result.imc.toFixed(1)}` : ''));
          form.reset();
          form.querySelector('[name="fecha_registro"]').value = new Date().toISOString().split('T')[0];
          await cargarDatosProgreso();
          
          // Cambiar a tab de gráficas
          const tabGraficas = document.querySelector('[data-bs-target="#tab-graficas"]');
          if (tabGraficas) tabGraficas.click();
        } else {
          alert('Error: ' + result.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al guardar registro');
      }
    }
    
    async function cargarObjetivos() {
      try {
        const res = await fetch('../../public/api/obtener-objetivos.php');
        const data = await res.json();
        
        if (!data.success) {
          console.error('Error:', data.error);
          return;
        }
        
        const objetivos = data.objetivos;
        let html = '';
        
        if (objetivos.length === 0) {
          html = '<div class="alert alert-info">No tienes objetivos. ¡Crea tu primer objetivo!</div>';
        } else {
          objetivos.forEach(obj => {
            const porcentaje = obj.porcentaje_progreso || 0;
            const colorBarra = porcentaje < 50 ? 'warning' : porcentaje < 100 ? 'info' : 'success';
            const iconoTipo = {
              'peso': '⚖️',
              'grasa': '🔥',
              'musculo': '💪',
              'medidas': '📏',
              'personalizado': '🎯'
            }[obj.tipo_objetivo] || '🎯';
            
            html += `
              <div class="card bg-secondary mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">${iconoTipo} ${obj.descripcion}</h6>
                    ${obj.completado ? '<span class="badge bg-success">✓ Completado</span>' : ''}
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-4">
                      <small class="text-muted">Inicial:</small><br>
                      <strong>${obj.valor_actual || '-'} ${obj.unidad}</strong>
                    </div>
                    <div class="col-4">
                      <small class="text-muted">Objetivo:</small><br>
                      <strong>${obj.valor_objetivo} ${obj.unidad}</strong>
                    </div>
                    <div class="col-4">
                      <small class="text-muted">Progreso:</small><br>
                      <strong class="text-${colorBarra}">${porcentaje}%</strong>
                    </div>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-${colorBarra}" style="width: ${porcentaje}%"></div>
                  </div>
                  ${obj.fecha_objetivo ? `<small class="text-muted">Meta: ${new Date(obj.fecha_objetivo).toLocaleDateString('es-MX')}</small>` : ''}
                </div>
              </div>
            `;
          });
        }
        
        document.getElementById('lista-objetivos').innerHTML = html;
        
      } catch (err) {
        console.error('Error:', err);
      }
    }
    
    window.mostrarFormObjetivo = function() {
      const formHTML = `
        <div class="card bg-secondary mb-3" id="form-nuevo-objetivo">
          <div class="card-body">
            <h6>Crear Nuevo Objetivo</h6>
            <form onsubmit="guardarObjetivo(event)">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Tipo</label>
                  <select name="tipo_objetivo" class="form-select form-select-sm bg-dark text-white" required>
                    <option value="peso">Peso</option>
                    <option value="grasa">% Grasa Corporal</option>
                    <option value="musculo">Masa Muscular</option>
                    <option value="medidas">Medidas</option>
                    <option value="personalizado">Personalizado</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Unidad</label>
                  <select name="unidad" class="form-select form-select-sm bg-dark text-white">
                    <option value="kg">kg</option>
                    <option value="%">%</option>
                    <option value="cm">cm</option>
                    <option value="lbs">lbs</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small">Descripción</label>
                  <input type="text" name="descripcion" class="form-control form-control-sm bg-dark text-white" placeholder="Ej: Reducir peso a 75kg" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Valor Actual</label>
                  <input type="number" step="0.1" name="valor_actual" class="form-control form-control-sm bg-dark text-white" placeholder="85">
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Valor Objetivo</label>
                  <input type="number" step="0.1" name="valor_objetivo" class="form-control form-control-sm bg-dark text-white" placeholder="75" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Fecha Meta</label>
                  <input type="date" name="fecha_objetivo" class="form-control form-control-sm bg-dark text-white">
                </div>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('form-nuevo-objetivo').remove()">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      `;
      
      document.getElementById('lista-objetivos').insertAdjacentHTML('afterbegin', formHTML);
    }
    
    window.guardarObjetivo = async function(event) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      
      // Convertir vacíos a null
      Object.keys(data).forEach(key => {
        if (data[key] === '') data[key] = null;
      });
      
      data.fecha_inicio = new Date().toISOString().split('T')[0];
      
      try {
        const res = await fetch('../../public/api/guardar-objetivo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (result.success) {
          alert('✓ Objetivo creado exitosamente');
          document.getElementById('form-nuevo-objetivo').remove();
          await cargarObjetivos();
        } else {
          alert('Error: ' + result.error);
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Error al guardar objetivo');
      }
    }
    
    // ============================================
    // SISTEMA DE NOTIFICACIONES
    // ============================================
    
    async function cargarNotificaciones() {
      try {
        const res = await fetch('../../public/api/obtener-notificaciones.php?no_leidas=1&limite=50');
        const data = await res.json();
        
        if (data.success) {
          const badge = document.getElementById('notif-badge');
          const total = data.total_no_leidas;
          
          if (total > 0) {
            badge.textContent = total > 99 ? '99+' : total;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }
      } catch (err) {
        console.error('Error al cargar notificaciones:', err);
      }
    }
    
    window.abrirNotificaciones = async function() {
      try {
        const res = await fetch('../../public/api/obtener-notificaciones.php?limite=30');
        const data = await res.json();
        
        if (!data.success) {
          alert('Error al cargar notificaciones: ' + data.error);
          return;
        }
        
        const notificaciones = data.notificaciones;
        let notifsHTML = '';
        
        if (notificaciones.length === 0) {
          notifsHTML = '<div class="alert alert-info">No tienes notificaciones</div>';
        } else {
          notificaciones.forEach(notif => {
            const leida = notif.leida == 1;
            const bgClass = leida ? 'bg-dark' : 'bg-secondary';
            const borderClass = notif.importante == 1 ? 'border-warning' : 'border-secondary';
            const fechaTexto = formatearFecha(notif.fecha_creacion);
            
            notifsHTML += `
              <div class="card ${bgClass} mb-2 ${borderClass}" style="border-left: 3px solid;">
                <div class="card-body py-2 px-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1" style="cursor:pointer" onclick="marcarNotificacionLeida(${notif.id})">
                      <div class="d-flex align-items-center gap-2">
                        <span style="font-size:20px">${notif.icono}</span>
                        <div>
                          <h6 class="mb-0 small">${notif.titulo}</h6>
                          <p class="mb-0 small text-muted">${notif.mensaje}</p>
                          <small class="text-muted" style="font-size:10px">${fechaTexto}</small>
                        </div>
                      </div>
                    </div>
                    ${!leida ? '<span class="badge bg-primary" style="font-size:8px">NUEVO</span>' : ''}
                  </div>
                </div>
              </div>
            `;
          });
        }
        
        const modalHTML = `
          <div class="modal fade" id="notificacionesModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content bg-dark text-white border-primary">
                <div class="modal-header border-primary">
                  <h5 class="modal-title">🔔 Notificaciones</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  \${notifsHTML}
                </div>
                <div class="modal-footer border-primary">
                  \${data.total_no_leidas > 0 ? '<button class="btn btn-sm btn-success" onclick="marcarTodasLeidas()">Marcar todas como leídas</button>' : ''}
                  <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const oldModal = document.getElementById('notificacionesModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('notificacionesModal'));
        modal.show();
        
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar notificaciones');
      }
    }
    
    window.marcarNotificacionLeida = async function(notifId) {
      try {
        const res = await fetch('../../public/api/marcar-notificacion.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ notificacion_id: notifId })
        });
        
        const data = await res.json();
        
        if (data.success) {
          await cargarNotificaciones();
          // Recargar modal
          const modalElement = document.getElementById('notificacionesModal');
          if (modalElement) {
            bootstrap.Modal.getInstance(modalElement)?.hide();
            await abrirNotificaciones();
          }
        }
      } catch (err) {
        console.error('Error:', err);
      }
    }
    
    window.marcarTodasLeidas = async function() {
      try {
        const res = await apiFetch('/notificaciones/marcar-todas-leidas/<?php echo $_SESSION['user_id']; ?>', {
          method: 'PUT'
        });
        
        const data = await res.json();
        
        if (data.success) {
          await cargarNotificaciones();
          bootstrap.Modal.getInstance(document.getElementById('notificacionesModal'))?.hide();
          setTimeout(() => abrirNotificaciones(), 300);
        }
      } catch (err) {
        console.error('Error:', err);
      }
    }
    
    function formatearFecha(fechaISO) {
      const fecha = new Date(fechaISO);
      const ahora = new Date();
      const diffMs = ahora - fecha;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHoras = Math.floor(diffMs / 3600000);
      const diffDias = Math.floor(diffMs / 86400000);
      
      if (diffMins < 1) return 'Ahora';
      if (diffMins < 60) return `Hace ${diffMins} min`;
      if (diffHoras < 24) return `Hace ${diffHoras}h`;
      if (diffDias < 7) return `Hace ${diffDias}d`;
      return fecha.toLocaleDateString('es-MX', { month: 'short', day: 'numeric' });
    }
    
    // Ver Mis Rutinas
    window.verMisRutinas = async function() {
      try {
        const res = await fetch('../../public/api/obtener-rutinas-usuario.php');
        const data = await res.json();
        
        if (!data.success) {
          alert('Error al cargar rutinas');
          return;
        }
        
        const rutinas = data.rutinas || [];
        
        // Agrupar ejercicios por día
        const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        
        let rutinasHTML = '';
        if (rutinas.length === 0) {
          rutinasHTML = '<div class="alert alert-info">No tienes rutinas asignadas aún. Tu instructor te asignará una rutina personalizada.</div>';
        } else {
          rutinas.forEach(rutina => {
            // Agrupar ejercicios por día
            const ejerciciosPorDia = {};
            rutina.ejercicios.forEach(ej => {
              const dia = ej.dia_semana || 'Sin asignar';
              if (!ejerciciosPorDia[dia]) {
                ejerciciosPorDia[dia] = [];
              }
              ejerciciosPorDia[dia].push(ej);
            });
            
            rutinasHTML += `
              <div class="card bg-secondary mb-3">
                <div class="card-header bg-dark">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h5 class="mb-1">${rutina.nombre}</h5>
                      <p class="small text-muted mb-0">${rutina.descripcion || ''}</p>
                    </div>
                    <span class="badge ${rutina.activo == 1 ? 'bg-success' : 'bg-secondary'}">${rutina.activo == 1 ? 'Activa' : 'Inactiva'}</span>
                  </div>
                  <div class="mt-2">
                    <span class="badge bg-info me-2">Duración: ${rutina.duracion_semanas || 4} semanas</span>
                    <span class="badge bg-warning me-2">Nivel: ${rutina.nivel_dificultad || 'Intermedio'}</span>
                    <span class="badge bg-primary">Objetivo: ${rutina.objetivo || 'General'}</span>
                  </div>
                  <p class="small text-muted mb-0 mt-2">
                    Instructor: ${rutina.instructor_nombre} ${rutina.instructor_apellido} | 
                    Asignada: ${new Date(rutina.fecha_asignacion).toLocaleDateString('es-MX')}
                  </p>
                </div>
                <div class="card-body">
                  ${Object.keys(ejerciciosPorDia).length === 0 ? 
                    '<p class="text-muted">Esta rutina no tiene ejercicios asignados aún.</p>' :
                    diasSemana.map((dia, idx) => {
                      const ejercicios = ejerciciosPorDia[idx + 1] || [];
                      if (ejercicios.length === 0) return '';
                      
                      return `
                        <div class="mb-4">
                          <h6 class="text-success border-bottom border-success pb-2">📅 ${dia}</h6>
                          <div class="row g-2">
                            ${ejercicios.map(ej => `
                              <div class="col-md-6">
                                <div class="d-flex gap-2 align-items-center bg-dark p-2 rounded">
                                  ${ej.gif_url ? 
                                    `<img src="${ej.gif_url}" style="width:60px;height:60px;object-fit:cover;border-radius:8px" onerror="this.style.display='none'">` :
                                    '<div class="bg-secondary" style="width:60px;height:60px;border-radius:8px"></div>'
                                  }
                                  <div class="flex-grow-1">
                                    <h6 class="mb-0 small">${ej.nombre}</h6>
                                    <small class="text-muted">${ej.grupo_muscular || ''}</small><br>
                                    <small class="text-success">${ej.series} series x ${ej.repeticiones} reps</small>
                                    <small class="text-info ms-2">Descanso: ${ej.descanso_seg}s</small>
                                  </div>
                                </div>
                              </div>
                            `).join('')}
                          </div>
                        </div>
                      `;
                    }).join('')
                  }
                </div>
              </div>
            `;
          });
        }
        
        const modalHTML = `
          <div class="modal fade" id="rutinasModal" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
              <div class="modal-content bg-dark text-white">
                <div class="modal-header border-success">
                  <h5 class="modal-title">💪 Mis Rutinas de Entrenamiento</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="overflow-y:auto">
                  ${rutinasHTML}
                </div>
                <div class="modal-footer border-secondary">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const oldModal = document.getElementById('rutinasModal');
        if (oldModal) oldModal.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('rutinasModal'));
        modal.show();
        
      } catch (err) {
        console.error('Error:', err);
        alert('Error al cargar rutinas');
      }
    }

    // ============ NUTRICIÓN ============
    let fechaNutricion = new Date();
    let tipoComidaActual = '';

    async function abrirNutricion() {
      const modal = new bootstrap.Modal(document.getElementById('nutricionModal'));
      modal.show();
      fechaNutricion = new Date();
      await cargarResumenNutricional();
    }

    async function cargarResumenNutricional() {
      try {
        const fechaStr = fechaNutricion.toISOString().split('T')[0];
        document.getElementById('fecha-nutricion').textContent = fechaNutricion.toLocaleDateString('es-MX', {
          weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });

        const res = await fetch(`../../public/api/resumen-nutricional.php?fecha=${fechaStr}`);
        const data = await res.json();

        if (data.success) {
          // Actualizar totales
          const totales = data.totales;
          const metas = data.metas || {
            calorias_objetivo: 2000,
            proteinas_objetivo: 150,
            carbohidratos_objetivo: 200,
            grasas_objetivo: 65,
            agua_vasos_objetivo: 8
          };

          // Calorías
          document.getElementById('cal-consumidas').textContent = Math.round(totales.calorias);
          document.getElementById('cal-objetivo').textContent = metas.calorias_objetivo;
          const calRestantes = metas.calorias_objetivo - totales.calorias;
          document.getElementById('cal-restantes').textContent = calRestantes > 0 ? 
            `${Math.round(calRestantes)} restantes` : 
            `${Math.abs(Math.round(calRestantes))} excedidas`;
          document.getElementById('cal-restantes').className = calRestantes > 0 ? 'small text-success' : 'small text-danger';
          
          const calPorcentaje = (totales.calorias / metas.calorias_objetivo) * 100;
          document.getElementById('cal-progress').style.width = Math.min(calPorcentaje, 100) + '%';
          document.getElementById('cal-progress').className = calPorcentaje > 100 ? 
            'progress-bar bg-danger' : 'progress-bar bg-success';

          // Macros
          document.getElementById('prot-consumidas').textContent = Math.round(totales.proteinas);
          document.getElementById('prot-objetivo').textContent = Math.round(metas.proteinas_objetivo);
          document.getElementById('carbs-consumidos').textContent = Math.round(totales.carbohidratos);
          document.getElementById('carbs-objetivo').textContent = Math.round(metas.carbohidratos_objetivo);
          document.getElementById('grasas-consumidas').textContent = Math.round(totales.grasas);
          document.getElementById('grasas-objetivo').textContent = Math.round(metas.grasas_objetivo);

          // Agua
          const aguaVasos = data.agua || 0;
          document.getElementById('agua-vasos').textContent = aguaVasos;
          renderizarAgua(aguaVasos, metas.agua_vasos_objetivo);

          // Renderizar comidas por tipo
          renderizarComidas(data.por_comida);
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }

    function renderizarAgua(vasos, objetivo) {
      const container = document.getElementById('agua-visual');
      container.innerHTML = '';
      
      for (let i = 0; i < objetivo; i++) {
        const vaso = document.createElement('div');
        vaso.style.cssText = 'flex:1;height:30px;border-radius:4px;cursor:pointer';
        vaso.style.background = i < vasos ? 
          'linear-gradient(135deg, rgba(59,130,246,0.5), rgba(59,130,246,0.8))' : 
          'rgba(255,255,255,0.1)';
        vaso.onclick = () => { if (i >= vasos) registrarAgua(); };
        container.appendChild(vaso);
      }
    }

    function renderizarComidas(porComida) {
      ['desayuno', 'comida', 'cena', 'snack'].forEach(tipo => {
        const container = document.getElementById(`lista-${tipo}`);
        const items = porComida[tipo] || [];
        
        if (items.length === 0) {
          container.innerHTML = '<p class="small text-muted">No hay alimentos registrados</p>';
          return;
        }

        container.innerHTML = items.map(item => `
          <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded" style="background:rgba(255,255,255,0.03)">
            <div class="flex-grow-1">
              <div class="small fw-bold">${item.nombre}${item.marca ? ` (${item.marca})` : ''}</div>
              <div class="small text-muted">${Math.round(item.cantidad_gramos)}g • ${Math.round(item.calorias_consumidas)} cal</div>
            </div>
            <div class="text-end small">
              <div class="text-success">${Math.round(item.proteinas_consumidas)}g P</div>
              <div class="text-primary">${Math.round(item.carbos_consumidos)}g C</div>
              <div class="text-warning">${Math.round(item.grasas_consumidas)}g G</div>
            </div>
            <button class="btn btn-sm btn-outline-danger ms-2" onclick="eliminarComida(${item.registro_id})">
              🗑️
            </button>
          </div>
        `).join('');
      });
    }

    async function registrarAgua() {
      try {
        const fechaStr = fechaNutricion.toISOString().split('T')[0];
        const res = await fetch('../../public/api/registrar-agua.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ vasos: 1, fecha: fechaStr })
        });

        const data = await res.json();
        if (data.success) {
          await cargarResumenNutricional();
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }

    function cambiarDia(offset) {
      if (offset === 0) {
        fechaNutricion = new Date();
      } else {
        fechaNutricion.setDate(fechaNutricion.getDate() + offset);
      }
      cargarResumenNutricional();
    }

    async function agregarComida(tipoComida) {
      tipoComidaActual = tipoComida;
      
      const modalHTML = `
        <div class="modal fade" id="buscarAlimentoModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">Buscar Alimento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="text" id="busqueda-alimento" class="form-control mb-3" placeholder="🔍 Buscar por nombre..." oninput="buscarAlimentos()">
                <div id="resultados-busqueda" style="max-height:400px;overflow-y:auto">
                  <p class="text-muted text-center">Escribe para buscar alimentos</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const old = document.getElementById('buscarAlimentoModal');
      if (old) old.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      const modal = new bootstrap.Modal(document.getElementById('buscarAlimentoModal'));
      modal.show();
    }

    let timeoutBusqueda;
    async function buscarAlimentos() {
      clearTimeout(timeoutBusqueda);
      timeoutBusqueda = setTimeout(async () => {
        const busqueda = document.getElementById('busqueda-alimento').value.trim();
        
        if (busqueda.length < 2) {
          document.getElementById('resultados-busqueda').innerHTML = '<p class="text-muted text-center">Escribe al menos 2 caracteres</p>';
          return;
        }

        try {
          const res = await fetch(`../../public/api/buscar-alimentos.php?q=${encodeURIComponent(busqueda)}`);
          const data = await res.json();

          if (data.success && data.alimentos.length > 0) {
            document.getElementById('resultados-busqueda').innerHTML = data.alimentos.map(alimento => `
              <div class="p-2 mb-2 rounded" style="background:rgba(255,255,255,0.03);cursor:pointer" onclick="seleccionarAlimento(${alimento.id}, '${alimento.nombre.replace(/'/g, "\\'")}', ${alimento.calorias}, ${alimento.porcion_gramos})">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-bold">${alimento.nombre}</div>
                    <div class="small text-muted">${alimento.marca || 'Natural'} • ${alimento.categoria || 'General'}</div>
                  </div>
                  <div class="text-end small">
                    <div>${Math.round(alimento.calorias)} cal</div>
                    <div class="text-muted">${alimento.porcion_gramos}g</div>
                  </div>
                </div>
                <div class="small text-muted mt-1">
                  P: ${Math.round(alimento.proteinas)}g | C: ${Math.round(alimento.carbohidratos)}g | G: ${Math.round(alimento.grasas)}g
                </div>
              </div>
            `).join('');
          } else {
            document.getElementById('resultados-busqueda').innerHTML = '<p class="text-muted text-center">No se encontraron alimentos</p>';
          }
        } catch (error) {
          console.error('Error:', error);
        }
      }, 300);
    }

    async function seleccionarAlimento(id, nombre, calorias, porcionGramos) {
      bootstrap.Modal.getInstance(document.getElementById('buscarAlimentoModal')).hide();
      
      const modalHTML = `
        <div class="modal fade" id="cantidadAlimentoModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">${nombre}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <label class="form-label">Cantidad (gramos)</label>
                <input type="number" id="cantidad-gramos" class="form-control mb-3" value="${porcionGramos}" min="1" oninput="actualizarPreview(${calorias}, ${porcionGramos})">
                <div class="alert alert-info">
                  <strong id="preview-calorias">${Math.round(calorias)}</strong> calorías
                </div>
              </div>
              <div class="modal-footer border-secondary">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" onclick="confirmarAgregarAlimento(${id})">Agregar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const old = document.getElementById('cantidadAlimentoModal');
      if (old) old.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      const modal = new bootstrap.Modal(document.getElementById('cantidadAlimentoModal'));
      modal.show();
    }

    function actualizarPreview(caloriasPorPorcion, porcionGramos) {
      const cantidad = parseFloat(document.getElementById('cantidad-gramos').value) || 0;
      const calorias = (cantidad / porcionGramos) * caloriasPorPorcion;
      document.getElementById('preview-calorias').textContent = Math.round(calorias);
    }

    async function confirmarAgregarAlimento(alimentoId) {
      try {
        const cantidad = parseFloat(document.getElementById('cantidad-gramos').value);
        const fechaStr = fechaNutricion.toISOString().split('T')[0];

        const res = await fetch('../../public/api/registrar-comida.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            alimento_id: alimentoId,
            cantidad_gramos: cantidad,
            tipo_comida: tipoComidaActual,
            fecha_registro: fechaStr
          })
        });

        const data = await res.json();
        
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('cantidadAlimentoModal')).hide();
          await cargarResumenNutricional();
        } else {
          alert('Error: ' + data.error);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al agregar alimento');
      }
    }

    async function eliminarComida(registroId) {
      if (!confirm('¿Eliminar este alimento?')) return;
      
      try {
        const res = await fetch(`../../public/api/eliminar-comida.php?id=${registroId}`, {
          method: 'DELETE'
        });
        
        const data = await res.json();
        if (data.success) {
          await cargarResumenNutricional();
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }

    function abrirConfigMetas() {
      const modalHTML = `
        <div class="modal fade" id="metasNutricionModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">⚙️ Configurar Metas Nutricionales</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Calorías Objetivo</label>
                  <input type="number" id="meta-calorias" class="form-control" value="2000" min="1000" max="5000">
                </div>
                <div class="mb-3">
                  <label class="form-label">Proteína (g)</label>
                  <input type="number" id="meta-proteinas" class="form-control" value="150" min="0" max="500">
                </div>
                <div class="mb-3">
                  <label class="form-label">Carbohidratos (g)</label>
                  <input type="number" id="meta-carbos" class="form-control" value="200" min="0" max="800">
                </div>
                <div class="mb-3">
                  <label class="form-label">Grasas (g)</label>
                  <input type="number" id="meta-grasas" class="form-control" value="65" min="0" max="300">
                </div>
                <div class="mb-3">
                  <label class="form-label">Agua (vasos/día)</label>
                  <input type="number" id="meta-agua" class="form-control" value="8" min="1" max="20">
                </div>
              </div>
              <div class="modal-footer border-secondary">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" onclick="guardarMetas()">Guardar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const old = document.getElementById('metasNutricionModal');
      if (old) old.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      const modal = new bootstrap.Modal(document.getElementById('metasNutricionModal'));
      modal.show();
    }

    async function guardarMetas() {
      try {
        const res = await fetch('../../public/api/actualizar-metas-nutricion.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            calorias_objetivo: parseInt(document.getElementById('meta-calorias').value),
            proteinas_objetivo: parseInt(document.getElementById('meta-proteinas').value),
            carbohidratos_objetivo: parseInt(document.getElementById('meta-carbos').value),
            grasas_objetivo: parseInt(document.getElementById('meta-grasas').value),
            agua_vasos_objetivo: parseInt(document.getElementById('meta-agua').value)
          })
        });

        const data = await res.json();
        
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('metasNutricionModal')).hide();
          await cargarResumenNutricional();
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }

    // ============ TRACKER DE ENTRENAMIENTOS (HEVY STYLE) ============
    let entrenamientoActivo = null;
    let timerInterval = null;
    let restTimerInterval = null;
    let tiempoInicio = null;

    async function iniciarEntrenamiento() {
      // Mostrar selector de rutina o entrenamiento libre
      const modalHTML = `
        <div class="modal fade" id="selectorRutinaModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
              <div class="modal-header border-secondary">
                <h5 class="modal-title">Selecciona una Rutina</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <button class="btn btn-outline-warning w-100 mb-2" onclick="empezarEntrenamientoLibre()">
                  🎯 Entrenamiento Libre
                </button>
                <hr class="border-secondary">
                <div id="lista-rutinas-selector">
                  <div class="text-center"><div class="spinner-border spinner-border-sm"></div></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const old = document.getElementById('selectorRutinaModal');
      if (old) old.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      const modal = new bootstrap.Modal(document.getElementById('selectorRutinaModal'));
      modal.show();
      
      // Cargar rutinas del usuario
      await cargarRutinasParaSelector();
    }

    async function cargarRutinasParaSelector() {
      try {
        const res = await fetch('../../public/api/obtener-rutinas-usuario.php');
        const data = await res.json();
        
        const container = document.getElementById('lista-rutinas-selector');
        
        if (data.success && data.rutinas && data.rutinas.length > 0) {
          container.innerHTML = data.rutinas.map(rutina => `
            <button class="btn btn-outline-primary w-100 mb-2 text-start" onclick="empezarConRutina(${rutina.id}, '${rutina.nombre.replace(/'/g, "\\'")}')">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">${rutina.nombre}</div>
                  <div class="small text-muted">${rutina.nivel || 'Intermedio'} • ${rutina.duracion_semanas || 0} semanas</div>
                </div>
                <span class="badge bg-success">${rutina.total_ejercicios || 0} ejercicios</span>
              </div>
            </button>
          `).join('');
        } else {
          container.innerHTML = '<p class="text-muted text-center">No tienes rutinas asignadas. Empieza con un entrenamiento libre.</p>';
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }

    async function empezarEntrenamientoLibre() {
      bootstrap.Modal.getInstance(document.getElementById('selectorRutinaModal')).hide();
      await empezarConRutina(null, 'Entrenamiento Libre');
    }

    async function empezarConRutina(rutinaId, nombreRutina) {
      try {
        const res = await fetch('../../public/api/iniciar-entrenamiento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ rutina_id: rutinaId })
        });
        
        const data = await res.json();
        
        if (data.success) {
          entrenamientoActivo = {
            id: data.entrenamiento_id,
            rutina_id: rutinaId,
            nombre: nombreRutina,
            ejercicios: data.ejercicios || [],
            series_completadas: {}
          };
          
          // Cerrar modal de selector
          const modal = bootstrap.Modal.getInstance(document.getElementById('selectorRutinaModal'));
          if (modal) modal.hide();
          
          // Abrir modal de entrenamiento
          document.getElementById('nombre-entrenamiento').textContent = nombreRutina;
          const entrenoModal = new bootstrap.Modal(document.getElementById('entrenamientoModal'));
          entrenoModal.show();
          
          // Iniciar timer
          tiempoInicio = new Date();
          iniciarTimer();
          
          // Renderizar ejercicios
          renderizarEjerciciosWorkout();
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al iniciar entrenamiento');
      }
    }

    function iniciarTimer() {
      if (timerInterval) clearInterval(timerInterval);
      
      timerInterval = setInterval(() => {
        const ahora = new Date();
        const diff = Math.floor((ahora - tiempoInicio) / 1000);
        const minutos = Math.floor(diff / 60);
        const segundos = diff % 60;
        document.getElementById('timer-entrenamiento').textContent = 
          `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;
      }, 1000);
    }

    function renderizarEjerciciosWorkout() {
      const container = document.getElementById('lista-ejercicios-workout');
      const ejercicios = entrenamientoActivo.ejercicios;
      
      if (!ejercicios || ejercicios.length === 0) {
        container.innerHTML = `
          <div class="alert alert-warning">
            <p class="mb-2">Entrenamiento libre - No hay ejercicios predefinidos</p>
            <button class="btn btn-sm btn-primary" onclick="agregarEjercicioLibre()">
              + Agregar Ejercicio
            </button>
          </div>
        `;
        return;
      }
      
      document.getElementById('ejercicios-totales').textContent = ejercicios.length;
      
      container.innerHTML = ejercicios.map((ej, idx) => {
        const completado = entrenamientoActivo.series_completadas[idx] >= (ej.series || 3);
        
        return `
          <div class="card bg-dark border-secondary mb-3 ejercicio-workout" data-idx="${idx}">
            <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle ${completado ? 'bg-success' : 'bg-secondary'}" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center">
                  ${completado ? '✓' : idx + 1}
                </div>
                <div>
                  <h6 class="mb-0">${ej.nombre}</h6>
                  <small class="text-muted">${ej.grupo_muscular || 'General'}</small>
                </div>
              </div>
              ${ej.gif_url ? `<img src="${ej.gif_url}" style="width:50px;height:50px;object-fit:cover;border-radius:4px" onerror="this.style.display='none'">` : ''}
            </div>
            <div class="card-body">
              <!-- Historial previo -->
              ${ej.historial && ej.historial.length > 0 ? `
                <div class="alert alert-info py-2 mb-3">
                  <small class="text-muted">Última vez: ${ej.historial[0].peso_kg}kg x ${ej.historial[0].repeticiones} reps</small>
                </div>
              ` : ''}
              
              <!-- Series -->
              <div id="series-container-${idx}">
                ${Array.from({length: ej.series || 3}, (_, i) => renderizarSerie(idx, i + 1, ej)).join('')}
              </div>
              
              <!-- Botón agregar serie -->
              <button class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="agregarSerie(${idx})">
                + Agregar Serie
              </button>
              
              ${ej.notas ? `<div class="small text-muted mt-2"><strong>Nota:</strong> ${ej.notas}</div>` : ''}
            </div>
          </div>
        `;
      }).join('');
      
      actualizarContadorEjercicios();
    }

    function renderizarSerie(ejIdx, serieNum, ejercicio) {
      const serieKey = `${ejIdx}-${serieNum}`;
      const completada = entrenamientoActivo.series_completadas[serieKey];
      
      return `
        <div class="d-flex gap-2 align-items-center mb-2 serie-row" data-key="${serieKey}">
          <div class="rounded-circle ${completada ? 'bg-success' : 'bg-secondary'}" style="width:25px;height:25px;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer" onclick="toggleSerie('${serieKey}', ${ejIdx}, ${serieNum})">
            ${completada ? '✓' : serieNum}
          </div>
          <input type="number" class="form-control form-control-sm" placeholder="kg" style="width:70px" id="peso-${serieKey}" ${completada ? 'disabled' : ''}>
          <span class="text-muted">×</span>
          <input type="number" class="form-control form-control-sm" placeholder="reps" style="width:70px" id="reps-${serieKey}" ${completada ? 'disabled' : ''}>
          <button class="btn btn-sm ${completada ? 'btn-success' : 'btn-outline-success'}" onclick="registrarSerieWorkout('${serieKey}', ${ejIdx}, ${serieNum})" ${completada ? 'disabled' : ''}>
            ${completada ? '✓' : 'OK'}
          </button>
        </div>
      `;
    }

    function toggleSerie(serieKey, ejIdx, serieNum) {
      if (entrenamientoActivo.series_completadas[serieKey]) {
        // Desmarcar
        delete entrenamientoActivo.series_completadas[serieKey];
        renderizarEjerciciosWorkout();
      } else {
        // Marcar como completada
        registrarSerieWorkout(serieKey, ejIdx, serieNum);
      }
    }

    async function registrarSerieWorkout(serieKey, ejIdx, serieNum) {
      const peso = parseFloat(document.getElementById(`peso-${serieKey}`)?.value) || 0;
      const reps = parseInt(document.getElementById(`reps-${serieKey}`)?.value) || 0;
      
      if (reps === 0) {
        alert('Ingresa las repeticiones');
        return;
      }
      
      try {
        const ejercicio = entrenamientoActivo.ejercicios[ejIdx];
        
        const res = await fetch('../../public/api/registrar-serie.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            entrenamiento_id: entrenamientoActivo.id,
            ejercicio_id: ejercicio.ejercicio_id,
            orden: ejIdx + 1,
            serie_numero: serieNum,
            peso_kg: peso,
            repeticiones: reps
          })
        });
        
        const data = await res.json();
        
        if (data.success) {
          entrenamientoActivo.series_completadas[serieKey] = true;
          
          if (data.es_pr) {
            mostrarNotificacionPR(ejercicio.nombre, peso);
          }
          
          // Iniciar descanso
          const descanso = ejercicio.descanso_segundos || 90;
          iniciarDescanso(descanso);
          
          renderizarEjerciciosWorkout();
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al registrar serie');
      }
    }

    function iniciarDescanso(segundos) {
      const restTimer = document.getElementById('rest-timer');
      const countdown = document.getElementById('rest-countdown');
      
      restTimer.style.display = 'block';
      countdown.textContent = segundos;
      
      let restante = segundos;
      
      if (restTimerInterval) clearInterval(restTimerInterval);
      
      restTimerInterval = setInterval(() => {
        restante--;
        countdown.textContent = restante;
        
        if (restante <= 0) {
          saltarDescanso();
          // Notificación de descanso completado
          if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Descanso completado', {
              body: '¡Listo para la siguiente serie!',
              icon: '../../public/images/logo.jpg'
            });
          }
        }
      }, 1000);
    }

    function saltarDescanso() {
      document.getElementById('rest-timer').style.display = 'none';
      if (restTimerInterval) clearInterval(restTimerInterval);
    }

    function agregarTiempo(segundos) {
      const countdown = document.getElementById('rest-countdown');
      const actual = parseInt(countdown.textContent) || 0;
      countdown.textContent = actual + segundos;
    }

    function agregarSerie(ejIdx) {
      const ejercicio = entrenamientoActivo.ejercicios[ejIdx];
      ejercicio.series = (ejercicio.series || 3) + 1;
      renderizarEjerciciosWorkout();
    }

    function actualizarContadorEjercicios() {
      const totalEjercicios = entrenamientoActivo.ejercicios.length;
      let completados = 0;
      
      entrenamientoActivo.ejercicios.forEach((ej, idx) => {
        const seriesCompletadas = Object.keys(entrenamientoActivo.series_completadas).filter(k => k.startsWith(`${idx}-`)).length;
        if (seriesCompletadas >= (ej.series || 3)) {
          completados++;
        }
      });
      
      document.getElementById('ejercicios-completados').textContent = completados;
    }

    function mostrarNotificacionPR(ejercicio, peso) {
      const alertHTML = `
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:99999;max-width:400px" role="alert">
          <h4 class="alert-heading">🎉 ¡NUEVO PR!</h4>
          <p class="mb-0"><strong>${ejercicio}</strong> - ${peso}kg</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      document.body.insertAdjacentHTML('beforeend', alertHTML);
      
      setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-success');
        alerts[alerts.length - 1]?.remove();
      }, 5000);
    }

    function cancelarEntrenamiento() {
      if (!confirm('¿Seguro que quieres cancelar el entrenamiento? Se perderá el progreso.')) return;
      
      if (timerInterval) clearInterval(timerInterval);
      if (restTimerInterval) clearInterval(restTimerInterval);
      
      entrenamientoActivo = null;
      bootstrap.Modal.getInstance(document.getElementById('entrenamientoModal')).hide();
    }

    async function finalizarEntrenamiento() {
      if (!confirm('¿Finalizar entrenamiento?')) return;
      
      try {
        const res = await fetch('../../public/api/finalizar-entrenamiento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            entrenamiento_id: entrenamientoActivo.id
          })
        });
        
        const data = await res.json();
        
        if (data.success) {
          if (timerInterval) clearInterval(timerInterval);
          if (restTimerInterval) clearInterval(restTimerInterval);
          
          bootstrap.Modal.getInstance(document.getElementById('entrenamientoModal')).hide();
          
          // Mostrar resumen
          mostrarResumenEntrenamiento(data);
          
          entrenamientoActivo = null;
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al finalizar entrenamiento');
      }
    }

    function mostrarResumenEntrenamiento(data) {
      const modalHTML = `
        <div class="modal fade" id="resumenEntrenoModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-success">
              <div class="modal-header border-success">
                <h5 class="modal-title">🎉 ¡Entrenamiento Completado!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body text-center">
                <div class="display-1 mb-3">💪</div>
                <h3 class="mb-4">¡Excelente trabajo!</h3>
                <div class="row g-3 mb-4">
                  <div class="col-6">
                    <div class="p-3 rounded" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3)">
                      <h4 class="text-success mb-1">${data.duracion_minutos || 0}</h4>
                      <small class="text-muted">Minutos</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="p-3 rounded" style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3)">
                      <h4 class="text-primary mb-1">${Math.round(data.volumen_total || 0)}</h4>
                      <small class="text-muted">kg Volumen</small>
                    </div>
                  </div>
                </div>
                <p class="text-muted small">¡Sigue así y alcanzarás tus objetivos!</p>
              </div>
              <div class="modal-footer border-success">
                <button class="btn btn-success w-100" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const old = document.getElementById('resumenEntrenoModal');
      if (old) old.remove();
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      
      const modal = new bootstrap.Modal(document.getElementById('resumenEntrenoModal'));
      modal.show();
    }

    // Solicitar permisos de notificaciones
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission();
    }
  </script>
</body>
</html>