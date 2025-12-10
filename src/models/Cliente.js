module.exports = (sequelize) => {
  const { DataTypes } = require('sequelize');
  const Cliente = sequelize.define('Cliente', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    nombre: { type: DataTypes.STRING(120), allowNull: false },
    email: { type: DataTypes.STRING(150), allowNull: false, unique: true },
    telefono: { type: DataTypes.STRING(50), allowNull: true },
    fecha_nacimiento: { type: DataTypes.DATEONLY, allowNull: true }
  }, { tableName: 'clientes' });
  return Cliente;
};