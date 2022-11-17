<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueGains extends Model
{
    use HasFactory;
    protected $table = 'historique_gains';
    protected $fillable=["idTicket","idUser", "takenAt"];
    public function getUser(){
        return  $this->belongsTo(User::class, 'idUser', 'id');
    }

    public function getTicket(){
        return $this->belongsTo(Ticket::class, 'idTicket', 'id');
    }

    public function getReclamation() {

        return $this->hasOne(Reclamation::class,'history_id');
    }

}
