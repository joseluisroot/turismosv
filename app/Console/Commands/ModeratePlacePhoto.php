<?php
namespace App\Console\Commands;
use App\Models\PlacePhoto;
use Illuminate\Console\Command;
class ModeratePlacePhoto extends Command {
    protected $signature='turismosv:photo-moderate {photo : ID o UUID público} {decision : approve o reject} {--note= : Nota interna de moderación}';
    protected $description='Aprueba o rechaza una fotografía comunitaria pendiente';
    public function handle(): int {
        $decision=$this->argument('decision');if(!in_array($decision,['approve','reject'],true)){$this->error('La decisión debe ser approve o reject.');return self::INVALID;}
        $photo=PlacePhoto::query()->where('id',$this->argument('photo'))->orWhere('public_id',$this->argument('photo'))->first();if(!$photo){$this->error('Fotografía no encontrada.');return self::FAILURE;}
        $photo->update(['status'=>$decision==='approve'?'approved':'rejected','moderated_at'=>now(),'moderation_note'=>$this->option('note')]);$this->info("Fotografía {$photo->id}: {$photo->status}.");return self::SUCCESS;
    }
}
