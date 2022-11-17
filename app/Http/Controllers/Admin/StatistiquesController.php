<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoriqueGains;
use App\Models\Ticket;
use Chartisan\PHP\Chartisan;
use Illuminate\Http\Request;

class StatistiquesController extends Controller
{
  public function getStats(){
      return view("admin.stats.statistiques");
  }
}
