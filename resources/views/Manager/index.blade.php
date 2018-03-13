@extends('layout')

@section('title', '| Sales manager')

@section('style')
    <link href="css/modal.css" rel="stylesheet" type="text/css">
@endsection

@section('body')
    Manager
    <div ng-controller="NewClothes">
        <button ng-click="Modal()">Add new clothes</button>
        <div ng-style="modal" ng-click="NotModal($event)" class="modal">

            <div class="modal-content">
                <span>New clothes</span>
                <br/>

                <input type="text" name="Name" placeholder="Name of clothes" ng-model="Clothes.Name">
                <br/>
                <textarea placeholder="Description" ng-model="Clothes.Description"></textarea>
                <br/>
                Department
                <select ng-model="Clothes.DepartmentId" ng-init="Clothes.Departments = {{ $departments }}">
                    <option ng-repeat="department in Clothes.Departments" value="@{{department.Id}}">@{{department.Name}}</option>
                </select>
                <br/>

                <input type="text" name="Kind" placeholder="Name of kind" ng-model="Clothes.Kind">
                OR
                <select ng-model="Clothes.Kind" ng-init="Clothes.Kinds = {{ $kinds }}">
                    <option ng-repeat="kind in Clothes.Kinds">@{{kind.Name}}</option>
                </select>
                <br/>

                <input type="text" name="Brand" placeholder="Name of brand" ng-model="Clothes.Brand">
                OR
                <select ng-model="Clothes.Brand" ng-init="Clothes.Brands = {{ $brands }}">
                    <option ng-repeat="brand in Clothes.Brands">@{{brand.Name}}</option>
                </select>
                <br/>

                <input type="text" name="Style" placeholder="Name of style" ng-model="Clothes.Style">
                OR
                <select ng-model="Clothes.Style" ng-init="Clothes.Styles = {{ $styles }}">
                    <option ng-repeat="style in Clothes.Styles">@{{style.Name}}</option>
                </select>
                <br/>

                <table border="1">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Images</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Count</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr ng-repeat="item in Clothes.Count track by $index">
                        <td>@{{$index + 1}}</td>
                        <td>
                            <input type="file" name="Images" accept="image/x-png,image/gif,image/jpeg" placeholder="Images" ng-model="Clothes.Images[$index]" fileread="Clothes.Images[$index]" multiple>
                        </td>
                        <td>
                            <input type="text" name="Color" placeholder="Color of clothes" ng-model="Clothes.Color[$index]">
                            OR
                            <select ng-model="Clothes.Color[$index]" ng-init="Clothes.Colors = {{ $colors }}">
                                <option ng-repeat="color in Clothes.Colors">@{{color.Name}}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="Size" placeholder="Size of clothes" ng-model="Clothes.Size[$index]" >
                            OR
                            <select ng-model="Clothes.Size[$index]" ng-init="Clothes.Sizes = {{ $sizes }}">
                                <option ng-repeat="size in Clothes.Sizes">@{{size.Name}}</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" min="1" placeholder="Count" ng-model="Clothes.Count[$index]">
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">
                            <button ng-click="AddRow()">Add new Row</button>
                            <button ng-click="Duplicate()">Duplicate the last row</button>
                            <button ng-click="DeleteRow()">Delete last Row</button>
                        </td>
                    </tr>
                    </tfoot>
                </table>

                $<input type="number" min="0.01" placeholder="Price" ng-model="Clothes.Price">
                <br/>

                <button ng-click="CreateNewClothes()">Create new clothes</button>
                <button ng-click="NotModal($event)">Cancel</button>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="js/Manager/NewClothesController.js"></script>
@endsection