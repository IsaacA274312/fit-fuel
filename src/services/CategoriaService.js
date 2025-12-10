const { Categoria } = require('../config/db');

module.exports = {
  async create(data) {
    if (!data.nombre) throw new Error('Nombre requerido');
    const exists = await Categoria.findOne({ where: { nombre: data.nombre } });
    if (exists) throw new Error('Categoría ya existe');
    return Categoria.create(data);
  },
  async getAll() {
    return Categoria.findAll();
  },
  async getById(id) {
    return Categoria.findByPk(id);
  },
  async update(id, data) {
    const cat = await Categoria.findByPk(id);
    if (!cat) throw new Error('No existe categoría');
    return cat.update(data);
  },
  async remove(id) {
    const cat = await Categoria.findByPk(id);
    if (!cat) throw new Error('No existe categoría');
    return cat.destroy();
  }
};