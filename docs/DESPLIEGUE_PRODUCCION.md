# Despliegue de PichangasYa en produccion

## 1. Preparar el paquete

Las reglas `export-ignore` de `.gitattributes` permiten conservar pruebas y evidencias en Git sin incluirlas en el paquete productivo.

```bash
git archive --format=zip --output=pichangasya-produccion.zip HEAD
```

El paquete excluye `tests`, `docs`, configuraciones locales del editor, `phpunit.xml` y plantillas de entorno. Tampoco incluye `.env`, `vendor`, `node_modules`, logs, sesiones ni archivos temporales porque no forman parte del repositorio.

## 2. Variables del servidor

Crear un `.env` nuevo directamente en el servidor. No copiar el `.env` local.

Configuracion minima recomendada:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dominio.example
LOG_LEVEL=warning
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
NIUBIZ_SIMULADO=false
SUPPORT_EMAIL=soporte@dominio.example
```

Generar una clave exclusiva con `php artisan key:generate`. Configurar credenciales reales y diferentes para base de datos, correo y Niubiz. No reutilizar secretos del entorno local.

## 3. Dependencias y recursos

```bash
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci
npm run build
```

`node_modules` se puede retirar del servidor despues de compilar si el despliegue no ejecuta Vite allí.

## 4. Base de datos y Laravel

Realizar primero un respaldo de la base de datos y de los archivos cargados por usuarios.

```bash
php artisan down --retry=60
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan queue:restart
php artisan up
```

No ejecutar seeders de demostracion en produccion.

## 5. Servidor web

- El `DocumentRoot` debe apuntar a la carpeta `public`.
- Forzar HTTPS y renovar automaticamente el certificado TLS.
- Permitir escritura solo en `storage` y `bootstrap/cache` para el usuario del servidor web.
- Mantener `ServerTokens Prod` y `ServerSignature Off` en Apache.
- Configurar respaldos, rotacion de logs y un proceso supervisor para las colas.
- Bloquear el acceso web a `.env`, `.git`, respaldos, logs y archivos de configuracion.

## 6. Verificacion posterior

- Confirmar que inicio, registro, login, recuperacion y cierre de sesion funcionan.
- Verificar reservas, pagos simulados desactivados y envio de correo real.
- Confirmar que un visitante no puede entrar al panel administrativo.
- Revisar que `APP_DEBUG` permanezca desactivado ante errores.
- Ejecutar OWASP ZAP contra el dominio autorizado y comprobar 0 alertas altas, medias y bajas.
- Revisar logs y metricas durante las primeras horas del despliegue.

## 7. Reversion

Conservar el paquete anterior, el respaldo de base de datos y los archivos cargados. Si falla una verificacion critica, activar mantenimiento, restaurar la version y la base compatibles, limpiar caches y volver a habilitar el servicio.
