module.exports = (sequelize) => {
  const { DataTypes } = require('sequelize');
  const Usuario = sequelize.define('Usuario', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    nombre: { type: DataTypes.STRING(120), allowNull: false },
    email: { type: DataTypes.STRING(150), allowNull: false, unique: true },
    password: { type: DataTypes.STRING(255), allowNull: false },
    rol: { type: DataTypes.STRING(30), allowNull: false, defaultValue: 'user' } // user | admin
  }, { tableName: 'usuarios' });
  return Usuario;
};