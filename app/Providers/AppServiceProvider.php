<?php

namespace App\Providers;

use App\Livewire\Concerns\SetsBreadcrumbs;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

use function Livewire\on;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('google', function ($app, array $config) {
            $options = [];

            if (! empty($config['teamDriveId'] ?? null)) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            if (! empty($config['sharedFolderId'] ?? null)) {
                $options['sharedFolderId'] = $config['sharedFolderId'];
            }

            $client = new GoogleClient;
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            $client->setApplicationName(config('app.name'));

            $service = new GoogleDrive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter);
        });

        on('render', function ($component, $view) {
            if (! in_array(SetsBreadcrumbs::class, class_uses_recursive($component))) {
                return;
            }

            $view->layoutData([
                'breadcrumbs' => $component->breadcrumbsForLayout(),
            ]);
        });
    }
}
