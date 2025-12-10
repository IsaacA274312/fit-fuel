require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const path = require('path');

const { sequelize } = require('./src/config/db');

const authRoutes = require('./src/views/routes/authRoutes');
const categoriaRoutes = require('./src/views/routes/categoriaRoutes');
const productoRoutes = require('./src/views/routes/productoRoutes');
const clienteRoutes = require('./src/views/routes/clienteRoutes');
const usuarioRoutes = require('./src/views/routes/usuarioRoutes');
const pedidoRoutes = require('./src/views/routes/pedidoRoutes');
const cuponRoutes = require('./src/views/routes/cuponRoutes');
const progresoRoutes = require('./src/views/routes/progresoRoutes');
const objetivoRoutes = require('./src/views/routes/objetivoRoutes');
const notificacionRoutes = require('./src/views/routes/notificacionRoutes');

const { authMiddleware } = require('./src/middleware/authMiddleware');

const app = express();

app.use(helmet());
app.use(cors());
app.use(morgan('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Static admin panel
app.use('/admin', express.static(path.join(__dirname, 'src', 'views', 'admin')));

// Public auth routes
app.use('/api/auth', authRoutes);

// Protect remaining API routes
// TEMPORAL: Comentado para desarrollo - descomentar en producción
// app.use('/api', authMiddleware); // requiere token para /api/*

// API routes
app.use('/api/categorias', categoriaRoutes);
app.use('/api/productos', productoRoutes);
app.use('/api/clientes', clienteRoutes);
app.use('/api/usuarios', usuarioRoutes);
app.use('/api/pedidos', pedidoRoutes);
app.use('/api/cupones', cuponRoutes);
app.use('/api/progreso', progresoRoutes);
app.use('/api/objetivos', objetivoRoutes);
app.use('/api/notificaciones', notificacionRoutes);

// Error handler
app.use((err, req, res, next) => {
  console.error(err);
  res.status(err.status || 500).json({ success: false, message: err.message || 'Server error' });
});

// Start
const PORT = process.env.PORT || 3000;
(async () => {
  try {
    await sequelize.authenticate();
    await sequelize.sync(); // en dev sincronizamos modelos
    console.log('DB connected and synced');
    app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
  } catch (e) {
    console.error('Unable to start server', e);
    process.exit(1);
  }
})();

// Export app for tests
module.exports = app;