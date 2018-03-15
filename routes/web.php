<?php
use \Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::post('Authorization', 'Controller@Authorization');
Route::get('Logout', 'Controller@Logout');

Route::get('Manager', 'ManagerController@Manager');

Route::post('NewManager', 'ManagerController@NewManager');
Route::get('NewManager/VerifyEmail/{selector}/{token}', 'ManagerController@VerifyEmail');
Route::post('NewPasswordManager', 'ManagerController@CreateNewPassword');

Route::post('Upload', 'ManagerController@Upload');
Route::post('NewClothes', 'ManagerController@NewClothes');

Route::post('/GetClothes', function (\Illuminate\Http\Request $request){
    $ItemOfClothes = DB::table('ItemOfClothes')
        ->where('Count', '>', 0)
        ->join('Size', 'ItemOfClothes.SizeId', '=', 'Size.Id')
        ->join('Color', 'ItemOfClothes.ColorId', '=', 'Color.Id')
        ->join('Clothes', 'ItemOfClothes.ClothesId', '=', 'Clothes.Id')
        ->join('Department', 'Clothes.DepartmentId', '=', 'Department.Id')
        ->join('Kind', 'Clothes.KindId', '=', 'Kind.Id')
        ->join('Brand', 'Clothes.BrandId', '=', 'Brand.Id')
        ->join('Style', 'Clothes.StyleId', '=', 'Style.Id')
        ;

    $result = array("Department" => [], "Kinds" => [], "Brands" => [], "Styles" => [], "Sizes" => [], "Colors" => [], "Clothes" => []);

    foreach (collect($ItemOfClothes->select('Department.Id', 'Department.Name')->get())->groupBy('Id') as $department)
        $result["Department"][] = $department[0];

    if ($request->input("Department") != null)
        $ItemOfClothes->where('Department.Id', $request->input("Department"));

    foreach (collect($ItemOfClothes->select('Kind.Id', 'Kind.Name')->get())->groupBy('Id') as $kind)
        $result["Kinds"][] = $kind[0];

    foreach (collect($ItemOfClothes->select('Brand.Id', 'Brand.Name')->get())->groupBy('Id') as $brand)
        $result["Brands"][] = $brand[0];

    foreach (collect($ItemOfClothes->select('Style.Id', 'Style.Name')->get())->groupBy('Id') as $style)
        $result["Styles"][] = $style[0];

    foreach (collect($ItemOfClothes->select('Size.Id', 'Size.Name')->get())->groupBy('Id') as $size)
        $result["Sizes"][] = $size[0];

    foreach (collect($ItemOfClothes->select('Color.Id', 'Color.Name')->get())->groupBy('Id') as $color)
        $result["Colors"][] = $color[0];

    if (count($request->input("Kinds")) > 0)
        $ItemOfClothes->whereIn('Kind.Id', $request->input("Kinds"));

    if (count($request->input("Brands")) > 0)
        $ItemOfClothes->whereIn('Brand.Id', $request->input("Brands"));

    if (count($request->input("Styles")) > 0)
        $ItemOfClothes->whereIn('Style.Id', $request->input("Styles"));

    if (count($request->input("Sizes")) > 0)
        $ItemOfClothes->whereIn('Size.Id', $request->input("Sizes"));

    if (count($request->input("Colors")) > 0)
        $ItemOfClothes->whereIn('Color.Id', $request->input("Colors"));

    $result["Clothes"] = collect($ItemOfClothes->select(array(
        'ItemOfClothes.Id as ItemOfClothesId', 'Clothes.Id', 'Clothes.Name',
        'Clothes.Description',
        'Clothes.KindId', 'Kind.Name as Kind',
        'Clothes.BrandId', 'Brand.Name as Brand',
        'Clothes.StyleId', 'Style.Name as Style',
        'SizeId', 'Size.Name as Size',
        'ColorId', 'Color.Name as Color',
        'Images', 'Count', 'Code', 'Clothes.Price'))->get())->groupBy('Id');

    return $result;
});

Route::post('GetAllClothes', function (\Illuminate\Http\Request $request) {
    $result = array(
        "Departments" => null,
        "Kinds" => null,
        "Brands" => null,
        "Styles" => null,
        "Sizes" => null,
        "Colors" => null);
    $result["Departments"] = DB::table('Department')->get();
    $result["Kinds"] = DB::table('Kind')->get();
    $result["Brands"] = DB::table('Brand')->get();
    $result["Styles"] = DB::table('Style')->get();
    $result["Sizes"] = DB::table('Size')->get();
    $result["Colors"] = DB::table('Color')->get();

    return $result;
});

Route::post('EditItem', 'ManagerController@EditItem');
Route::post('NewItem', 'ManagerController@NewItem');
Route::post('DeleteItem', 'ManagerController@DeleteItem');

Route::get('event', function () {
    event(new \App\Events\ClothesEvent('Hello!'));
});

Route::get('listen', function () {
    return view('listen');
});
