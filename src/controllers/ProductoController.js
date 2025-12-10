const ProductoService = require('../services/ProductoService');

module.exports = {
  async create(req, res, next) {
    try {
      const item = await ProductoService.create(req.body);
      res.json({ success: true, data: item });
    } catch (e) { next(e); }
  },
  async getAll(req, res, next) {
    try {
      const list = await ProductoService.getAll();
      res.json({ success: true, data: list });
    } catch (e) { next(e); }
  },
  async getById(req, res, next) {
    try {
      const item = await ProductoService.getById(req.params.id);
      if (!item) return res.status(404).json({ success: false, message: 'No encontrado' });
      res.json({ success: true, data: item });
    } catch (e) { next(e); }
  },
  async update(req, res, next) {
    try {
      const updated = await ProductoService.update(req.params.id, req.body);
      res.json({ success: true, data: updated });
    } catch (e) { next(e); }
  },
  async remove(req, res, next) {
    try {
      await ProductoService.remove(req.params.id);
      res.json({ success: true, message: 'Eliminado' });
    } catch (e) { next(e); }
  }
};
