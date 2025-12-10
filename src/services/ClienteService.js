const { Cliente } = require('../config/db');

module.exports = {
  async create(data) {
    if (!data.nombre || !data.email) throw new Error('Nombre y email requeridos');
    return Cliente.create(data);
  },
  async getAll() { return Cliente.findAll(); },
  async getById(id) { return Cliente.findByPk(id); },
  async update(id,data){ const c=await Cliente.findByPk(id); if(!c) throw new Error('No existe cliente'); return c.update(data); },
  async remove(id){ const c=await Cliente.findByPk(id); if(!c) throw new Error('No existe cliente'); return c.destroy(); }
};