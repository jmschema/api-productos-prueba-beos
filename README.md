# API de Productos

API RESTful desarrollada en Laravel para gestionar productos y sus precios en distintas divisas.

## Stack

- PHP 8.3+ / Laravel 13
- Eloquent ORM con SQLite (compatible con MySQL)
- Laravel Sanctum (autenticación por tokens)
- PHPUnit (tests unitarios y de feature)

## Instalación

```bash
git clone https://github.com/jmschema/api-productos-prueba-beos.git
cd api-productos-prueba-beos
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Si la base SQLite no existe, Laravel ofrecerá crearla al migrar. La API queda disponible en `http://127.0.0.1:8000/api`.

El seeder crea las divisas USD, CLP, EUR y MXN, 10 productos de ejemplo y un usuario de prueba:

| Email | Contraseña |
|---|---|
| test@example.com | password |

## Autenticación

1. Obtener un token con `POST /api/login`.
2. Enviarlo en cada request: `Authorization: Bearer {token}`.
3. Todas las requests deben incluir `Accept: application/json`.

Los tokens expiran a las 24 horas.

## Endpoints

| Método | Ruta | Descripción | Auth |
|---|---|---|---|
| POST | `/api/login` | Obtener token | No |
| POST | `/api/logout` | Revocar el token actual | Sí |
| GET | `/api/products` | Listar productos (paginado) | Sí |
| POST | `/api/products` | Crear producto | Sí |
| GET | `/api/products/{id}` | Ver producto con su divisa y precios | Sí |
| PUT | `/api/products/{id}` | Actualizar producto (parcial) | Sí |
| DELETE | `/api/products/{id}` | Eliminar producto (soft delete) | Sí |
| GET | `/api/products/{id}/prices` | Listar precios del producto | Sí |
| POST | `/api/products/{id}/prices` | Registrar precio en otra divisa | Sí |
| GET | `/api/currencies` | Listar divisas disponibles | Sí |

`GET /api/products` acepta `?per_page=` (máximo 100) y `?page=`.

### Ejemplo: crear producto

```http
POST /api/products
```

```json
{
    "name": "Silla ergonómica",
    "description": "Silla de oficina con soporte lumbar",
    "price": 150.00,
    "currency_id": 1,
    "tax_cost": 28.50,
    "manufacturing_cost": 60.00
}
```

Respuesta `201 Created`:

```json
{
    "data": {
        "id": 11,
        "name": "Silla ergonómica",
        "description": "Silla de oficina con soporte lumbar",
        "price": "150.00",
        "currency_id": 1,
        "currency": { "id": 1, "name": "USD", "symbol": "$", "exchange_rate": "1.000000" },
        "tax_cost": "28.50",
        "manufacturing_cost": "60.00",
        "created_at": "2026-10-27T22:00:00+00:00",
        "updated_at": "2026-10-27T22:00:00+00:00"
    }
}
```

### Ejemplo: registrar precio en otra divisa

```http
POST /api/products/11/prices
```

```json
{ "currency_id": 2, "price": 145000 }
```

Si se omite `price`, se calcula automáticamente desde el precio base usando las tasas de cambio:

```json
{ "currency_id": 2 }
```

## Códigos de respuesta

| Código | Significado |
|---|---|
| 200 | OK |
| 201 | Recurso creado |
| 204 | Eliminado / sin contenido |
| 401 | No autenticado o credenciales inválidas |
| 404 | Recurso no encontrado |
| 422 | Error de validación (detalle por campo en `errors`) |
| 429 | Demasiadas requests |

## Decisiones de diseño

- **Divisa base vs. precios adicionales:** el precio base vive en `products` (`price` + `currency_id`); la tabla `product_prices` guarda los precios en otras divisas. No se permite registrar un precio adicional en la divisa base ni dos precios en la misma divisa.
- **Tasas de cambio:** `exchange_rate` representa las unidades de la divisa por 1 USD. La conversión está aislada en `App\Services\CurrencyConverter`.
- **Montos:** columnas `decimal` (no `float`) para evitar errores de redondeo; se devuelven como string para no perder precisión.
- **Soft deletes** en productos para conservar historial.
- **`GET /api/currencies`:** endpoint adicional para que el cliente conozca los `currency_id` válidos.

## Seguridad

- Autenticación con tokens de Sanctum (almacenados como hash) con expiración.
- Rate limiting: 60 req/min por usuario y 5 intentos de login por minuto por IP.
- Validación con Form Requests y protección contra mass assignment (`$fillable`).
- Mensaje genérico de login para evitar enumeración de usuarios.
- Errores siempre en JSON y sin detalles internos. En producción usar `APP_DEBUG=false`.

## Estructura

```
app/
├── Http/Controllers/Api/   Controladores delgados
├── Http/Requests/          Validación
├── Http/Resources/         Transformación a JSON
├── Models/                 Modelos Eloquent y relaciones
└── Services/               Lógica de negocio (conversión de divisas)
```

## Tests

```bash
php artisan test
```

Incluye tests unitarios del conversor de divisas y tests de feature para autenticación, CRUD de productos, precios, validaciones y errores.

## Documentación

Colección de Postman en `docs/Prueba-de-Api-de-Productos-Beos.postman_collection.json`. Al importarla, ajustar la variable `base_url`. La request **Login** guarda el token automáticamente.