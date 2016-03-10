<?php

namespace JavaScript\Utilities\JavaScript;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class JavaScriptServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('JavaScript', function ($app) {
            $namespace = config('javascript.js_namespace');
            $useJquery = config('javascript.use_jquery_extend');

            return new PHPToJavaScriptTransformer($namespace, $useJquery);
        });

        $this->mergeConfigFrom(
                __DIR__ . '/config/javascript.php', 'javascript'
        );
    }

    /**
     * Publish the plugin configuration.
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/config/javascript.php' => config_path('javascript.php')
        ]);

        AliasLoader::getInstance()->alias(
                'JavaScript', 'JavaScript\Utilities\JavaScript\JavaScriptFacade'
        );
    }

}
