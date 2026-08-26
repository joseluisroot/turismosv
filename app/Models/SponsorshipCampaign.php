<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SponsorshipCampaign extends Model {protected $fillable=['place_id','sponsor_name','title','description','destination_url','starts_on','ends_on','status','impressions','clicks','created_by'];protected function casts():array{return ['starts_on'=>'date','ends_on'=>'date','impressions'=>'integer','clicks'=>'integer'];}public function place():BelongsTo{return $this->belongsTo(Place::class);}public function scopeActive($query){return $query->where('status','active')->whereDate('starts_on','<=',today())->whereDate('ends_on','>=',today());}}
