<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function usersList(){
        $users=User::all();
        return view('admin.users.user_list', compact('users'));
    }


    public function exportCsv()
    {

        $fileName = 'users.csv';
        $users = User::latest()->get();



        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('name','email',"telephone","address",'additional_address','ville');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['name'] =  $user->name ?? ' ';
                $row['email'] = $user->email ?? ' ';
                $row['telephone'] = $user->telephone ?? ' ';
                $row['address'] = $user->address ?? ' ';
                $row['additional_address'] = $user->additional_address ?? ' ';
                $row['ville'] = $user->ville ?? ' ';

                fputcsv($file, array($row['name'], $row['email'], $row['telephone'], $row['address'], $row['additional_address'],$row['ville'] ));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function myProfil( $id_user=null ){
        $user=User::find( $id_user );

        if( empty($user) ){
            abort('403','L\utilisateur dont vous essayer d\'avoir les informations n\'existe pas');
        } else {

            return  view('admin.users.my_profil',compact('user') );
        }
    }

    public function updateMyProfil(Request $request, $id_user ){

        $user=User::find( $id_user );

        if( empty($user) ){
            abort('403','L\utilisateur dont vous essayer de modififer  les informations n\'existe pas');
        } else {

            $validateData=$request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255',
                //'password' => 'string|min:6|confirmed',
                'telephone'=>'string',
                'role'=>'string'
            ]);

            $user->name= $request->input('name');
            $user->telephone= $request->input('telephone');
            $user->email= $request->input('email');

            if( $request->filled('role') ) {

                if(Auth::user()->role == env('ADMIN_ROLE') && Auth::user()->id != $user->id){
                    $user->role = $request->input('role');

                } else {
                    return back()->with('errorNotification','Vous n\'êtes pas autorisé à modifier votre rôle ou le rôle
                                        d\'un quelconque autre utilisateur ou votre propre role');
                }
            }

            $user->save();
            return back()->with('successNotification','Profil mis à jour avec succès.');
        }
    }

    public function deleteUser( $id_user ){
        if( Auth::user()->role != env('ADMIN_ROLE') ){
            return back()->with('errorNotification','Vous n\'êtes pas autorisé à supprimer cet utilisateur.');
        } else {
            $user=User::find( $id_user );
            if( empty($user) ){
                abort('403','L\utilisateur que vous essayez de supprimer  n\'existe pas');
            } else {
                $user->delete();

                return back()->with('successNotification','Utilisateur supprimé avec succès');
            }
        }

    }

    public function listeLots($user_id) {
      $user =    User::find($user_id);
      if(isset($user)) {
          return view('admin.users.lots', compact('user'));
      }else {
          return back()->with('errorNotification','Aucun utilisateur  n\'existe  avec cet identifiant.');

      }

    }
}
