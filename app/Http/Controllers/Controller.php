<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function Auth()
    {
        if(!isset($_COOKIE["Manager"])) return false;

        DB::table('AuthTokens')
            ->where('Expires', '<', date('Y-m-d\TH:i:s', time()))->delete();

        $values = json_decode($_COOKIE["Manager"]);
        $selector = $values[0];
        $authenticator = $values[1];

        $AuthToken = DB::table('AuthTokens')->where('Selector', $selector)->first();

        if ($AuthToken != null && Hash::check(base64_decode($authenticator), $AuthToken->Token))
        {
            $_SESSION["ManagerId"] = $AuthToken->ManagerId;
            setcookie("Manager", json_encode($values),time() + 60 * 60 * 36);

            DB::table('AuthTokens')
                ->where('Selector', $selector)
                ->update(['Expires' => date('Y-m-d\TH:i:s', time() + 60 * 60 * 36)]);

            return true;
        }

        return false;
    }

    public function Authorization(Request $request)
    {
        DB::table('AuthTokens')
            ->where('Expires', '<', date('Y-m-d\TH:i:s', time()))->delete();

        $Email = $request->input('Email');
        $Password = $request->input('Password');

//        $generator = uniqid();
//        DB::table('Manager')
//            ->where('Id', 1)
//            ->update(['Password' => Hash::make('CS#GG1998'.$generator), 'Salt' => $generator]);
        $table = DB::table('Manager')->where('Email', $Email)->first();

        if($table != null && $Email == $table->Email && Hash::check($Password.$table->Salt, $table->Password))
        {
            $selector = base64_encode(random_bytes(9));
            $authenticator = random_bytes(33);

            $values = array($selector, base64_encode($authenticator));

            setcookie("Manager", json_encode($values),time() + 60 * 60 * 36);

            DB::table('AuthTokens')->insert([
                'Selector' => $selector,
                'Token' => Hash::make($authenticator),
                'ManagerId' => $table->Id,
                'Expires' => date('Y-m-d\TH:i:s', time() + 60 * 60 * 36)
            ]);
           // return redirect($table->Id == 1 ? 'Admin' : 'Manager');
        }
        return redirect('/');
    }

    public function Logout(Request $request)
    {
        if(self::Auth())
        {
            $values = json_decode($_COOKIE["Manager"]);
            $selector = $values[0];
            $authenticator = $values[1];
            setcookie("Manager", '', time());


            DB::table('AuthTokens')
                ->where('Selector', $selector)->delete();
        }

        return redirect('/');
    }
}
