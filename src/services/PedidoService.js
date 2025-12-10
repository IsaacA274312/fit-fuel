const { Pedido } = require('../config/db');

module.exports = {
  async create(data) {
    if (!data.clienteId) throw new Error('clienteId requerido');
    return Pedido.create(data);
  },
  async getAll() { return Pedido.findAll(); },
  async getById(id) { return Pedido.findByPk(id); },
  async update(id,data){ const p=await Pedido.findByPk(id); if(!p) throw new Error('No existe pedido'); return p.update(data); },
  async remove(id){ const p=await Pedido.findByPk(id); if(!p) throw new Error('No existe pedido'); return p.destroy(); }
};