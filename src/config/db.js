require('dotenv').config();
const { Sequelize } = require('sequelize');
const path = require('path');

const sequelize = new Sequelize(
  process.env.DB_NAME || 'fitandfuel',
  process.env.DB_USER || 'root',
  process.env.DB_PASS || '',
  {
    host: process.env.DB_HOST || 'localhost',
    port: process.env.DB_PORT || 3306,
    dialect: 'mysql',
    logging: false,
    define: {
      timestamps: false
    }
  }
);

// cargar modelos
const Categoria = require(path.join(__dirname, '..', 'models', 'Categoria'))(sequelize);
const Producto = require(path.join(__dirname, '..', 'models', 'Producto'))(sequelize);
const Cliente = require(path.join(__dirname, '..', 'models', 'Cliente'))(sequelize);
const Usuario = require(path.join(__dirname, '..', 'models', 'Usuario'))(sequelize);
const Pedido = require(path.join(__dirname, '..', 'models', 'Pedido'))(sequelize);

// Relaciones
Categoria.hasMany(Producto, { foreignKey: 'categoriaId' });
Producto.belongsTo(Categoria, { foreignKey: 'categoriaId' });

Cliente.hasMany(Pedido, { foreignKey: 'clienteId' });
Pedido.belongsTo(Cliente, { foreignKey: 'clienteId' });

Usuario.hasMany(Pedido, { foreignKey: 'usuarioId' });
Pedido.belongsTo(Usuario, { foreignKey: 'usuarioId' });

module.exports = {
  sequelize,
  Categoria,
  Producto,
  Cliente,
  Usuario,
  Pedido
};