# 🔐 Autenticación JWT en Laravel 12 — Biblioteca Comunitaria

Este paquete genera automáticamente el proyecto Laravel 12 con `tymon/jwt-auth`
ya configurado. Tú solo necesitas Docker: al encender el contenedor, un script
crea el proyecto desde cero, instala JWT, copia el código (modelos, controladores,
rutas, migraciones) y corre las migraciones. No hay Stripe ni Swagger en esta
tarea — solo autenticación JWT y CRUD de libros protegido.

---

## 0. Requisitos (Linux Mint / Ubuntu)

```bash
docker --version
docker compose version
```

Si no los tienes:

```bash
sudo apt update
sudo apt install -y ca-certificates curl gnupg
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo usermod -aG docker $USER
newgrp docker
```

---

## 1. Descomprime y entra a la carpeta

```bash
unzip library-jwt-api.zip
cd library-jwt-api
```

```
library-jwt-api/
├── docker-compose.yml
├── Dockerfile
├── entrypoint.sh
├── overlay/          ← código de la biblioteca, se copia automáticamente
├── src/               ← aquí se crea el proyecto Laravel real (vacío por ahora)
└── README.md
```

---

## 2. Levanta todo con un solo comando

```bash
docker compose up -d --build
```

La primera vez tarda unos minutos: crea Laravel 12, instala `tymon/jwt-auth`,
copia el código, genera la llave de la app y el secreto JWT, y corre las
migraciones. Sigue el progreso con:

```bash
docker compose logs -f app
```

Cuando veas `Proyecto listo.`, ya puedes usar la API en http://localhost:8000
y phpMyAdmin en http://localhost:8081 (usuario `root`, contraseña `root`).

---

## 3. Prueba los endpoints

```bash
# 1) Registrar usuario
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Andre","email":"andre@example.com","password":"password123","password_confirmation":"password123"}'

# Copia el "access_token" que te devuelve

# 2) Ver mi perfil (requiere token)
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer TU_TOKEN_AQUI"

# 3) Crear un libro (requiere token)
curl -X POST http://localhost:8000/api/books \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{"title":"Cien años de soledad","author":"Gabriel García Márquez","isbn":"9780307474728","available_copies":3}'

# 4) Listar libros (requiere token)
curl http://localhost:8000/api/books \
  -H "Authorization: Bearer TU_TOKEN_AQUI"

# 5) Cerrar sesión (invalida el token)
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

Si intentas acceder a `/api/books` sin el header `Authorization`, o con el
token ya invalidado por logout, la API responde `401 No autenticado`.

---

## 4. Subir el proyecto a GitHub

El código real de Laravel queda dentro de `src/`. Esa carpeta es la que subes:

```bash
cd src
git init
git add .
git commit -m "Autenticación JWT en Laravel 12 - Biblioteca comunitaria"
git branch -M main
```

1. Crea un repositorio público en https://github.com/new (sin README ni .gitignore).
2. Conéctalo y sube:

```bash
git remote add origin https://github.com/TU_USUARIO/NOMBRE_REPO.git
git push -u origin main
```

Si te pide contraseña y falla: GitHub ya no acepta contraseña normal, necesitas
un **Personal Access Token** (https://github.com/settings/tokens → "Generate
new token (classic)" → permiso `repo`). Úsalo como contraseña cuando Git te
lo pida.

El `.env` real **no se sube** (Laravel ya lo trae en `.gitignore`). El
`.env.example` que se genera automáticamente sí incluye las variables `JWT_SECRET`
y `JWT_TTL` documentadas, tal como pide el entregable.

---

## 5. Endpoints implementados

**Públicos**

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | /api/auth/register | Registrar nuevo usuario |
| POST | /api/auth/login | Iniciar sesión y obtener token JWT |

**Requieren token** (`Authorization: Bearer {token}`)

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | /api/auth/logout | Cerrar sesión e invalidar token |
| GET | /api/auth/me | Perfil del usuario autenticado |
| GET | /api/books | Listar todos los libros |
| GET | /api/books/{id} | Obtener un libro específico |
| POST | /api/books | Crear un nuevo libro |
| PUT/PATCH | /api/books/{id} | Actualizar un libro |
| DELETE | /api/books/{id} | Eliminar un libro |

---

## 6. Cómo funciona la seguridad (para tu sustentación)

- **Cifrado de contraseñas**: se guardan con `Hash::make()` (bcrypt), nunca en texto plano. El cast `'password' => 'hashed'` en el modelo `User` refuerza esto también al usar `update()`.
- **Tokens JWT**: `tymon/jwt-auth` firma el token con `JWT_SECRET` (generado con `php artisan jwt:secret`, guardado solo en `.env`, nunca en el repo).
- **Sesiones stateless**: el guard `api` usa `driver => jwt` en `config/auth.php`; el servidor no guarda sesión, cada request se valida solo con el token enviado.
- **Middleware de protección**: las rutas de `/api/books` y `/api/auth/me` y `/api/auth/logout` están dentro de `Route::middleware('auth:api')`, que rechaza automáticamente cualquier request sin un token válido.
- **Invalidación de tokens**: `Auth::guard('api')->logout()` agrega el token a una blacklist interna, así que un token usado en logout ya no sirve para futuras peticiones.
- **Manejo de errores uniforme**: `bootstrap/app.php` intercepta errores de autenticación, validación y "no encontrado" para devolver siempre JSON con la misma forma (`success`, `message`).

---

## 7. Comandos útiles

```bash
docker compose logs -f app                 # ver logs en vivo
docker compose exec app bash                # entrar a la terminal del contenedor
docker compose exec app php artisan migrate:fresh   # reiniciar la base de datos
docker compose down                          # apagar todo
docker compose down -v && rm -rf src/* && docker compose up -d --build   # empezar de cero
```
