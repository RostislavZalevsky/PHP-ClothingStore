<?php

namespace App\Http\Controllers;

use App\Events\ClothesEvent;
use App\Mail\ManagerMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    public function Manager(Request $request)
    {
        if (!self::Auth())
            return view('Manager.authorization');

        return view('Manager.index', [
            'departments' => DB::table('Department')->get(),
            'kinds' => DB::table('Kind')->get(),
            'brands' => DB::table('Brand')->get(),
            'styles' => DB::table('Style')->get(),
            'sizes' => DB::table('Size')->get(),
            'colors' => DB::table('Color')->get()]);
    }

    public function NewManager(Request $request)
    {
        if (!self::Auth() && $_SESSION["ManagerId"] != 1) return;

        DB::table('Manager')->insert([
            'FullName' => $request->input('FullName'),
            'Email' => $request->input('Email'),
            'Phone' => $request->input('Phone'),
            'Password' => '',
            'Salt' => ''
        ]);

        $table = DB::table('Manager')->where('Email', $request->input('Email'))->first();

        $selector = base64_encode(uniqid('', true));
        $token = uniqid('', true);

        DB::table('AuthTokens')->insert([
            'Selector' => $selector,
            'Token' => Hash::make($token),
            'ManagerId' => $table->Id,
            'Expires' => date('Y-m-d\TH:i:s', time() + 60 * 15)
        ]);

        $link = $_SERVER['HTTP_ORIGIN']."/NewManager/VerifyEmail/$selector/".base64_encode($token);
        $content = "Hello $table->FullName,<br/>To confirm your ClothingStore.com, please click the link below:<br/>
        <a href='$link'>Confirm email address</a><br/>
        If you've received this email by mistake, please delete it.";


        Mail::to($table->Email)
            ->queue(new ManagerMail($content,'ROBOT', 'Confirm your Email | CLOTHING STORE'));

        return $request->input('FullName').' '.$request->input('Email').' '.$request->input('Phone');
    }

    public function VerifyEmail($selector, $token)
    {
        DB::table('AuthTokens')
            ->where('Expires', '<', date('Y-m-d\TH:i:s', time()))->delete();

        $AuthToken = DB::table('AuthTokens')->where('Selector', $selector)->first();

        if ($AuthToken != null && Hash::check(base64_decode($token), $AuthToken->Token))
        {
            return view('Manager.newpassword', ['SecurityKey1' => $selector, 'SecurityKey2' => $token]);
        }

        return redirect('');
    }

    public function CreateNewPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $AuthToken = DB::table('AuthTokens')->where('Selector', $request->input('SecurityKey1'))->first();

        if ($AuthToken != null && Hash::check(base64_decode($request->input('SecurityKey2')), $AuthToken->Token))
        {
            $table = DB::table('Manager')->where('Id', $AuthToken->ManagerId)->first();

            if($table->Password == '' && $table->Salt == '')
            {
                $generator = uniqid();
                DB::table('Manager')->where('Id', $table->Id)->update(['Password' => Hash::make($request->input('password_confirmation').$generator), 'Salt' => $generator]);
                DB::table('AuthTokens')->where('Selector', $AuthToken->Selector)->delete();

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
                return redirect('');
            }

            return redirect('');
        }

        return redirect('');
    }
}
