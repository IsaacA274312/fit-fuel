const PedidoService = require('../services/PedidoService');

module.exports = {
  async create(req, res, next) {
    try {
      const item = await PedidoService.create(req.body);
      res.json({ success: true, data: item });
    } catch (e) { next(e); }
  },
  async getAll(req, res, next) {
    try {
      const list = await PedidoService.getAll();
      res.json({ success: true, data: list });
    } catch (e) { next(e); }
  },
  async getById(req, res, next) {
    try {
      const item = await PedidoService.getById(req.params.id);
      if (!item) return res.status(404).json({ success: false, message: 'No encontrado' });
      res.json({ success: true, data: item });
    } catch (e) { next(e); }
  },
  async update(req, res, next) {
    try {
      const updated = await PedidoService.update(req.params.id, req.body);
      res.json({ success: true, data: updated });
    } catch (e) { next(e); }
  },
  async remove(req, res, next) {
    try {
      await PedidoService.remove(req.params.id);
      res.json({ success: true, message: 'Eliminado' });
    } catch (e) { next(e); }
  }
};
