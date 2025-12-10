const jwt = require('jsonwebtoken');
const UsuarioService = require('../services/UsuarioService');

const JWT_SECRET = process.env.JWT_SECRET || 'change_this_secret';

module.exports = {
  async login(req, res, next) {
    try {
      const { email, password } = req.body;
      const user = await UsuarioService.verifyCredentials(email, password);
      if (!user) return res.status(401).json({ success: false, message: 'Credenciales inválidas' });
      const payload = { id: user.id, email: user.email, rol: user.rol, nombre: user.nombre };
      const token = jwt.sign(payload, JWT_SECRET, { expiresIn: '8h' });
      res.json({ success: true, token, user: payload });
    } catch (e) { next(e); }
  },

  // Register endpoint to create usuario (public)
  async register(req, res, next) {
    try {
      const u = await UsuarioService.create(req.body);
      res.json({ success: true, data: { id: u.id, nombre: u.nombre, email: u.email }});
    } catch (e) { next(e); }
  }
};