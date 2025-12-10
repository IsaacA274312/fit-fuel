# FitAndFuel — Plataforma de Fitness y Nutrición

Sistema completo de gestión para gimnasio con backend Node.js/Express y frontend PHP.

## ✨ Características Principales

- 🛒 **Sistema de Tienda y Carrito** - Compra de productos y suplementos
- 🎫 **Sistema de Cupones** - Descuentos porcentuales y de monto fijo
- 📊 **Seguimiento de Progreso** - Registra peso, grasa corporal, masa muscular
- 🎯 **Objetivos Personalizados** - Define y alcanza tus metas fitness
- 🔔 **Notificaciones en Tiempo Real** - Alertas y actualizaciones automáticas
- 👥 **Sistema Multi-Rol** - Admin, Usuario, Instructor, Nutriólogo
- 📱 **Interfaz Responsiva** - Diseño moderno con Bootstrap 5

## 📋 Requisitos Previos

- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **Node.js** 14+ y **npm**
- **MySQL** 5.7+ o MariaDB 10+

## 🚀 Instalación

### 1. Configurar la Base de Datos

1. Inicia XAMPP y arranca los servicios **Apache** y **MySQL**
2. Abre phpMyAdmin en `http://localhost/phpmyadmin`
3. Importa el archivo `db/INSTALACION-COMPLETA.sql` que incluye:
   - ✅ Esquema completo de la base de datos
   - ✅ Tablas de cupones, progreso, objetivos y notificaciones
   - ✅ 5 cupones de prueba activos
   - ✅ 5 notificaciones de ejemplo

   **Usando línea de comandos:**
   ```powershell
   Get-Content db\INSTALACION-COMPLETA.sql | C:\xampp\mysql\bin\mysql.exe -u root -h localhost fitandfuel
   ```
   
   **O importa manualmente desde phpMyAdmin**

### 2. Configurar Variables de Entorno

1. Ya existe un archivo `.env` en la raíz del proyecto
2. Edita las credenciales de MySQL si es necesario:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=fitandfuel
   DB_USER=root
   DB_PASS=tu_contraseña_mysql
   PORT=3000
   JWT_SECRET=tu_secreto_super_seguro_aqui
   ```

### 3. Instalar Dependencias de Node.js

```bash
npm install
```

### 4. Iniciar el Servidor

**Modo desarrollo** (con auto-recarga):
```bash
npm run dev
```

**Modo producción**:
```bash
npm start
```

El servidor Node.js estará en: `http://localhost:3000`

### 5. Acceder a la Aplicación

La aplicación PHP (punto de entrada de autenticación) está en:
- Ruta: `c:\xampp\htdocs\fitandfuel\src\public\index.html`
- URL: `http://localhost/fitandfuel/src/public/index.html`

## 📁 Estructura del Proyecto

```
fitandfuel/
├── db/                      # Scripts SQL
│   ├── fit-fuel.sql        # Esquema base de la BD
│   └── INSTALACION-COMPLETA.sql  # Instalación completa con todos los sistemas
├── src/
│   ├── config/             # Configuraciones
│   │   ├── db.js          # Conexión Sequelize (Node.js)
│   │   └── db.php         # Conexión PDO (PHP)
│   ├── controllers/        # Controladores Node.js
│   ├── models/            # Modelos Sequelize
│   ├── routes/            # Rutas API Express
│   ├── services/          # Lógica de negocio
│   ├── middleware/        # Middlewares (auth, etc.)
│   └── views/             # Vistas PHP
│       ├── public/        # Login/Registro
│       ├── admin/         # Panel administrador
│       ├── instructor/    # Panel instructor
│       ├── nutriologo/    # Panel nutriólogo
│       └── user/          # Panel usuario (con tienda, cupones, progreso, notificaciones)
├── app.js                 # Servidor Express alternativo
├── index.js               # Servidor principal
├── package.json           # Dependencias npm
└── .env                   # Variables de entorno
```

## 🔑 API Endpoints

### Autenticación (Público)
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario

### API Protegida (requiere token JWT)

**Categorías y Productos**
- `GET/POST/PUT/DELETE /api/categorias` - Gestión de categorías
- `GET/POST/PUT/DELETE /api/productos` - Gestión de productos

**Clientes y Usuarios**
- `GET/POST/PUT/DELETE /api/clientes` - Gestión de clientes
- `GET/POST/PUT/DELETE /api/usuarios` - Gestión de usuarios

**Pedidos**
- `GET/POST/PUT/DELETE /api/pedidos` - Gestión de pedidos
- `GET /api/pedidos/usuario/:id` - Historial de pedidos por usuario

**Cupones**
- `GET /api/cupones` - Listar cupones activos
- `POST /api/cupones/validar` - Validar un cupón
- `POST /api/cupones` - Crear cupón (admin)
- `PUT /api/cupones/:id` - Actualizar cupón
- `DELETE /api/cupones/:id` - Eliminar cupón

**Progreso y Objetivos**
- `GET /api/progreso/usuario/:id` - Obtener progreso del usuario
- `POST /api/progreso` - Registrar nuevo progreso
- `GET /api/objetivos/usuario/:id` - Obtener objetivos del usuario
- `POST /api/objetivos` - Crear nuevo objetivo
- `PUT /api/objetivos/:id` - Actualizar objetivo

**Notificaciones**
- `GET /api/notificaciones/usuario/:id` - Obtener notificaciones
- `POST /api/notificaciones` - Crear notificación
- `PUT /api/notificaciones/:id/leer` - Marcar como leída
- `PUT /api/notificaciones/marcar-todas-leidas/:usuarioId` - Marcar todas leídas
- `GET /api/notificaciones/preferencias/:usuarioId` - Obtener preferencias
- `PUT /api/notificaciones/preferencias/:usuarioId` - Actualizar preferencias

**Instructores y Nutriólogos**
- `GET /api/usuarios/instructores` - Listar instructores
- `GET /api/usuarios/nutriologos` - Listar nutriólogos
- `POST /api/usuarios/:userId/asignar-instructor` - Asignar instructor
- `POST /api/usuarios/:userId/asignar-nutriologo` - Asignar nutriólogo

## 👥 Tipos de Usuario

El sistema soporta 4 roles con funcionalidades específicas:

### 🔴 Admin
- Gestión completa de usuarios, productos y categorías
- Creación y administración de cupones
- Panel de administración con estadísticas
- Control total del sistema

### 🟢 Usuario
- Dashboard personalizado con:
  - 🛒 Tienda de productos y carrito de compras
  - 🎫 Aplicación de cupones de descuento
  - 📊 Registro de progreso (peso, grasa, músculo)
  - 🎯 Creación y seguimiento de objetivos
  - 🔔 Notificaciones en tiempo real
  - 📜 Historial de pedidos
  - 👨‍🏫 Asignación de instructor y nutriólogo

### 🟡 Instructor
- Panel para gestión de rutinas
- Seguimiento de clientes asignados
- Herramientas de entrenamiento

### 🟣 Nutriólogo
- Panel para planes nutricionales
- Seguimiento de clientes asignados
- Herramientas de nutrición

## 🧪 Tests

Ejecutar tests:
```bash
npm test
```

## 🔧 Solución de Problemas

### Error de conexión a MySQL
- Verifica que MySQL esté corriendo en XAMPP
- Confirma las credenciales en `.env` y `src/config/db.php`
- Asegúrate de que la base de datos `fitandfuel` existe

### Error "Cannot find module"
```bash
rm -rf node_modules package-lock.json
npm install
```

### Rutas PHP no funcionan
- Verifica que Apache esté corriendo
- Confirma que estás en: `http://localhost/fitandfuel/...`
- Revisa los logs de Apache en `c:\xampp\apache\logs\error.log`

### Sesiones PHP no funcionan
- Asegúrate de que `session.save_path` esté configurado en `php.ini`
- Verifica permisos de escritura en la carpeta de sesiones

## 🧪 Usuarios de Prueba

Después de importar la base de datos con `INSTALACION-COMPLETA.sql`, tendrás disponibles:

**Credenciales de prueba:**
- **Admin:** admin@fitandfuel.com / Admin123!
- **Usuario:** usuario@fitandfuel.com / User1234!
- **Instructor:** instructor@fitandfuel.com / Instructor1!
- **Nutriólogo:** nutriologo@fitandfuel.com / Nutri1234!

**Datos de prueba incluidos:**
- ✅ 5 cupones activos (BIENVENIDO10, VERANO20, PRIMERACOMPRA, etc.)
- ✅ 5 notificaciones de ejemplo para el usuario ID 2
- ✅ Productos y categorías de ejemplo

**Cupones disponibles para probar:**
- `BIENVENIDO10` - 10% de descuento, compra mínima $100
- `VERANO20` - 20% de descuento, compra mínima $500
- `PRIMERACOMPRA` - $50 de descuento fijo
- `ENVIOGRATIS` - 100% descuento en envío
- `VIP30` - 30% de descuento, compra mínima $1000

## 📝 Notas de Desarrollo

### Arquitectura
- El archivo `app.js` usa `express-myconnection` (desarrollo/pruebas)
- El archivo `index.js` usa Sequelize ORM (producción recomendada)
- Las vistas PHP usan sesiones nativas de PHP
- La API Node.js usa JWT para autenticación
- Las contraseñas se hashean con bcrypt

### Sistemas Implementados

**Sistema de Cupones:**
- Cupones de descuento porcentual y monto fijo
- Validación de monto mínimo de compra
- Límite de usos por cupón
- Estado activo/inactivo
- Fechas de vigencia

**Sistema de Progreso:**
- Registro de métricas: peso, grasa corporal, masa muscular
- Visualización con gráficas (Chart.js)
- Historial completo de mediciones
- Comparación de progreso en el tiempo

**Sistema de Objetivos:**
- Objetivos personalizados (peso, grasa, músculo, medidas)
- Tracking de progreso hacia la meta
- Fechas límite y porcentaje de avance
- Estados: en progreso / completado

**Sistema de Notificaciones:**
- Notificaciones en tiempo real
- Auto-refresh cada 30 segundos
- Contador de notificaciones no leídas
- Marcado individual o masivo como leído
- Preferencias de notificación por usuario
- Iconos y categorías (info, éxito, advertencia, error)

### Tecnologías
- **Backend:** Node.js, Express.js, Sequelize ORM
- **Frontend:** PHP, Bootstrap 5, JavaScript ES6+
- **Base de Datos:** MySQL/MariaDB
- **Gráficas:** Chart.js
- **Autenticación:** JWT (API) + Sesiones PHP (Vistas)
- **Seguridad:** Bcrypt, Prepared Statements, CORS

## 📄 Licencia

IITG - Gabriel Isaac Alvarado Puch

---

Para más información o soporte, contacta al desarrollador.

