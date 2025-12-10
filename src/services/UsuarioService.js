const { Usuario } = require('../config/db');
const bcrypt = require('bcrypt');

module.exports = {
  async create(data) {
    if (!data.nombre || !data.email || !data.password) throw new Error('Nombre, email y password requeridos');
    const exists = await Usuario.findOne({ where: { email: data.email } });
    if (exists) throw new Error('Email ya registrado');
    const hash = await bcrypt.hash(data.password, 10);
    return Usuario.create({ nombre: data.nombre, email: data.email, password: hash, rol: data.rol || 'user' });
  },
  async getAll() { return Usuario.findAll({ attributes: { exclude: ['password'] } }); },
  async getById(id) { return Usuario.findByPk(id, { attributes: { exclude: ['password'] } }); },
  async update(id,data){ const u=await Usuario.findByPk(id); if(!u) throw new Error('No existe usuario'); if(data.password) data.password = await bcrypt.hash(data.password,10); return u.update(data); },
  async remove(id){ const u=await Usuario.findByPk(id); if(!u) throw new Error('No existe usuario'); return u.destroy(); },
  async verifyCredentials(email, password) {
    const u = await Usuario.findOne({ where: { email } });
    if (!u) return null;
    const ok = await bcrypt.compare(password, u.password);
    if (!ok) return null;
    return u;
  }
};