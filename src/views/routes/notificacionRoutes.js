const express = require('express');
const router = express.Router();
const { sequelize } = require('../../config/db');

// GET /api/notificaciones/usuario/:id - Obtener notificaciones
router.get('/usuario/:id', async (req, res) => {
  try {
    const { no_leidas, limite } = req.query;
    let query = `SELECT * FROM notificaciones WHERE usuario_id = ?`;
    
    if (no_leidas === '1') {
      query += ` AND leida = 0`;
    }
    
    query += ` ORDER BY fecha_creacion DESC`;
    
    if (limite) {
      query += ` LIMIT ${parseInt(limite)}`;
    }
    
    const [notificaciones] = await sequelize.query(query, { replacements: [req.params.id] });
    
    res.json({ success: true, data: notificaciones });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// POST /api/notificaciones - Crear notificación
router.post('/', async (req, res) => {
  try {
    const { usuario_id, tipo, titulo, mensaje, icono, importante } = req.body;
    
    await sequelize.query(`
      INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, icono, importante, leida, fecha_creacion)
      VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
    `, { replacements: [usuario_id, tipo, titulo, mensaje, icono || '📬', importante || 0] });
    
    res.json({ success: true, message: 'Notificación creada exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// PUT /api/notificaciones/:id/leer - Marcar como leída
router.put('/:id/leer', async (req, res) => {
  try {
    await sequelize.query(`
      UPDATE notificaciones 
      SET leida = 1, fecha_leida = NOW()
      WHERE id = ?
    `, { replacements: [req.params.id] });
    
    res.json({ success: true, message: 'Notificación marcada como leída' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// PUT /api/notificaciones/marcar-todas-leidas/:usuarioId - Marcar todas como leídas
router.put('/marcar-todas-leidas/:usuarioId', async (req, res) => {
  try {
    await sequelize.query(`
      UPDATE notificaciones 
      SET leida = 1, fecha_leida = NOW()
      WHERE usuario_id = ? AND leida = 0
    `, { replacements: [req.params.usuarioId] });
    
    res.json({ success: true, message: 'Todas las notificaciones marcadas como leídas' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
