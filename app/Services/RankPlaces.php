<?php
namespace App\Services;
use Illuminate\Support\Collection;
class RankPlaces {
    public function handle(Collection $places): Collection {
        if($places->isEmpty())return $places;$rated=$places->whereNotNull('rating_average');$globalMean=(float)($rated->avg(fn($place)=>(float)$place->rating_average)?:3.5);$maxVisits=max(1,(int)$places->max('verified_visits_count'));$priorWeight=10;
        return $places->map(function($place)use($globalMean,$maxVisits,$priorWeight){$reviews=(int)$place->reviews_count;$rating=(float)($place->rating_average?:$globalMean);$bayesian=(($reviews/($reviews+$priorWeight))*$rating)+(($priorWeight/($reviews+$priorWeight))*$globalMean);$ratingPoints=($bayesian/5)*100;$visitPoints=(log(1+(int)$place->verified_visits_count)/log(1+$maxVisits))*100;$reviewConfidence=min($reviews/25,1)*100;$score=($ratingPoints*.50)+((int)$place->verification_score*.25)+($visitPoints*.15)+($reviewConfidence*.10);$place->setAttribute('ranking_score',round($score,1));$place->setAttribute('adjusted_rating',round($bayesian,2));return $place;})->sortByDesc(fn($place)=>sprintf('%06.1f-%03d-%010d',$place->ranking_score,$place->verification_score,$place->verified_visits_count))->values()->each(fn($place,$index)=>$place->setAttribute('ranking_position',$index+1));
    }
}
