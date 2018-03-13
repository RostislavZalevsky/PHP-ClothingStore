@extends('layout')

@section('title', '| Clothing Store')

@section('style')
    <link href="css/modal.css" rel="stylesheet" type="text/css">
    <link href="css/clothes.css" rel="stylesheet" type="text/css">
    <link href="css/stripe.css" rel="stylesheet" type="text/css">
    @if(\App\Http\Controllers\Controller::Auth() == true && $_SESSION["ManagerId"] == 1)
        <link href="css/modal.css" rel="stylesheet" type="text/css">
    @endif
@endsection

@section('body')
    <div>
        <header>
            Clothing Store
            <hr/>
            @if(\App\Http\Controllers\Controller::Auth() && $_SESSION["ManagerId"] == 1)
                <div ng-controller="NewManager">
                    <button ng-click="Modal()">Add new Sales Manager</button>
                    <div ng-style="modal" ng-click="NotModal($event)" class="modal">

                        <div class="modal-content">
                            <span>New Sales Manager</span>
                            <input type="text" name="FullName" placeholder="Fullname" ng-model="Manager.FullName">
                            <input type="email" name="Email" placeholder="Email" ng-model="Manager.Email">
                            <input type="tel" name="Phone" placeholder="Phone number" ng-model="Manager.Phone">
                            <button ng-click="CreateNewManager()">Create new manager</button>
                            <button ng-click="NotModal($event)">Cancel</button>
                        </div>

                    </div>
                </div>
            @endif
            @if(\App\Http\Controllers\Controller::Auth())
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
                            <select ng-model="Clothes.DepartmentId">
                                <option ng-repeat="department in Clothes.Departments" value="@{{department.Id}}">@{{department.Name}}</option>
                            </select>
                            <br/>

                            <input type="text" name="Kind" placeholder="Name of kind" ng-model="Clothes.Kind">
                            OR
                            <select ng-model="Clothes.Kind">
                                <option ng-repeat="kind in Clothes.Kinds">@{{kind.Name}}</option>
                            </select>
                            <br/>

                            <input type="text" name="Brand" placeholder="Name of brand" ng-model="Clothes.Brand">
                            OR
                            <select ng-model="Clothes.Brand">
                                <option ng-repeat="brand in Clothes.Brands">@{{brand.Name}}</option>
                            </select>
                            <br/>

                            <input type="text" name="Style" placeholder="Name of style" ng-model="Clothes.Style">
                            OR
                            <select ng-model="Clothes.Style">
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
                                        <select ng-model="Clothes.Color[$index]">
                                            <option ng-repeat="color in Clothes.Colors">@{{color.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="Size" placeholder="Size of clothes" ng-model="Clothes.Size[$index]" >
                                        OR
                                        <select ng-model="Clothes.Size[$index]">
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
            @endif
        </header>
        <main ng-controller="Clothes">
            <button ng-repeat="item in Clothes.Options.Department track by $index" ng-click="Department(item.Id)">@{{item.Name}}</button>
            <button ng-click="Department()">All</button>
            <hr/>
            <form action="api/charge" method="post" id="payment-form">
                <div class="form-row">
                    <label for="card-element">
                        Credit or debit card
                    </label>
                    <div id="card-element">
                        <!-- A Stripe Element will be inserted here. -->
                    </div>

                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert"></div>
                </div>

                <button>Submit Payment</button>
            </form>

            <div class="menu">
                <div>
                    <b>Kinds</b>
                    <div name="Kind">
                        <div ng-repeat="item in Clothes.Options.Kinds track by $index" ng-click="Kinds(item.Id)">@{{item.Name}}</div>
                    </div>
                    <hr/>
                    <b>Brands</b>
                    <div name="Brand">
                        <div ng-repeat="item in Clothes.Options.Brands track by $index" ng-click="Brands(item.Id)">@{{item.Name}}</div>
                    </div>
                    <hr/>
                    <b>Styles</b>
                    <div name="Style">
                        <div ng-repeat="item in Clothes.Options.Styles track by $index" ng-click="Styles(item.Id)">@{{item.Name}}</div>
                    </div>
                    <hr/>
                    <b>Sizes</b>
                    <div name="Size">
                        <div ng-repeat="item in Clothes.Options.Sizes track by $index" ng-click="Sizes(item.Id)">@{{item.Name}}</div>
                    </div>
                    <hr/>
                    <b>Colors</b>
                    <div name="Color">
                        <div ng-repeat="item in Clothes.Options.Colors track by $index" ng-click="Colors(item.Id)">@{{item.Name}}</div>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="main">
                <div class="flex-container">
                    <div ng-repeat="item in Clothes.Options.Clothes track by $index">
                        <img ng-src="/img/@{{item[0].Images[0]}}">
                        <br/>
                        @{{item[0].Name}}<br/>
                        $@{{item[0].Price / 100}}<br/>
                    </div>
                </div>
                <hr/>
                <div ng-controller="NewManager">
                    <button ng-click="Modal()">Add new Sales Manager</button>
                    <div ng-style="modal" ng-click="NotModal($event)" class="modal">

                        <div class="modal-content">
                            <span>New Sales Manager</span>
                            <input type="text" name="FullName" placeholder="Fullname" ng-model="Manager.FullName">
                            <input type="email" name="Email" placeholder="Email" ng-model="Manager.Email">
                            <input type="tel" name="Phone" placeholder="Phone number" ng-model="Manager.Phone">
                            <button ng-click="CreateNewManager()">Create new manager</button>
                            <button ng-click="NotModal($event)">Cancel</button>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('script')
    <script src="/js/Clothes/stripe.js"></script>
    <script src="/js/Clothes/Clothes.js"></script>
    @if(\App\Http\Controllers\Controller::Auth() && $_SESSION["ManagerId"] == 1)
        <script src="js/Manager/NewManagerController.js"></script>
    @endif
    @if(\App\Http\Controllers\Controller::Auth())
        <script src="js/Manager/NewClothesController.js"></script>
    @endif
@endsection