# Dark-Kitchens
monolito en base php para plataforma donde el dueño sube su menú, el cliente hace el pedido y el cocinero ve una lista de tareas pendientes.


## Descripción del proyecto
**Dark Kitchens** es una plataforma web pensada para emprendimientos de comida que trabajan únicamente por domicilios o delivery. La idea nace de la necesidad de organizar mejor los pedidos, el menú y las tareas de cocina, evitando depender solamente de WhatsApp para todo el proceso.

La plataforma permite que el dueño del negocio publique su menú, que los clientes realicen pedidos de forma más ordenada y que el cocinero visualice una lista de tareas pendientes según los pedidos recibidos.

## Problemática
Hoy en día muchos negocios de comida pequeños o medianos manejan sus pedidos por chats, llamadas o mensajes informales. Esto puede generar desorden, pérdida de información, demora en la atención y dificultad para llevar el control de los pedidos en tiempo real.

## Solución propuesta
Este proyecto busca ofrecer una solución sencilla y funcional para centralizar la operación básica de una dark kitchen mediante una plataforma web desarrollada en PHP.

## Objetivo general
Desarrollar una aplicación web en PHP que permita gestionar el menú, registrar pedidos y organizar las tareas de cocina en un emprendimiento de tipo dark kitchen.

## Objetivos específicos
- Permitir al administrador o dueño registrar, editar y eliminar productos del menú.
- Facilitar al cliente la visualización del menú y la realización de pedidos.
- Mostrar al cocinero una lista de pedidos pendientes para mejorar la organización en cocina.
- Centralizar la información del negocio en una sola plataforma.

## Usuarios del sistema
### Dueño o administrador
- Gestiona el menú.
- Revisa pedidos realizados.
- Controla el estado general del negocio.

### Cliente
- Consulta el menú disponible.
- Realiza pedidos.
- Puede visualizar información básica de su pedido.

### Cocinero
- Consulta los pedidos pendientes.
- Organiza la preparación según el orden de llegada o prioridad.

## Funcionalidades principales
- Registro e inicio de sesión de usuarios.
- Gestión del menú de productos.
- Visualización del menú por parte del cliente.
- Registro de pedidos.
- Listado de pedidos pendientes para cocina.
- Cambio de estado de pedidos.
- Panel básico de administración.

## Instalación local con Docker

### Requisitos
- Docker Desktop corriendo.
- Estar ubicado en la raíz del repo (donde vive `docker-compose.yml`).

### Arranque desde cero
```bash
cp laravel/.env.example laravel/.env
docker compose up -d --build
docker compose --profile tools run --rm composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Verificación rápida
```bash
docker compose config
docker compose ps
docker compose exec app php artisan --version
```

La app queda disponible en `http://localhost:8080` y MySQL en `127.0.0.1:3307`.

## Uso de Artisan
Para cualquier comando de Laravel:

```bash
docker compose exec app php artisan [comando]
```

## Nota importante sobre Composer
Las dependencias de Laravel **no se instalan en el build** de la imagen para desarrollo. Este proyecto monta `./laravel` sobre `/var/www/html`, por lo que cualquier `vendor/` generado en la imagen se tapa con el volumen del host. Por eso el flujo correcto es ejecutar `composer install` explícitamente con:

```bash
docker compose --profile tools run --rm composer install
```

## Demo CRUD administrativo de usuarios

### Seeder de admin (idempotente)
El seeder crea/actualiza automáticamente:

- Rol `Admin`
- Rol `User`
- Rol `Cook`
- Usuario admin de prueba

Credenciales del admin de prueba:

- Email: `admin@darkkitchens.local`
- Password: `Admin12345!`

### Ruta del módulo
- `http://localhost:8080/admin/users`

### Flujo de demostración
1. Ejecutar migraciones y seed:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

2. Iniciar sesión con el admin en:
- `http://localhost:8080/login`

3. Entrar al dashboard y abrir `Gestionar usuarios`.

4. En `/admin/users` demostrar:
- **Listar** usuarios.
- **Crear** un usuario con nombre, apellido, email, rol y password.
- **Editar** ese usuario (si dejas password vacío, se mantiene la anterior).
- **Eliminar** ese usuario.

5. Validar restricción de acceso:
- Inicia sesión con un usuario normal (`register`) y abre `/admin/users`.
- Debe responder `403` (acceso denegado).

### Verificación rápida por consola
```bash
docker compose exec app php artisan route:list | grep admin/users
```

Debe listar rutas `admin.users.*` para index, create, store, show, edit, update y destroy.

### Tests del CRUD admin
```bash
docker compose exec app php artisan test --filter=AdminUserCrudTest
```
