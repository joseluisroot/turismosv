<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FounderInterestRequest extends Model {protected $fillable=['public_id','business_name','contact_name','email','phone','relationship_role','department','website','message','status','privacy_accepted_at','assigned_to','contacted_at','admin_notes'];protected function casts():array{return ['privacy_accepted_at'=>'datetime','contacted_at'=>'datetime'];}public function assignee():BelongsTo{return $this->belongsTo(User::class,'assigned_to');}}
