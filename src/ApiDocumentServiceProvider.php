<?php


namespace Zning\Apidocument;


use Illuminate\Support\ServiceProvider;
use Zning\Apidocument\commands\ApiDocument;

class ApiDocumentServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('doc.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiDocument::class,
            ]);
        }

        //路由
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/views','doc');

        $this->publishes([
            __DIR__.'/static' => public_path('static'),
        ], 'public');
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'doc');
    }

    /**
     * Set the config path
     *
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/config/doc.php';
    }

}
