const { Producto } = require('../config/db');

module.exports = {
  async create(data) {
    if (!data.nombre || !data.precio) throw new Error('Nombre y precio requeridos');
    return Producto.create(data);
  },
  async getAll() { return Producto.findAll(); },
  async getById(id) { return Producto.findByPk(id); },
  async update(id,data){ const p=await Producto.findByPk(id); if(!p) throw new Error('No existe producto'); return p.update(data); },
  async remove(id){ const p=await Producto.findByPk(id); if(!p) throw new Error('No existe producto'); return p.destroy(); }
};