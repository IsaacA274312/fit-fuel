const express = require('express');
const router = express.Router();
const { sequelize } = require('../../config/db');

// GET /api/progreso/usuario/:id - Obtener progreso del usuario
router.get('/usuario/:id', async (req, res) => {
  try {
    const [registros] = await sequelize.query(`
      SELECT * FROM progreso_usuario 
      WHERE usuario_id = ? 
      ORDER BY fecha_registro DESC
    `, { replacements: [req.params.id] });
    
    res.json({ success: true, data: registros });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// POST /api/progreso - Registrar nuevo progreso
router.post('/', async (req, res) => {
  try {
    const { usuario_id, peso, grasa_corporal, masa_muscular, circunferencia_pecho, 
            circunferencia_cintura, circunferencia_cadera, circunferencia_brazo, 
            circunferencia_pierna, notas } = req.body;
    
    await sequelize.query(`
      INSERT INTO progreso_usuario (usuario_id, peso, grasa_corporal, masa_muscular, 
        circunferencia_pecho, circunferencia_cintura, circunferencia_cadera, 
        circunferencia_brazo, circunferencia_pierna, notas, fecha_registro)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    `, { replacements: [usuario_id, peso, grasa_corporal, masa_muscular, circunferencia_pecho, 
                        circunferencia_cintura, circunferencia_cadera, circunferencia_brazo, 
                        circunferencia_pierna, notas] });
    
    res.json({ success: true, message: 'Progreso registrado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
