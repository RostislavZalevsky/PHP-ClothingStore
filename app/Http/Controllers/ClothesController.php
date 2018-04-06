<?php

namespace App\Http\Controllers;
use App\Events\ClothesEvent;
use App\Mail\ManagerMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ClothesController extends Controller
{
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

    public function EditItem(Request $request)
    {
        if (!self::Auth()) return null;
        $ItemOfClothes = $request->input();

        foreach ($ItemOfClothes as $item)
        {
            if (!DB::table('Size')->where('Name', $item['Size'])->exists())
            {
                DB::table('Size')->insert([
                    'Name' => $item['Size']
                ]);
            }

            if (!DB::table('Color')->where('Name', $item['Color'])->exists())
            {
                DB::table('Color')->insert([
                    'Name' => $item['Color']
                ]);
            }
        }
//
//        foreach ($ItemOfClothes as $item)
//            DB::table('ItemOfClothes')->where('ClothesId', $item['Id'])->update(['Count' => 0]);
//
        foreach ($ItemOfClothes as $item)
            DB::table('ItemOfClothes')
                ->where('ItemOfClothes.Id', $item['ItemOfClothesId'])
                ->join('Clothes', 'ItemOfClothes.ClothesId', '=', 'Clothes.Id')
                ->join('Size', 'ItemOfClothes.SizeId', '=', 'Size.Id')
                ->join('Color', 'ItemOfClothes.ColorId', '=', 'Color.Id')
                ->update([
                    'Clothes.Name' => $item['Name'],
                    'Clothes.Description' => $item['Description'],
                    'Clothes.Price' => $item['Price'] *= 100,
                    'Color.Name' => $item['Color'],
                    'Size.Name' => $item['Size'],
                    'ItemOfClothes.Images' => json_encode($item['Images']),
                    'ItemOfClothes.Count' => $item['Count'],
            ]);

        event(new ClothesEvent('Edited clothes.'));

        return ($ItemOfClothes[0])['Id'];
    }

    public function NewItem(Request $request)
    {
        if (!self::Auth()) return null;

        if (!DB::table('Size')->where('Name', $request->input('Size'))->exists())
            DB::table('Size')->insert([
                'Name' => $request->input('Size')
            ]);

        if (!DB::table('Color')->where('Name', $request->input('Color'))->exists())
            DB::table('Color')->insert([
                'Name' => $request->input('Color')
            ]);

        $rand = 0;
        do
        {
            $rand = rand(1000000, 10000000);
        } while(DB::table('ItemOfClothes')->where('Code', $rand)->exists());

        DB::table('ItemOfClothes')->insert([
            'SizeId' => DB::table('Size')->where('Name' , $request->input('Size'))->first()->Id,
            'ColorId' => DB::table('Color')->where('Name' , $request->input('Color'))->first()->Id,
            'Images' => json_encode($request->input('Images')),
            'Count' => $request->input('Count'),
            'Code' => $rand,
            'ClothesId' => $request->input('Id'),
        ]);

        event(new ClothesEvent('New item.'));

        return;
    }

    public function DeleteItem(Request $request)
    {
        if (!self::Auth()) return null;

        return $request->input();
    }
}
