
# Índice

1. [Agradecimientos](#agradecimientos)

2. [Instalación (Terminal)](#instalación-terminal)

3. [Levantar app (Terminal)](#levantar-app-terminal)

4. [Documentación API](#documentación-api)
   - [Rutas API](#rutas-api)
   - [Cuerpo petición create/update](#cuerpo-petición-createupdate)
   - [Respuesta HTTP MovieResource](#respuesta-http-movieresource)

5. [Implementación de la solución](#implementación-de-la-solución)

6. [Librerías](#librerías)

7. [Autenticación](#autenticación)

8. [Base de datos](#base-de-datos)

9. [API](#api)
   - [Rutas](#rutas-1)
   - [Validación de los datos](#validación-de-los-datos)
   - [Repository pattern](#repository-pattern)
   - [Controlador](#controlador)
   - [Features Test](#features-test)

10. [Panel de administración y filtros](#panel-de-administración-y-filtros)

# Agradecimientos

Primero de todo agradecimientos a Agilia Center por darme la oportunidad de resolver esta prueba técnica. Se ha optado por utilizar Laravel para el desarrollo de la solución.

Las respuestas a las preguntas de teoría se encuentran en RespuestasTeoría.md

# Instalación (Terminal)
- composer install
- npm install
- php artisan migrate:fresh --seed
- (Opcional) php artisan db:seed --class=DatabaseFakeSeeder

Si se lanza el último comando se obtendrá acceso directo al panel de administración, con los siguientes mails:

- fake1@fakemail.com
- fake2@fakemail.com
- fake3@fakemail.com
- fake4@fakemail.com
- fake5@fakemail.com

La contraseña es siempre 1234. Cada usuario tiene asociado 10 películas con 2 categorías aleatorias.

# Levantar app (Terminal)
- php artisan serve
- npm run dev

Clica [aquí](http://localhost:8000/) para acceder a la aplicación una vez esté levantada.

# Documentación API

## Ejecución tests

./vendor/bin/phpunit tests/Feature/MovieApiTest

## Rutas api

| Método HTTP | URI                 | Acción    | Nombre de la Ruta   |
|-------------|---------------------|----------|---------------------|
| GET         | /movies             | index    | movies.index        |
| POST        | /movies             | store    | movies.store        |
| PUT/PATCH   | /movies/{movieId}   | update   | movies.update       |
| DELETE      | /movies/{movieId}   | destroy  | movies.destroy      |

## Cuerpo petición create/update:

- **name**
  - Type: `string`
  - Required: `true`
  - Maximum Length: `255`

- **release_year**
  - Type: `string`
  - Required: `true`
  - Format: `date`
  - Pattern: `^(19|20)\d{2}$`

- **cover**
  - Type: `string`
  - Format: `url`
  - Nullable: `true`

- **user_id**
  - Type: `integer`
  - Required: `true`
  - Exists: `{ table: "users", column: "id" }`

- **categories**
  - Type: `array`
  - Required: `true`
  - Minimum Items: `1`
  - Maximum Items: `4`
  - Items:
    - Type: `integer`
    - Required: `true`
    - Exists: `{ table: "categories", column: "id" }`

## Respuesta HTTP MovieResource
- **id**
  - Type: `integer`

- **name**
  - Type: `string`

- **cover**
  - Type: `string`
  - Format: `url`

- **categories**
  - Type: `array`
  - Items:
    - Type: `object`

- **created_at**
  - Type: `string`
  - Format: `date-time`


# Implementación de la solución

## Librerías

Me he decantado por un setup con JetStream, utilizando Livewire para la reactividad de la página. Esto es debido a que JetStream ofrece toda la autenticación y registro de usuarios. Además que ofrece una plantilla para poder montar paneles de administración, lo cuál ha sido muy útil para montar un mini front end para la visualización y filtrado de películas.

## Autenticación

Como se ha comentado previamente JetStream monta la base para un registro y autenticación de los usuarios. Las rutas se han establecido en web.php ya que el acceso al panel es en esta misma aplicación. Se aplica el middleware de Laravel Sanctum que es una libreria ligera de autenticación de usuarios muy enfocada en SPAs. En caso de que el front end estuviera alojado en otro servidor se podrian mover api.php para hacer una autenticación basada en JWTs. 

La protección para modificar, eliminar y crear películas que sean unicamente del usuario, se realiza en los correspondientes objetos Requests especificos para cada operación, en el método authorize. De esta forma se encapsula por cada operación y es escalable. Por ejemplo en un futuro se podria añadir roles, y que un admin sí tenga permiso para modificar cualquier recurso.

## Base de datos

Respecto la base de dato se han definido las migraciones, modelos, seeders y factories correspondientes, con los comentarios por columna.

Cada pelicula pertenece a un único usuario, y las peliculas pueden tener varias categorías. En el caso de eliminar una categoria no está automatizado el borrado de la tabla asociativa con peliculas debido a que una categoria puede tener varias peliculas de muchos usuarios. En caso de que se elimine un usuario si que está automatizado que se borren sus peliculas directamente.

He asumido que la portada es la url del fichero, de esta forma se podria guardar en almacenamientos externos como S3. 

Además se ha configurado la tabla de películas para soportar borrado lógico (soft delete).

Como se habrá observado en las instrucciones de instalación, se ha definido un fakeseeder de la base de datos. Considero que es útil para poder hacer pruebas sobre el front más rápidamente, además de ser un recurso útil para demostraciones.

## API

### Rutas

La api se ha construido usando el comando que genera automaticamente un Controller con todas las operaciones CRUD, que genera los nombres convencionales de cada operación. De esta forma solo basta indicar Route::resource('movies', MovieController::class) en web.php, y todas las rutas quedan registradas.

### Validación de los datos

Como ya se ha adelantado en la autenticación se ha definido un StoreMovieRequest, UpdateMovieRequest y DestroyMovieRequest. He hecho que el update request herede las reglas del store request, asi por el momento queda centralizado. He delegado toda la responsabilidad de validación a la entrada de la request en los controladores, de esta forma el resto del código queda mas limpio.

### Repository pattern

Es la capa intermedia para acceder y modificar los datos de la aplicación. He definido un MovieRepositoryInterface y su correspondiente MovieEloquentRepository. El patron repositorio es ideal para escalar la aplicación. Además por lo general en Laravel los modelos pueden acabar con demasiada lógica, entre relaciones, operaciones con los atributos... De esta forma se aisla la parte de consulta y modificación de datos a otra clase. Un punto muy positivo es que permite recuperar instancias del Modelo desde otras fuentes de datos que no sean la base de datos con la que se conecta Eloquent.

### Controlador

El MovieController utiliza el Service Container de laravel para resolver directamente la dependencia del MovieRepositoryInterface. Esto permite intercambiar la implementacion de la interfaz para toda la aplicación de forna centralizada. Respecto las respuestas HTTP se gestionan tanto como si es una petición normal, como en caso de que la petición espere un JSON, para reusabilidad de las funciones del controlador. 

En caso de que la respuesta espera JSON he utilizado Resources. Son clases especiales de laravel para convertir la instancia de un modelo en una respuesta HTTP. La parte positiva es que sigue el esquema json y los codigos estandar de respuestas, (201 para recurso creado etc.) Tambien permiten añadir meta información y sirven para ocultar/filtrar/modificar la información del modelo en la respuesta HTTP al cliente.

### Features Test

He desarrollado tests de integración para probar la API definida. Sobre todo los he enfocado para probar la autenticacion y restricciones establecidas. Usan sqlite en memoria para mejorar el rendimiento, y por cada test se hace la migracion y seed de la base de datos.

- Un usuario solo puede crear peliculas bajo su id
- Un usuario solo puede mofiicar peliculas creadas por el.
- El minimo de categorias es 1 y el maximo es 4.

Sería conveniente también tener los correspondientes tests unitarios del MovieEloquentRepository.

## Panel de administración y filtros

De la parte de front he modificado levemente la página de inicio. He añadido al menú una pestaña para acceder al listado de peliculas de un usuario. Se ha utilizado un componente de LiveWire (MovieIndex) para poder mostrar el listado de películas con paginación y poder tener filtros reactivos por Nombre y Categoria.

Para los filtros he definido clases de filtros que modifican la query() original del eloquent builder. De esta forma el metodo getMoviesByCriteria recibe una colección de los criterios que se quieren aplicar. Es un enfoque practico por que escala muy bien a medida se quieren añadir mas filtros ya que sigue la inyección de dependencias. Además permite reutilizar los filtros para diferentes modelos y consultas.