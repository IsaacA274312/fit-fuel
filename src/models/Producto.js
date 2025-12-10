module.exports = (sequelize) => {
  const { DataTypes } = require('sequelize');
  const Producto = sequelize.define('Producto', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    nombre: { type: DataTypes.STRING(150), allowNull: false },
    descripcion: { type: DataTypes.TEXT, allowNull: true },
    precio: { type: DataTypes.DECIMAL(10,2), allowNull: false, defaultValue: 0.00 },
    stock: { type: DataTypes.INTEGER, allowNull: false, defaultValue: 0 },
    categoriaId: { type: DataTypes.INTEGER, allowNull: true }
  }, { tableName: 'productos' });
  return Producto;
};