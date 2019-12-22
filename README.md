# Features
- SEO-friendly urls
- Redirects for non-localed routes
- No additional parameters inside controllers
- Routes still can be cached
- Localization for the 404 error page 
- Default locale parameter for URL generator 
- Correct locale validation
- LocaleRepository by default use session for saving locale, but can be easily extended.

# Usage

Define your localized routes with ```locale``` macro method like this:
```
Route::locale(function () {
    Route::get('/login', 'LoginController@create')->name('login.create');
});
```

Now you have available ```/{locale}/login``` route in your app. 

For switching locale all you need to do is to set `$locale` with:
```
app()->setLocale($locale)
```
It triggers ```Illuminate\Foundation\Events\LocaleUpdated``` event, which is automatically handled by the package.


# Installation
Register the LocalizationRouterServiceProvider in the ```config/app.php``` as the very **last provider**.
It should be placed after all possible routes because it uses ```Route::fallback()``` to intercept not found routes.
```
'providers' => [
    // Other providers...

    Nevadskiy\LocalizationRouter\LocalizationRouterServiceProvider::class,
]
```

Add locales array to your config/app.php with your supported locales.
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

Put the middleware ```Nevadskiy\LocalizationRouter\Middleware\SetLocaleMiddleware``` to your web middleware group in the ```app/Http/Kernel.php``` file.
```
/**
 * The application's route middleware groups.
 *
 * @var array
 */
protected $middlewareGroups = [
    'web' => [
        // app middlewares...
        \Nevadskiy\LocalizationRouter\Middleware\SetLocaleMiddleware::class,
    ],
];
```

[OPTIONAL] If you want to set your locale before resolving route models (for example, your model use some sort of localization), just override ```$middlewarePriority``` array from base HttpKernel in your ```app/Http/Kernel.php``` file and put the ```LocaleMiddleware``` before ```SubstituteBindings``` like this:
```
/**
 * The priority-sorted list of middleware.
 *
 * Forces non-global middleware to always be in the given order.
 *
 * @var array
 */
protected $middlewarePriority = [
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Auth\Middleware\Authenticate::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Nevadskiy\LocalizationRouter\Middleware\SetLocaleMiddleware::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Illuminate\Auth\Middleware\Authorize::class,
];
```


#### TODO:
- add config for package and extract all depended parameters into it
- add possibility to specify locales list to the specific group of routes (e.g. add additional locales for admin panel)
