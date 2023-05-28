# Prueba de implementación de un servidor GraphQL con Symfony 6

API GraphQL para realizar gestión de usuarios y obtención automática de su región mediante la IP.

## Requisitos del sistema

- PHP 8.1
- No es necesaria base de datos

## Ejecutar la aplicación

1- Desde el directorio raíz del proyecto, ejecutar el siguiente comando, para crear los datos de prueba:

```bash      
$ ./bin/init-app
```

Esto creará los directorios "resources/db" y "resources/db_test" en la raiz del proyecto, conteniendo datos de prueba

2- Instalar dependencias con composer:

```bash      
$ composer install
```

3- Para ejecutar el servidor propio que viene con PHP, ejecutar:

```bash
$ php -S localhost:8080
```

El comando anterior ejecuta la prueba en el puerto 8080 de tu `localhost`.

## Ejecutar Unit Tests

Para ejecutar los tests, basta con ejecutar este comando:

```bash
$ vendor/bin/phpunit test
```
O también

```bash
$ vendor/bin/phpunit --testdox  test
```

## Soluciones implementadas

### Actualización asíncrona

Se ha optado por obtener las regiones de los usuarios de manera asíncrona, mediante su IP, utilizando una 
API externa (https://ip-api.com/), la cual provee varios datos dada una dirección IP, entre ellos,
la región.

Para esto se dispone del siguiente comando, ejecutándolo desde la raiz del proyecto:

```bash
$ php bin/console.php app:geolocation:update:users
```

Este comando se encarga de obtener los usuarios que no tienen definida región, recolecta sus IPs,
y en una sola petición BATCH envía el listado de IPS a la API para obtener sus respectivas regiones.

Igualmente, tiene en cuenta los usuarios que ya se les ha establecido su región, pero su IP ha cambiado,
por lo que actualiza nuevamente la región con la nueva IP.

Mientras las IP y las regiones correspondientes de cada usuario se encuentren actualizadas, el comando no realiza ninguna
acción y termina.

En un entorno real, se configuraría un CRON JOB para ejecutar este comando de forma periódica, y así
mantener actualizadas las regiones de los usuarios en todo momento, y sin penalizar el rendimiento
de la aplicación, ni obstaculizando el hilo principal de las consultas a la API GraphQL.

### Actualización síncrona

Como una segunda solución, en el endpoint GET de un User, se verifica que éste tenga región, en caso de
que no la tenga, en ese momento se llama a la API para intentar obtenerla a través de su IP, de manera
síncrona. En caso de éxito se actualiza el usuario con la región en la BD y se devuelve, en caso de 
fallo, se devuelve el usuario igualmente con los datos que tenía originalmente.

Esta solución es válida para el caso de que siempre se requiera que el usuario tenga una región definida
al devolverlo en la API, por lo que, para el caso de que se haga una petición de un usuario al que
en ese instante, no se le ha asignado su región mediante el CRON JOB, ésta se obtendrá de manera síncrona
la primera vez, y persistiendo este dato en BD para las siguientes peticiones.
