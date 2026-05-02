<?php

declare(strict_types=1);

namespace App\Common\Providers;

use App\Ai\Gpt\Providers\{
    GptInterface,
    Yandex\Async as YandexGptAsync
};
use App\Ai\Operation\Providers\{
    OperationInterface,
    Yandex\Async as YandexOperationAsync
};
use App\Ai\Stt\Providers\{
    SttInterface,
    Yandex\Async as YandexSttAsync
};
use App\Ai\Tts\Providers\{
    TtsInterface,
    Yandex\Tts as YandexTts
};
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

        $provider = config('ai.provider');

        match ($provider) {
            'yandex' => $this->bindYandex(),
            default => throw new \InvalidArgumentException('Invalid AI provider'),
        };
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function bindYandex(): void
    {
        $this->app->bind(GptInterface::class, YandexGptAsync::class);
        $this->app->bind(SttInterface::class, YandexSttAsync::class);
        $this->app->bind(TtsInterface::class, YandexTts::class);
        $this->app->bind(OperationInterface::class, YandexOperationAsync::class);
    }
}
