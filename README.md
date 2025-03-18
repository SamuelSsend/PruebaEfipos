# Publicar a producción

Nota: Se supone que el directorio de produción es `backoffice.efiposdelivery.com` y está en la raíz del servidor.
Nota: En el repositorio hay imágenes de prueba, una actualización las podría sobreescribir.
Nota: Se recomienda instalar las dependencias (vendor) vía terminal, aunque en este manual se supone que el entorno siempre tendrá un PHP7.4 y las dependencias necesarias.

## Configuración necesaria la primera vez:
- `.env` (coger como base .env.example y cambiar la parte de base de datos)
- Base se de datos. Cargar el fichero database.sql en la base de datos instalada (vía PHPMyAdmin o el que sea)
    - Esto creará un usuario `efiposdelivery@gmail.com` con contraseña `Efi*Pos*789`

## Actualización de la aplicación (con Terminal):
- Descargar fichero .zip desde la rama `main` GitHub. _Pulsar sobre el botón `code` y después Download ZIP_. 
  - Ejemplo:
  - ```DB_CONNECTION=mysql
    DB_HOST=1270.0.0.1
    DB_PORT=3306
    DB_DATABASE=lc43eqfj_delivery
    DB_USERNAME=lc43eqfj_webs
    DB_PASSWORD=Efi*pos*789```
- Con el CPanel (o vía SCP): Subir el fichero ZIP en la raiz y descomprimirlo. Debería crear una carpeta llamada `efipos-delivery-main`
- Entrar vía comandos en el CPanel (_Terminal_) y actualizar:
  - `cp -rv efipos-delivery-main/* backoffice.efiposdelivery.com/`
- Se pueden borrar los ficheros temporales: `efipos-delivery-main.zip` y la carpeta `efipos-delivery-main`

# Docker

## Desarrollo

`docker compose -f docker-compose.dev.yml up`

La aplicación va a estar disponible en `http://localhost`

### A tener en cuenta:
- Pueden salir mensajes de error mientras el contener de MySQL se está creando y configurando. Basta esperar un rato.
- Para ejecutar comandos en el contenedor, se puede utilizar `docker exec -it efipo-delivery-efipos-1 COMANDO`.
  - Por ejemplo: `docker exec -it efipo-delivery-efipos-1 php artisan efipos:sync` para obtener la carta.

## Producción

`docker compose up`
