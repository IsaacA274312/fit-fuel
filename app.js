const express = require('express');
const mysql = require("mysql2");
const myConnection = require("express-myconnection");

const app = express();

// --- Tu configuración de conexión ---
// ¡PELIGRO! Estás usando 'root'. Solo para desarrollo local.
app.use(myConnection(mysql, {
  host: 'localhost',
  user: 'root',
  password: '9622?Agi', // Esta contraseña ahora es visible
  port: 3306,
  database: 'fitandfuel'
}));

// --- Tu ruta original ---
app.get('/', (req, res) => res.send('Servidor funcionando. ¿Si me explico?'));

// --- ¡NUEVA RUTA AÑADIDA! ---
app.get('/crear-usuario', (req, res) => {
  
  // Obtenemos una conexión del pool que 'express-myconnection' maneja
  req.getConnection((err, connection) => {
    if (err) {
      console.error(err);
      return res.status(500).send('Error al obtener la conexión a la DB');
    }

    // Definimos los 3 comandos SQL que necesitamos
    const sql_create = "CREATE USER 'usuario_express'@'localhost' IDENTIFIED BY 'Pass.Express123';";
    const sql_grant = "GRANT ALL PRIVILEGES ON fitandfuel.* TO 'usuario_express'@'localhost';";
    const sql_flush = "FLUSH PRIVILEGES;";

    // --- Ejecutamos las consultas en orden ---
    // (Esto se llama "callback hell", hay formas más limpias con Promises,
    // pero funciona para este ejemplo)

    // 1. Crear el usuario
    connection.query(sql_create, (err, result) => {
      if (err) {
        // Un error común es que el usuario ya exista
        if (err.code === 'ER_CANNOT_CREATE_USER_WITH_GRANT') {
             return res.status(400).send('Error: El usuario ya existe o la contraseña no es válida.');
        }
        console.error(err);
        return res.status(500).send(err.message);
      }

      console.log('Paso 1: Usuario creado.');

      // 2. Otorgar permisos
      connection.query(sql_grant, (err, result) => {
        if (err) {
          console.error(err);
          return res.status(500).send(err.message);
        }
        
        console.log('Paso 2: Permisos otorgados.');

        // 3. Aplicar los cambios
        connection.query(sql_flush, (err, result) => {
          if (err) {
            console.error(err);
            return res.status(500).send(err.message);
          }

          console.log('Paso 3: Privilegios actualizados.');
          
          // ¡Todo salió bien!
          res.send('¡Éxito! Usuario "usuario_express" creado y con permisos en la DB "fitandfuel".');
        
        }); // Fin Query 3
      }); // Fin Query 2
    }); // Fin Query 1
  }); // Fin req.getConnection
}); // Fin app.get

// --- Tu servidor escuchando ---
app.listen(8080, () => {
  console.log('Servidor escuchando en el puerto 8080');
  console.log('Visita http://localhost:8080/ para ver la ruta principal');
  console.log('Visita http://localhost:8080/crear-usuario para crear el usuario');
});