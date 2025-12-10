<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../../public/index.html');
    exit;
}
// require admin role
if (!isset($_SESSION['tipo_usuario']) || strtolower($_SESSION['tipo_usuario']) !== 'admin') {
    // redirect non-admin users to user dashboard
    header('Location: ../user/dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel Administrador</title>
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
    .admin-stat{background:linear-gradient(135deg, rgba(240,112,8,0.15), rgba(255,255,255,0.02));padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.05);text-align:center}
    .admin-stat h3{margin:0;font-size:26px;color:#fff;font-weight:700}
    .admin-stat p{margin:6px 0 0;font-size:11px;color:#fff}
    .chart-container{background:rgba(255,255,255,0.02);padding:16px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);max-height:280px}
    .chart-container h3{margin:0 0 12px;font-size:16px;color:#fff}
    .chart-container canvas{max-height:230px !important}
    .table-dark{--bs-table-bg:#1a1a1a;--bs-table-border-color:rgba(255,255,255,0.1)}
    .table-dark th{background:rgba(255,255,255,0.05);font-size:12px;padding:8px}
    .table-dark td{font-size:12px;padding:8px}
    .table-dark tbody tr:hover{background:rgba(255,255,255,0.02)}
    .btn-sm{padding:4px 10px;font-size:12px}
    .table-responsive{max-height:500px;overflow-y:auto}
    .sidebar{background:linear-gradient(180deg, rgba(30,30,30,1), rgba(20,20,20,1));border-radius:12px;padding:18px;border:1px solid rgba(255,255,255,0.05)}
    .nav-pills .nav-link{color:rgba(255,255,255,0.8);margin-bottom:6px;padding:8px 12px;font-size:13px}
    .nav-pills .nav-link.active{background:#f07008}
    .modal-content{background:linear-gradient(180deg, rgba(30,30,30,1), rgba(20,20,20,1));border:1px solid rgba(255,255,255,0.1)}
    .form-control,.form-select{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#fff}
    .form-control:focus,.form-select:focus{background:rgba(255,255,255,0.08);border-color:#f07008;color:#fff}
    .form-label{color:rgba(255,255,255,0.9)}
  </style>
</head>
<body>
  <div class="container-fluid py-4" style="max-width:1800px">
    <div class="row g-3">
      <div class="col-lg-2 col-md-3">
        <div class="sidebar sticky-top" style="top:20px">
          <div class="text-center mb-4">
            <img src="../../public/images/logo.jpg" alt="logo" class="img-fluid mb-3" style="max-width:120px">
            <h5 class="mb-1">Admin: <?php echo htmlspecialchars($_SESSION['nombre']); ?></h5>
            <p class="small text-muted mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
          </div>
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link active" data-section="dashboard" href="#">📊 Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="usuarios" href="#">👥 Usuarios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="productos" href="#">📦 Productos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-section="categorias" href="#">🏷️ Categorías</a>
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
          <div class="row g-3 mb-3">
            <div class="col-xl col-lg-4 col-md-6">
              <div class="admin-stat">
                <h3 id="stat-usuarios">—</h3>
                <p>Usuarios</p>
              </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
              <div class="admin-stat">
                <h3 id="stat-productos">—</h3>
                <p>Productos</p>
              </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
              <div class="admin-stat">
                <h3 id="stat-categorias">—</h3>
                <p>Categorías</p>
              </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
              <div class="admin-stat">
                <h3 id="stat-clientes">—</h3>
                <p>Clientes</p>
              </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
              <div class="admin-stat">
                <h3 id="stat-pedidos">—</h3>
                <p>Pedidos</p>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-lg-6">
              <div class="chart-container">
                <h3>Usuarios por Tipo</h3>
                <canvas id="chartUsuarios"></canvas>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="chart-container">
                <h3>Productos por Categoría</h3>
                <canvas id="chartProductos"></canvas>
              </div>
            </div>
          </div>

          <div class="chart-container">
            <h3>Pedidos Últimos 7 Días</h3>
            <canvas id="chartPedidos"></canvas>
          </div>
        </section>

        <!-- Usuarios CRUD -->
        <section id="section-usuarios" style="display:none">
          <div class="card bg-dark text-white border-secondary" style="max-width:100%">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title mb-0">Gestión de Usuarios</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('usuario')">+ Nuevo Usuario</button>
              </div>
              <div class="table-responsive">
                <table class="table table-dark table-hover" id="table-usuarios" style="min-width:100%">
                  <thead>
                    <tr>
                      <th style="width:50px">ID</th>
                      <th style="width:200px">Nombre</th>
                      <th style="width:220px">Email</th>
                      <th style="width:120px">Tipo</th>
                      <th style="width:140px">Teléfono</th>
                      <th style="width:180px">Acciones</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- Productos CRUD -->
        <section id="section-productos" style="display:none">
          <div class="card bg-dark text-white border-secondary" style="max-width:100%">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title mb-0">Gestión de Productos</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('producto')">+ Nuevo Producto</button>
              </div>
              <div class="table-responsive">
                <table class="table table-dark table-hover" id="table-productos" style="min-width:100%">
                  <thead>
                    <tr>
                      <th style="width:50px">ID</th>
                      <th style="width:250px">Nombre</th>
                      <th style="width:150px">Categoría</th>
                      <th style="width:100px">Precio</th>
                      <th style="width:80px">Stock</th>
                      <th style="width:180px">Acciones</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- Categorías CRUD -->
        <section id="section-categorias" style="display:none">
          <div class="card bg-dark text-white border-secondary" style="max-width:100%">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title mb-0">Gestión de Categorías</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('categoria')">+ Nueva Categoría</button>
              </div>
              <div class="table-responsive">
                <table class="table table-dark table-hover" id="table-categorias" style="min-width:100%">
                  <thead>
                    <tr>
                      <th style="width:50px">ID</th>
                      <th style="width:200px">Nombre</th>
                      <th>Descripción</th>
                      <th style="width:180px">Acciones</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
        </main>
      </div>
    </div>
  </div>

  <!-- Modal Universal -->
  <div class="modal fade" id="modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title">Formulario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="modal-form">
          <div class="modal-body" id="modal-fields"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let charts = {};
    let currentEntity = null;
    let currentData = null;
    let bsModal = null;

    // Navegación
    document.querySelectorAll('.nav-link[data-section]').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        const section = link.dataset.section;
        
        // Ocultar todas las secciones
        document.querySelectorAll('section[id^="section-"]').forEach(s => s.style.display = 'none');
        
        const targetSection = document.getElementById('section-' + section);
        targetSection.style.display = 'block';
        
        // Animar entrada de sección
        anime({
          targets: targetSection,
          translateX: [50, 0],
          opacity: [0, 1],
          duration: 600,
          easing: 'easeOutExpo'
        });
        
        if (section === 'usuarios') loadUsuarios();
        else if (section === 'productos') loadProductos();
        else if (section === 'categorias') loadCategorias();
      });
    });

    // Logout
    document.getElementById('logoutLink').addEventListener('click', async function(e){
      e.preventDefault();
      const res = await fetch('../../public/logout.php',{method:'POST'});
      const j = await res.json();
      if(j.success) window.location='../../public/index.html';
    });

    // Cargar estadísticas
    async function loadStats() {
      try {
        const res = await fetch('../../public/api/admin-stats.php');
        const data = await res.json();
        if (data.success) {
          document.getElementById('stat-usuarios').textContent = data.totales.usuarios;
          document.getElementById('stat-productos').textContent = data.totales.productos;
          document.getElementById('stat-categorias').textContent = data.totales.categorias;
          document.getElementById('stat-clientes').textContent = data.totales.clientes;
          document.getElementById('stat-pedidos').textContent = data.totales.pedidos;
          
          renderCharts(data);
        }
      } catch (err) {
        console.error('Error cargando stats:', err);
      }
    }

    function renderCharts(data) {
      // Usuarios por tipo
      const ctxUsuarios = document.getElementById('chartUsuarios');
      if (charts.usuarios) charts.usuarios.destroy();
      charts.usuarios = new Chart(ctxUsuarios, {
        type: 'doughnut',
        data: {
          labels: data.usuariosPorTipo.map(u => u.tipo_usuario),
          datasets: [{
            data: data.usuariosPorTipo.map(u => u.total),
            backgroundColor: ['#f07008', '#8bc34a', '#3b82f6', '#ef4444']
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { labels: { color: '#fff', font: { size: 11 } } }
          }
        }
      });

      // Productos por categoría
      const ctxProductos = document.getElementById('chartProductos');
      if (charts.productos) charts.productos.destroy();
      charts.productos = new Chart(ctxProductos, {
        type: 'bar',
        data: {
          labels: data.productosPorCategoria.map(p => p.nombre),
          datasets: [{
            label: 'Productos',
            data: data.productosPorCategoria.map(p => p.total),
            backgroundColor: '#f07008'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { labels: { color: '#fff', font: { size: 11 } } }
          },
          scales: {
            y: { ticks: { color: '#fff', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.1)' } },
            x: { ticks: { color: '#fff', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.1)' } }
          }
        }
      });

      // Pedidos recientes
      const ctxPedidos = document.getElementById('chartPedidos');
      if (charts.pedidos) charts.pedidos.destroy();
      charts.pedidos = new Chart(ctxPedidos, {
        type: 'line',
        data: {
          labels: data.pedidosRecientes.map(p => p.fecha),
          datasets: [{
            label: 'Pedidos',
            data: data.pedidosRecientes.map(p => p.total),
            borderColor: '#f07008',
            backgroundColor: 'rgba(240,112,8,0.1)',
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { labels: { color: '#fff', font: { size: 11 } } }
          },
          scales: {
            y: { ticks: { color: '#fff', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.1)' } },
            x: { ticks: { color: '#fff', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.1)' } }
          }
        }
      });
    }

    // CRUD Usuarios
    async function loadUsuarios() {
      try {
        const res = await fetch('../../public/api/usuarios.php');
        const data = await res.json();
        console.log('Usuarios:', data); // Debug
        
        if (data.success && data.data) {
          const tbody = document.querySelector('#table-usuarios tbody');
          if (data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay usuarios registrados</td></tr>';
            return;
          }
          
          tbody.innerHTML = data.data.map(u => `
            <tr>
              <td>${u.id}</td>
              <td>${u.nombre} ${u.apellido || ''}</td>
              <td>${u.email}</td>
              <td><span class="badge bg-secondary">${u.tipo_usuario}</span></td>
              <td>${u.telefono || '—'}</td>
              <td>
                <button class="btn btn-secondary btn-sm me-1" onclick='editUsuario(${JSON.stringify(u).replace(/'/g, "&apos;")})'>Editar</button>
                <button class="btn btn-danger btn-sm" onclick="deleteUsuario(${u.id})">Eliminar</button>
              </td>
            </tr>
          `).join('');
          
          // Animar filas
          anime({
            targets: '#table-usuarios tbody tr',
            translateX: [-20, 0],
            opacity: [0, 1],
            duration: 400,
            delay: anime.stagger(50),
            easing: 'easeOutQuad'
          });
        } else {
          console.error('Error cargando usuarios:', data);
          const tbody = document.querySelector('#table-usuarios tbody');
          tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar usuarios</td></tr>';
        }
      } catch (err) {
        console.error('Error:', err);
        const tbody = document.querySelector('#table-usuarios tbody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error de red</td></tr>';
      }
    }

    function editUsuario(usuario) {
      currentData = usuario;
      openModal('usuario', usuario);
    }

    async function deleteUsuario(id) {
      if (!confirm('¿Eliminar usuario?')) return;
      await fetch('../../public/api/usuarios.php', {
        method: 'DELETE',
        body: 'id=' + id
      });
      loadUsuarios();
    }

    // CRUD Productos
    async function loadProductos() {
      try {
        const res = await fetch('../../public/api/productos.php');
        const data = await res.json();
        console.log('Productos:', data); // Debug
        
        if (data.success && data.data) {
          const tbody = document.querySelector('#table-productos tbody');
          if (data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay productos registrados</td></tr>';
            return;
          }
          
          tbody.innerHTML = data.data.map(p => `
            <tr>
              <td>${p.id}</td>
              <td>${p.nombre}</td>
              <td>${p.categoria_nombre || '<span class="text-muted">Sin categoría</span>'}</td>
              <td>$${parseFloat(p.precio).toFixed(2)}</td>
              <td>${p.stock}</td>
              <td class="text-nowrap">
                <button class="btn btn-secondary btn-sm me-1" onclick='editProducto(${JSON.stringify(p).replace(/'/g, "&apos;")})'>Editar</button>
                <button class="btn btn-danger btn-sm" onclick="deleteProducto(${p.id})">Eliminar</button>
              </td>
            </tr>
          `).join('');
          
          // Animar filas
          anime({
            targets: '#table-productos tbody tr',
            translateX: [-20, 0],
            opacity: [0, 1],
            duration: 400,
            delay: anime.stagger(50),
            easing: 'easeOutQuad'
          });
        } else {
          console.error('Error cargando productos:', data);
          const tbody = document.querySelector('#table-productos tbody');
          tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar productos</td></tr>';
        }
      } catch (err) {
        console.error('Error:', err);
        const tbody = document.querySelector('#table-productos tbody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error de red</td></tr>';
      }
    }

    function editProducto(producto) {
      currentData = producto;
      openModal('producto', producto);
    }

    async function deleteProducto(id) {
      if (!confirm('¿Eliminar producto?')) return;
      await fetch('../../public/api/productos.php', {
        method: 'DELETE',
        body: 'id=' + id
      });
      loadProductos();
    }

    // CRUD Categorías
    async function loadCategorias() {
      const res = await fetch('../../public/api/categorias.php');
      const data = await res.json();
      if (data.success) {
        const tbody = document.querySelector('#table-categorias tbody');
        tbody.innerHTML = data.data.map(c => `
          <tr>
            <td>${c.id}</td>
            <td>${c.nombre}</td>
            <td>${c.descripcion || '—'}</td>
            <td>
              <button class="btn btn-secondary btn-sm me-1" onclick='editCategoria(${JSON.stringify(c)})'>Editar</button>
              <button class="btn btn-danger btn-sm" onclick="deleteCategoria(${c.id})">Eliminar</button>
            </td>
          </tr>
        `).join('');
        
        // Animar filas
        anime({
          targets: '#table-categorias tbody tr',
          translateX: [-20, 0],
          opacity: [0, 1],
          duration: 400,
          delay: anime.stagger(50),
          easing: 'easeOutQuad'
        });
      }
    }

    function editCategoria(categoria) {
      currentData = categoria;
      openModal('categoria', categoria);
    }

    async function deleteCategoria(id) {
      if (!confirm('¿Eliminar categoría?')) return;
      await fetch('../../public/api/categorias.php', {
        method: 'DELETE',
        body: 'id=' + id
      });
      loadCategorias();
    }

    // Modal
    function openModal(entity, data = null) {
      currentEntity = entity;
      currentData = data;
      const title = document.getElementById('modal-title');
      const fields = document.getElementById('modal-fields');

      if (entity === 'usuario') {
        title.textContent = data ? 'Editar Usuario' : 'Nuevo Usuario';
        fields.innerHTML = `
          ${data ? `<input type="hidden" name="id" value="${data.id}">` : ''}
          <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="${data?.nombre || ''}" required></div>
          <div class="mb-3"><label class="form-label">Apellido</label><input class="form-control" name="apellido" value="${data?.apellido || ''}" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" type="email" value="${data?.email || ''}" required></div>
          <div class="mb-3"><label class="form-label">Tipo</label>
            <select class="form-select" name="tipo_usuario">
              <option value="usuario" ${data?.tipo_usuario === 'usuario' ? 'selected' : ''}>Usuario</option>
              <option value="instructor" ${data?.tipo_usuario === 'instructor' ? 'selected' : ''}>Instructor</option>
              <option value="nutriologo" ${data?.tipo_usuario === 'nutriologo' ? 'selected' : ''}>Nutriólogo</option>
              <option value="admin" ${data?.tipo_usuario === 'admin' ? 'selected' : ''}>Admin</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Teléfono</label><input class="form-control" name="telefono" value="${data?.telefono || ''}"></div>
          <div class="mb-3"><label class="form-label">Género</label>
            <select class="form-select" name="genero">
              <option value="">—</option>
              <option value="masculino" ${data?.genero === 'masculino' ? 'selected' : ''}>Masculino</option>
              <option value="femenino" ${data?.genero === 'femenino' ? 'selected' : ''}>Femenino</option>
              <option value="otro" ${data?.genero === 'otro' ? 'selected' : ''}>Otro</option>
            </select>
          </div>
        `;
      } else if (entity === 'producto') {
        title.textContent = data ? 'Editar Producto' : 'Nuevo Producto';
        fields.innerHTML = `
          ${data ? `<input type="hidden" name="id" value="${data.id}">` : ''}
          <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="${data?.nombre || ''}" required></div>
          <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="3">${data?.descripcion || ''}</textarea></div>
          <div class="mb-3"><label class="form-label">Precio</label><input class="form-control" name="precio" type="number" step="0.01" value="${data?.precio || ''}" required></div>
          <div class="mb-3"><label class="form-label">Stock</label><input class="form-control" name="stock" type="number" value="${data?.stock || 0}"></div>
          <div class="mb-3"><label class="form-label">Categoría ID</label><input class="form-control" name="categoria_id" type="number" value="${data?.categoria_id || ''}"></div>
        `;
      } else if (entity === 'categoria') {
        title.textContent = data ? 'Editar Categoría' : 'Nueva Categoría';
        fields.innerHTML = `
          ${data ? `<input type="hidden" name="id" value="${data.id}">` : ''}
          <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="${data?.nombre || ''}" required></div>
          <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="3">${data?.descripcion || ''}</textarea></div>
        `;
      }

      bsModal = new bootstrap.Modal(document.getElementById('modal'));
      bsModal.show();
      
      // Animar modal
      setTimeout(() => {
        anime({
          targets: '.modal-content',
          scale: [0.8, 1],
          opacity: [0, 1],
          duration: 400,
          easing: 'easeOutElastic(1, .6)'
        });
      }, 100);
    }

    document.getElementById('modal-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const payload = Object.fromEntries(formData);

      let endpoint = '';
      if (currentEntity === 'usuario') endpoint = '../../public/api/usuarios.php';
      else if (currentEntity === 'producto') endpoint = '../../public/api/productos.php';
      else if (currentEntity === 'categoria') endpoint = '../../public/api/categorias.php';

      await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      bsModal.hide();
      document.getElementById('modal-form').reset();
      
      const entity = currentEntity; // Guardar antes de limpiar
      currentEntity = null;
      currentData = null;
      
      if (entity === 'usuario') loadUsuarios();
      else if (entity === 'producto') loadProductos();
      else if (entity === 'categoria') loadCategorias();
      
      loadStats();
    });

    // Inicializar
    loadStats();

    // Animaciones de entrada
    anime({
      targets: '.sidebar',
      translateX: [-50, 0],
      opacity: [0, 1],
      duration: 800,
      easing: 'easeOutExpo'
    });

    anime({
      targets: '.admin-stat',
      scale: [0.8, 1],
      opacity: [0, 1],
      duration: 600,
      delay: anime.stagger(100, {start: 300}),
      easing: 'easeOutElastic(1, .6)'
    });

    anime({
      targets: '.chart-container',
      translateY: [30, 0],
      opacity: [0, 1],
      duration: 800,
      delay: anime.stagger(150, {start: 500}),
      easing: 'easeOutExpo'
    });
  </script>
</body>
</html>
