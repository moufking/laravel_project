<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;


    protected $fillable = ['user_id',"lieu_livraison","phone","statut_reclamation", "history_id"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user() {

        return $this->belongsTo(User::class, 'user_id');
    }

    public function view_information($reclamation_id)
    {

        $reclamation = Reclamation::where('id', $reclamation_id)->first();
        return view('admin.reclamation.list_reclamation', compact("reclamation"));

    }

    public function historique() {
        return $this->belongsTo(HistoriqueGains::class, 'history_id','id');
    }


}
