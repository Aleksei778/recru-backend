<?php

declare(strict_types=1);

namespace App\Common\Providers;

use App\Ai\Gpt\Contracts\{AsyncInterface as GptAsyncInterface, SyncInterface as GptSyncInterface};
use App\Ai\Tts\Contracts\{SyncInterface as TtsSyncInterface};
use App\Ai\Operation\Contracts\AsyncInterface as OperationAsyncInterface;
use App\Ai\Stt\Contracts\{SyncInterface as SttSyncInterface, AsyncInterface as SttAsyncInterface};
use App\Ai\Gpt\Providers\Yandex as YandexGpt;
use App\Ai\Stt\Providers\Yandex as YandexStt;
use App\Ai\Tts\Providers\Yandex as YandexTts;
use App\Ai\Operation\Providers\Yandex as YandexOperation;
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
        $this->app->bind(GptAsyncInterface::class, YandexGpt::class);
        $this->app->bind(GptSyncInterface::class, YandexGpt::class);
        $this->app->bind(SttAsyncInterface::class, YandexStt::class);
        $this->app->bind(SttSyncInterface::class, YandexStt::class);
        $this->app->bind(OperationAsyncInterface::class, YandexOperation::class);
        $this->app->bind(TtsSyncInterface::class, YandexTts::class);
    }
}
