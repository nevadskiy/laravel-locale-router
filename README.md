# Features
- SEO-friendly urls
- Redirects for non-localed routes
- No additional parameters inside controllers
- Routes still can be cached
- Default locale parameter for URL generator 
- Correct locale validation

# Installation

Register the LocalizationRouterServiceProvider in the config/app.php
```
'providers' => [
    /*
     * Package Service Providers...
     */
    Nevadskiy\LocalizationRouter\LocalizationRouterServiceProvider::class,
]
```

Add 'locales' array to your config/app.php with supported locales
```
    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    'locales' => ['en', 'es, 'it', 'de', 'ru'],
```

Define your localized routes with locale method
```
Route::locale(function () {
    Route::get('/login', 'LoginController@create')->name('login.create');
});
```

If you want to have auto redirect for routes with no locale set (e.g. https://localhost:8080/login -> https://localhost:8080/en/login),
add the following code to your app/Exceptions/Handler.php to your render method:
```
use Nevadskiy\LocalizationRouter\Exceptions\NotFoundByWrongLocaleException;
use Nevadskiy\LocalizationRouter\Exceptions\NotFoundHandler;
...

public function render($request, Exception $exception)
{
    $exception = app(NotFoundHandler::class)->prepareException($request, $exception);

    if ($exception instanceof NotFoundByWrongLocaleException) {
        return $exception->redirect();
    }

    return parent::render($request, $exception);
}
```