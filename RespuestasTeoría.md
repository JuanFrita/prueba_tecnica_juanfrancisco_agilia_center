He usado Laravel para ejemplificar mis respuestas.

### Imagina que quieres tener una función global que almacene en BBDD las acciones llevadas a cabo por los usuarios (Por ejemplo: Usuario x crea película y) ¿cómo lo harías?

Supondré que estas acciones se guardan en una tabla de la base de datos user_actions. Se podría definir un GlobalObserver para todos los modelos de la aplicación. En la funcion boot del ServiceProvider o en un ServiceProvider especifico se aplicaría el método ::observe para cada modelo.

```php
private static function boot()
{
    $MODELS = [
        Movie::class,
        ... mas modelos aqui
    ];

    foreach ($MODELS as $MODEL) {
        $MODEL::observe(GlobalObserver::class);
    }
}
```

Los Observers pueden escuchar los eventos de updated, created, deleted, forceDeleted... Cuando se disparen, se obtendrá el usuario autenticado via el facade Auth o la funcion global user() y se registrará en la tabla user_actions junto la accion realizada y el registro de base de datos afectado. Al ser una función por cada evento, permite incluso asignar diferentes tipos y variar la funcionalidad por cada uno. Por ejemplo, se podrian indicar o resaltar mas las acciones que implican un forceDelete.

### Quieres gestionar una excepción de un caso especial, por ejemplo, siempre que se lanza la excepción ModelNotFound quieres que la API devuelva un mensaje especial, ¿cómo lo implementamos?

En el fichero boostrap/app.php se puede definir como se quiere renderizar una excepción para la aplicacion de Laravel. De esta forma se podria definir un mensaje especial siempre que se lance la excepción ModelNotFound.

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
         $exceptions->render(function (ModelNotFound $e, Request $request) {
             return response()->json([
                'message' => 'Mensaje especial personalizado.'
            ], 404);
        });
    })->create();
```
De esta forma al cliente le llega el mensaje personalizado si se lanza la exepción ModelNotFound.

### ¿Tiene sentido un endpoint de logout en este tipo de API?

En este caso como he optado por una solución con Laravel Sanctum si que es útil, ya que permite al servidor invalidar el token generado por el usuario, y que no pueda ser reutilizado. En caso de utilizar JWTs como el token tiene caducidad, no es estrictamente necesario. Al ser stateless toda la informacion se puede obtener del token JWT y no es necesario mantener sesiones/estados en el servidor.

### ¿Cómo implementarías medidas de seguridad adicionales (avoid cross-site scripting attacks, enforces secure (HTTP over SSL/TLS) connections, ...) en esta API?

Para mejorar la seguridad se podría:
- Definir un Content Security policy (CSP) en el fichero .htaccess de la aplicacion de laravel. Esto es un ejemplo de un htaccess de un blog que comencé tesuplementamos.com. Esto se usa para restringir las fuentes de las que se puedan inyectar los tags scripts y recursos en una página web.

```html
<IfModule mod_headers.c>
    Header set Content-Security-Policy-Report-Only "default-src 'self'; script-src 'self' www.google-analytics.com www.googletagmanager.com; img-src 'self' www.googleadservices.com; font-src 'self' fonts.googleapis.com fonts.gstatic.com fonts.bunny.net; style-src 'self' fonts.googleapis.com fonts.bunny.net; connect-src 'self' https://region1.google-analytics.com https://www.google-analytics.com;"
</IfModule>
```

- Cookies HTTP Only. En esta API no se utilizan pero si que es conveniente usar Cookies HTTP Only para que estén encriptadas. Sobre todo si pueden modificar o estan relacionadas con información sensible del usuario.

- Tambien se puede definir reglas en el htaccess para que siempre se redirija de una conexión http a una https

```html
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
A nivel de aplicacion de Laravel se podría forzar a que el esquema para las urls sea https:

```php
if($this->app->environment('production')) {
    \URL::forceScheme('https');
}
```

- Rate Limiting: Laravel tiene mecanismos para aplicar rate limiting. Se pueden definir límites y bloqueos en base el referer, body o ip de la petición. Un ejemplo podría ser limitar por ip consumir un recurso API un maximo de 100 veces por minuto (lo cual es bastante restrictivo). Si el usuario lo excede es bloqueado 5 minutos. Si excede el límite 5 veces es bloqueado permanentemente.
