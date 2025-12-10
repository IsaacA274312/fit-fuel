const jwt = require('jsonwebtoken');
const JWT_SECRET = process.env.JWT_SECRET || 'change_this_secret';

function authMiddleware(req, res, next) {
  const auth = req.headers.authorization;
  if (!auth || !auth.startsWith('Bearer ')) return res.status(401).json({ success: false, message: 'No autorizado' });
  const token = auth.split(' ')[1];
  try {
    const payload = jwt.verify(token, JWT_SECRET);
    req.user = payload;
    return next();
  } catch (e) {
    return res.status(401).json({ success: false, message: 'Token inválido' });
  }
}

function adminMiddleware(req, res, next) {
  if (!req.user) return res.status(401).json({ success: false, message: 'No autorizado' });
  if (req.user.rol !== 'admin') return res.status(403).json({ success: false, message: 'Requiere rol admin' });
  return next();
}

module.exports = { authMiddleware, adminMiddleware };