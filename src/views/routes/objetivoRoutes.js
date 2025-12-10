const express = require('express');
const router = express.Router();
const { sequelize } = require('../../config/db');

// GET /api/objetivos/usuario/:id - Obtener objetivos del usuario
router.get('/usuario/:id', async (req, res) => {
  try {
    const [objetivos] = await sequelize.query(`
      SELECT * FROM objetivos_usuario 
      WHERE usuario_id = ? 
      ORDER BY created_at DESC
    `, { replacements: [req.params.id] });
    
    res.json({ success: true, data: objetivos });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// POST /api/objetivos - Crear nuevo objetivo
router.post('/', async (req, res) => {
  try {
    const { usuario_id, tipo_objetivo, descripcion, valor_objetivo, valor_actual, unidad, fecha_objetivo } = req.body;
    
    await sequelize.query(`
      INSERT INTO objetivos_usuario (usuario_id, tipo_objetivo, descripcion, valor_objetivo, valor_actual, unidad, fecha_objetivo, completado)
      VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    `, { replacements: [usuario_id, tipo_objetivo, descripcion, valor_objetivo, valor_actual, unidad, fecha_objetivo] });
    
    res.json({ success: true, message: 'Objetivo creado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// PUT /api/objetivos/:id - Actualizar objetivo
router.put('/:id', async (req, res) => {
  try {
    const { valor_actual, completado } = req.body;
    
    await sequelize.query(`
      UPDATE objetivos_usuario 
      SET valor_actual = ?, completado = ?, updated_at = NOW()
      WHERE id = ?
    `, { replacements: [valor_actual, completado, req.params.id] });
    
    res.json({ success: true, message: 'Objetivo actualizado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
