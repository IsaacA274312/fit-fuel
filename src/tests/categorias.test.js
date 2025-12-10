const request = require('supertest');
const app = require('../../../index.js'); // ajustar ruta si se ejecuta desde /src
let token = null;

beforeAll(async () => {
  // crear usuario admin y obtener token (si endpoint register crea user)
  await request(app).post('/api/auth/register').send({ nombre: 'admin', email: 'admin@test.local', password: 'Admin123!', rol: 'admin' });
  const res = await request(app).post('/api/auth/login').send({ email: 'admin@test.local', password: 'Admin123!' });
  token = res.body.token;
});

describe('Categorias CRUD', () => {
  let id;
  test('POST /api/categorias', async () => {
    const res = await request(app).post('/api/categorias').set('Authorization', `Bearer ${token}`).send({ nombre: 'TestCat' });
    expect(res.statusCode).toBe(200);
    expect(res.body.success).toBe(true);
    id = res.body.data.id;
  });

  test('GET /api/categorias', async () => {
    const res = await request(app).get('/api/categorias').set('Authorization', `Bearer ${token}`);
    expect(res.statusCode).toBe(200);
    expect(Array.isArray(res.body.data)).toBe(true);
  });

  test('PUT /api/categorias/:id', async () => {
    const res = await request(app).put('/api/categorias/' + id).set('Authorization', `Bearer ${token}`).send({ descripcion: 'Mod' });
    expect(res.statusCode).toBe(200);
    expect(res.body.success).toBe(true);
  });

  test('DELETE /api/categorias/:id', async () => {
    const res = await request(app).delete('/api/categorias/' + id).set('Authorization', `Bearer ${token}`);
    expect(res.statusCode).toBe(200);
    expect(res.body.success).toBe(true);
  });
});