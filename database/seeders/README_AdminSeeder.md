# AdminSeeder - Seeder de Administradores

Este seeder crea administradores predeterminados para el sistema médico.

## Administradores creados

El seeder crea los siguientes administradores:

1. **Administrador Principal**
   - Username: `admin`
   - Email: `admin@sistemamedico.com`
   - Password: `admin123`

2. **Super Administrador**
   - Username: `superadmin`
   - Email: `superadmin@sistemamedico.com`
   - Password: `superadmin123`

3. **Administrador Sistema**
   - Username: `adminsistema`
   - Email: `sistema@sistemamedico.com`
   - Password: `sistema123`

## Cómo ejecutar el seeder

### Ejecutar solo el AdminSeeder:
```bash
php artisan db:seed --class=AdminSeeder
```

### Ejecutar todos los seeders (incluye AdminSeeder):
```bash
php artisan db:seed
```

### Ejecutar con refresh de base de datos:
```bash
php artisan migrate:fresh --seed
```

## Características del seeder

- **Verificación de existencia**: No duplica usuarios existentes
- **Passwords hasheadas**: Utiliza Hash::make() para seguridad
- **Email verificado**: Los usuarios se crean con email_verified_at
- **Activos por defecto**: Todos los administradores se crean activos
- **Dependencias**: Requiere que el RoleSeeder se ejecute primero

## Seguridad

⚠️ **IMPORTANTE**: Las contraseñas predeterminadas deben cambiarse en producción.

## Personalización

Para agregar más administradores, edita el array `$admins` en el método `run()` del seeder.
