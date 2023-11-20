<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Decrypt the Environment File
|--------------------------------------------------------------------------
|
| Check if an encrypted environment file exists, decrypt it, and load the
| decrypted content into the application.
|
*/

$encryptedEnvFile = $app->environmentPath().'/env.encrypted';

if (file_exists($encryptedEnvFile)) {
    $decryptedEnv = file_get_contents($encryptedEnvFile);
    
    // Use the encryption key (replace 'YOUR_ENCRYPTION_KEY' with your actual key)
    $encryptionKey = 'NR6V0yPJM8oGSsaVl16lgmU0pCUy/0bcOLUsXCht5QU=';

    try {
        $decryptedEnv = openssl_decrypt($decryptedEnv, 'AES-256-CBC', $encryptionKey, 0, $encryptionKey);
        file_put_contents($app->environmentPath().'/'.$app->environmentFile(), $decryptedEnv);
    } catch (Throwable $e) {
        // Handle decryption error
        die('Error decrypting environment file.');
    }
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
