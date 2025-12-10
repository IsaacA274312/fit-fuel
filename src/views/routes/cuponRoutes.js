const express = require('express');
const router = express.Router();
const { sequelize } = require('../../config/db');

// GET /api/cupones - Listar cupones activos
router.get('/', async (req, res) => {
  try {
    const [cupones] = await sequelize.query(`
      SELECT * FROM cupones 
      WHERE activo = 1 
      AND (fecha_expiracion IS NULL OR fecha_expiracion >= CURDATE())
      ORDER BY created_at DESC
    `);
    res.json({ success: true, data: cupones });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// POST /api/cupones/validar - Validar un cupón
router.post('/validar', async (req, res) => {
  try {
    const { codigo, total } = req.body;
    
    const [[cupon]] = await sequelize.query(`
      SELECT * FROM cupones 
      WHERE codigo = ? 
      AND activo = 1 
      AND (fecha_expiracion IS NULL OR fecha_expiracion >= CURDATE())
      AND (usos_maximos IS NULL OR usos_actuales < usos_maximos)
    `, { replacements: [codigo] });
    
    if (!cupon) {
      return res.json({ success: false, message: 'Cupón inválido o expirado' });
    }
    
    if (cupon.monto_minimo && total < cupon.monto_minimo) {
      return res.json({ 
        success: false, 
        message: `Compra mínima de $${cupon.monto_minimo} requerida` 
      });
    }
    
    let descuento = 0;
    if (cupon.tipo_descuento === 'porcentaje') {
      descuento = (total * cupon.valor_descuento) / 100;
      if (cupon.descuento_maximo && descuento > cupon.descuento_maximo) {
        descuento = cupon.descuento_maximo;
      }
    } else {
      descuento = cupon.valor_descuento;
    }
    
    res.json({ 
      success: true, 
      cupon: cupon,
      descuento: descuento
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// POST /api/cupones - Crear cupón (admin)
router.post('/', async (req, res) => {
  try {
    const { codigo, tipo_descuento, valor_descuento, monto_minimo, usos_maximos, fecha_expiracion } = req.body;
    
    await sequelize.query(`
      INSERT INTO cupones (codigo, tipo_descuento, valor_descuento, monto_minimo, usos_maximos, fecha_expiracion, activo)
      VALUES (?, ?, ?, ?, ?, ?, 1)
    `, { replacements: [codigo, tipo_descuento, valor_descuento, monto_minimo, usos_maximos, fecha_expiracion] });
    
    res.json({ success: true, message: 'Cupón creado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
