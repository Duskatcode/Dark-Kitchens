# Dark Kitchens

Aplicación web para gestión básica de una dark kitchen con roles diferenciados para administración, clientes y cocina.

## Stack

- Laravel 12
- Blade
- Vite
- MySQL 8
- Docker Compose
- PHPUnit

## Roles principales

- `admin`: administra usuarios, roles, permisos, categorías, productos y pedidos.
- `client`: consulta menú, crea pedidos, ve sus pedidos y cancela pedidos pendientes.
- `cook`: consulta la cola de cocina y actualiza estados de pedidos.

## Usuarios de prueba

| Rol | Email | Password |
|---|---|---|
| Admin | `admin@test.com` | `password` |
| Cliente | `client@test.com` | `password` |
| Cocina | `cook@test.com` | `password` |

## Funcionalidades principales

### Admin

- Dashboard administrativo.
- Gestión de usuarios.
- Gestión de roles.
- Gestión de permisos por rol.
- Gestión de categorías.
- Gestión de productos.
- Gestión de pedidos.
- Filtro de pedidos por estado.
- Cambio de estado de pedidos.
- Eliminación restringida de pedidos pendientes.

### Cliente

- Ver menú.
- Ver detalle de productos disponibles.
- Crear pedidos.
- Ver pedidos propios.
- Cancelar pedidos propios en estado `pending`.

### Cocina

- Ver pedidos `pending` e `in_progress`.
- Cambiar pedidos de `pending` a `in_progress`.
- Cambiar pedidos de `in_progress` a `completed`.

## Estados de pedidos

- `pending`
- `in_progress`
- `completed`
- `cancelled`

## Permisos RBAC

El sistema usa una combinación de middleware:

```txt
auth
role:{role}
permission:{permission_key}

Permisos principales:

admin.dashboard.view
admin.users.view
admin.users.create
admin.users.update
admin.users.delete
admin.roles.view
admin.roles.create
admin.roles.update
admin.roles.delete
admin.roles.manage_permissions
admin.categories.view
admin.categories.create
admin.categories.update
admin.categories.delete
admin.products.view
admin.products.create
admin.products.update
admin.products.delete
admin.orders.view
admin.orders.update_status
admin.orders.delete
client.orders.view
client.orders.create
client.orders.cancel
cook.orders.view
cook.orders.update_status
Instalación local con Docker
Requisitos
Docker Desktop corriendo.
Estar ubicado en la raíz del repo, donde está docker-compose.yml.
Arranque
cp laravel/.env.example laravel/.env
docker compose up -d --build
docker compose --profile tools run --rm composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

Verificar contenedores:

docker compose ps

La app queda disponible normalmente en:

http://localhost:8080

MySQL queda disponible en:

127.0.0.1:3307
Migraciones y seeders

Para una base existente:

docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=PermissionSeeder
docker compose exec app php artisan db:seed --class=RoleSeeder

Para un entorno limpio de desarrollo:

docker compose exec app php artisan migrate:fresh --seed

migrate:fresh --seed borra y recrea toda la base de datos.

Frontend
cd laravel
npm install
npm run build
cd ..
Tests
docker compose exec app php artisan test

Última validación conocida:

93 tests passed
338 assertions
URLs principales
General
/
/login
/dashboard
Admin
/admin/dashboard
/admin/users
/admin/users/create
/admin/roles
/admin/roles/create
/admin/categories
/admin/products
/admin/orders
Cliente
/client/dashboard
/client/menu
/client/orders
Cocina
/cook/dashboard
/cook/orders
Flujo recomendado de prueba manual
Iniciar sesión como admin.
Crear usuario cliente.
Crear usuario cocina.
Crear categoría.
Crear producto disponible.
Iniciar sesión como cliente.
Crear pedido.
Cancelar pedido si está en estado pending.
Crear otro pedido.
Iniciar sesión como cocina.
Cambiar pedido de pending a in_progress.
Cambiar pedido de in_progress a completed.
Iniciar sesión como admin.
Revisar /admin/orders.
Filtrar pedidos por estado.
Ver detalle de pedido.
Intentar cambios de estado válidos e inválidos.
Validación antes de entrega
docker compose ps
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
docker compose exec app php artisan test

cd laravel
npm run build
cd ..
Rama principal de trabajo
feat/finalize-dark-kitchens-rbac

