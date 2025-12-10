module.exports = (sequelize) => {
  const { DataTypes } = require('sequelize');
  const Pedido = sequelize.define('Pedido', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    clienteId: { type: DataTypes.INTEGER.UNSIGNED, allowNull: false, references: { model: 'clientes', key: 'id' } },
    usuarioId: { type: DataTypes.INTEGER.UNSIGNED, allowNull: true, references: { model: 'usuarios', key: 'id' } }, // quien creó/gestionó
    total: { type: DataTypes.DECIMAL(10,2), allowNull: false, defaultValue: 0.00 },
    estado: { type: DataTypes.STRING(50), allowNull: false, defaultValue: 'pendiente' },
    creado_en: { type: DataTypes.DATE, allowNull: false, defaultValue: DataTypes.NOW }
  }, { tableName: 'pedidos' });
  return Pedido;
};