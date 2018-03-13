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

    public function Upload(Request $request)
    {
        if (!self::Auth()) return null;

        $rules = array(
            'files.*' => 'required|image',
        );
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) return null;

        $files = $request->file('files');
        $filespath = array($request->input('image'));
        foreach ($files as $file)
        {
            $filename = uniqid('IMG', true).'.'.$file->getClientOriginalExtension();

            while (file_exists('img/'.$filename))
            {
                $filename = uniqid('IMG', true).'.'.$file->getClientOriginalExtension();
            }
            $file->move('img', $filename);
            $filespath[] = $filename;
        }

        return $filespath;
    }

    public function NewClothes(Request $request)
    {
        if (!self::Auth()) return null;

        $Clothes = $request->input();

        $rules = array(
            'Name' => 'required',
            'DepartmentId' => 'required',
            'Kind' => 'required',
            'Brand' => 'required',
            'Style' => 'required',
            'Price' => 'required',
            'Images.*' => 'required',
            'Color.*' => 'required',
            'Size.*' => 'required',
            'Count.*' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) return null;

        if (!DB::table('Kind')->where('Name', $Clothes['Kind'])->exists())
            DB::table('Kind')->insert([
                'Name' => $Clothes['Kind']
            ]);

        if (!DB::table('Brand')->where('Name', $Clothes['Brand'])->exists())
            DB::table('Brand')->insert([
                'Name' => $Clothes['Brand']
            ]);

        if (!DB::table('Style')->where('Name', $Clothes['Style'])->exists())
            DB::table('Style')->insert([
                'Name' => $Clothes['Style']
            ]);

        foreach ($Clothes['Size'] as $size)
        {
            if (!DB::table('Size')->where('Name', $size)->exists())
                DB::table('Size')->insert([
                    'Name' => $size
                ]);
        }

        foreach ($Clothes['Color'] as $color)
        {
            if (!DB::table('Color')->where('Name', $color)->exists())
                DB::table('Color')->insert([
                    'Name' => $color
                ]);
        }

        $Clothes['Price'] *= 100;

        DB::table('Clothes')->insert([
            'ManagerId' => $_SESSION["ManagerId"],
            'Name' => $Clothes['Name'],
            'Description' => $Clothes['Description'],
            'DepartmentId' => $Clothes['DepartmentId'],
            'KindId' => DB::table('Kind')->where(['Name' => $Clothes['Kind']])->first()->Id,
            'BrandId' => DB::table('Brand')->where('Name' , $Clothes['Brand'])->first()->Id,
            'StyleId' => DB::table('Style')->where('Name' , $Clothes['Style'])->first()->Id,
            'Price' => $Clothes['Price'],
        ]);

        $ClothesId = DB::table('Clothes')->where([
            'ManagerId' => $_SESSION["ManagerId"],
            'Name' => $Clothes['Name'],
            'Description' => $Clothes['Description'],
            'KindId' => DB::table('Kind')->where(['Name' => $Clothes['Kind']])->first()->Id,
            'BrandId' => DB::table('Brand')->where('Name' , $Clothes['Brand'])->first()->Id,
            'StyleId' => DB::table('Style')->where('Name' , $Clothes['Style'])->first()->Id,
            'Price' => $Clothes['Price'],
        ])->first()->Id;

        $rand = array();

        foreach ($Clothes['Count'] as $index => $count)
        {
            do
            {
                $rand[$index] = rand(1000000, 10000000);
            } while(DB::table('ItemOfClothes')->where('Code', $rand[$index])->exists());

            DB::table('ItemOfClothes')->insert([
                'SizeId' => DB::table('Size')->where('Name' , ($Clothes['Size'])[$index])->first()->Id,
                'ColorId' => DB::table('Color')->where('Name' , ($Clothes['Color'])[$index])->first()->Id,
                'Images' => json_encode(($Clothes['Images'])[$index]),
                'Count' => $count,
                'Code' => $rand[$index],
                'ClothesId' => $ClothesId,
            ]);
        }

        event(new ClothesEvent('New clothes!!!'));

        return $rand;
    }
}
