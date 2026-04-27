<?php

declare(strict_types=1);

namespace App\Common\Providers;

use App\Ai\Gpt\Providers\GptInterface as GptSyncInterface;
use App\Ai\Gpt\Contracts\{AsyncInterface as GptAsyncInterface};
use App\Ai\Gpt\Providers\Yandex as YandexGpt;
use App\Ai\Operation\Providers\OperationInterface as OperationAsyncInterface;
use App\Ai\Operation\Providers\Yandex as YandexOperation;
use App\Ai\Stt\Contracts\{AsyncInterface as SttAsyncInterface};
use App\Ai\Stt\Providers\SttInterface as SttSyncInterface;
use App\Ai\Stt\Providers\Yandex\Async as YandexStt;
use App\Ai\Tts\Providers\{TtsInterface as TtsSyncInterface};
use App\Ai\Tts\Providers\Yandex\Tts as YandexTts;
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
