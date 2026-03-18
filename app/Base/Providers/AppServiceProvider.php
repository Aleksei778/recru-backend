<?php

declare(strict_types=1);

namespace App\Base\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Smalot\PdfParser\Parser as PdfParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::unguard();

        $this->app->bind(PdfParser::class, fn() => new PdfParser());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
