<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    protected $fillable=["number","isUsed", "idUser","idLot","startDate","endDate"];

    public function getUser(){
        return $this->belongsTo(User::class, 'idUser', 'id');
    }

    public function getLot(){
        return $this->belongsTo(Lot::class, 'idLot',);
    }

    public function isValid(){
        $currentTime = Carbon::now();
        $tickerStartDate = Carbon::parse($this->startDate);
        $ticketEndDate = Carbon::parse( $this->endDate );

        if(  $currentTime->greaterThan( $tickerStartDate ) && $currentTime->lessThan( $ticketEndDate) ){
            return true;
        } else {
            return false;
        }
    }


}
