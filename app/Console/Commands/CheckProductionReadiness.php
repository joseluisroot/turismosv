<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckProductionReadiness extends Command
{
    protected $signature = 'turismosv:production-check';

    protected $description = 'Comprueba que TurismoSV tenga una configuracion minima segura para produccion';

    public function handle(): int
    {
        $checks = [
            $this->check(PHP_VERSION_ID >= 80200, 'PHP 8.2 o superior', PHP_VERSION, true),
            $this->check(extension_loaded('pdo_mysql'), 'Extension pdo_mysql', 'Necesaria para MySQL', true),
            $this->check(app()->environment('production'), 'APP_ENV=production', app()->environment(), true),
            $this->check(! config('app.debug'), 'APP_DEBUG=false', config('app.debug') ? 'debug activo' : 'correcto', true),
            $this->check(Str::startsWith((string) config('app.url'), 'https://'), 'APP_URL usa HTTPS', (string) config('app.url'), true),
            $this->check(filled(config('app.key')), 'APP_KEY configurada', filled(config('app.key')) ? 'presente' : 'ausente', true),
            $this->check(config('database.default') === 'mysql', 'Conexion MySQL', (string) config('database.default'), true),
            $this->check(config('mail.default') !== 'log', 'Correo real configurado', (string) config('mail.default'), true),
            $this->check(is_writable(storage_path()), 'Directorio storage escribible', storage_path(), true),
            $this->check(is_writable(base_path('bootstrap/cache')), 'bootstrap/cache escribible', base_path('bootstrap/cache'), true),
            $this->check(file_exists(public_path('build/manifest.json')), 'Recursos frontend compilados', public_path('build/manifest.json'), true),
            $this->check($this->legalIdentityIsComplete(), 'Identidad legal completa', 'LEGAL_OWNER_NAME, LEGAL_TAX_ID, LEGAL_CONTACT_EMAIL y LEGAL_ADDRESS', true),
            $this->check((bool) config('session.secure'), 'Cookie de sesion solo HTTPS', 'SESSION_SECURE_COOKIE=true', true),
            $this->check(config('queue.default') === 'sync', 'Cola compatible con hosting compartido', (string) config('queue.default'), false),
            $this->check($this->socialProvidersAreComplete(), 'Acceso social configurado', 'Google y Facebook', false),
            $this->check(file_exists(public_path('storage')), 'Almacenamiento publico enlazado', public_path('storage'), false),
        ];

        $this->table(
            ['Estado', 'Comprobacion', 'Detalle'],
            array_map(fn (array $check) => [$check['passed'] ? 'OK' : ($check['critical'] ? 'ERROR' : 'AVISO'), $check['label'], $check['detail']], $checks),
        );

        $errors = collect($checks)->where('critical', true)->where('passed', false)->count();
        $warnings = collect($checks)->where('critical', false)->where('passed', false)->count();

        if ($errors > 0) {
            $this->error("Produccion no esta lista: {$errors} error(es) y {$warnings} aviso(s).");

            return self::FAILURE;
        }

        $this->info("Configuracion minima aprobada con {$warnings} aviso(s).");

        return self::SUCCESS;
    }

    private function check(bool $passed, string $label, string $detail, bool $critical): array
    {
        return compact('passed', 'label', 'detail', 'critical');
    }

    private function legalIdentityIsComplete(): bool
    {
        return collect(['owner_name', 'tax_id', 'contact_email', 'address'])
            ->every(fn (string $field) => filled(config("legal.{$field}")));
    }

    private function socialProvidersAreComplete(): bool
    {
        return collect(['google', 'facebook'])->every(
            fn (string $provider) => filled(config("services.{$provider}.client_id"))
                && filled(config("services.{$provider}.client_secret")),
        );
    }
}
