<?php
namespace App\Console\Commands;
use App\Models\Place;
use App\Models\PlaceQrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class GeneratePlaceQrCode extends Command {
    protected $signature='turismosv:qr {place : Slug del lugar} {--days=365 : Días de vigencia}'; protected $description='Genera un QR físico seguro para un lugar';
    public function handle(): int {
        $place=Place::query()->where('slug',$this->argument('place'))->first(); if(!$place){$this->error('No existe un lugar con ese slug.');return self::FAILURE;}
        $secret=Str::random(48);$publicId=(string)Str::uuid();$days=max(1,(int)$this->option('days'));
        $url=route('qr.show',['publicId'=>$publicId,'secret'=>$secret]);
        $qr=new QrCode(data:$url,size:640,margin:24,foregroundColor:new Color(7,91,94),backgroundColor:new Color(255,255,255));$svg=(new SvgWriter())->write($qr)->getString();
        $code=PlaceQrCode::query()->create(['place_id'=>$place->id,'public_id'=>$publicId,'token_hash'=>hash_hmac('sha256',$secret,(string)config('app.key')),'expires_at'=>now()->addDays($days)]);
        $path='qr-codes/'.$place->slug.'-'.$code->public_id.'.svg';Storage::disk('local')->put($path,$svg);
        $this->info('QR generado para '.$place->name);$this->line('Archivo privado: storage/app/private/'.$path);$this->line('Vigente hasta: '.$code->expires_at->format('Y-m-d'));$this->warn('No publiques la URL como texto; entrega el archivo solo al lugar autorizado.');
        return self::SUCCESS;
    }
}
