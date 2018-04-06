@extends('layout')

@section('title', '| Clothing Store')

@section('style')
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/clothes.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/stripe.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('body')
    <div>
        <header>
            <div style="width: 100%; font-size: 60px; width: 100%; text-align: center;">
            <span>Clothing Store</span>
            </div>
            <hr/>
            <!--#region Manager  -->
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
                            <div style="font-size: 40px; width: 100%; font-weight: bold; text-align: center">
                               <span>
                                New clothes
                               </span>
                            </div>
                            <br/>

                            <input style="width: 100%; font-size: 25px;" type="text" name="Name" placeholder="Name of clothes" ng-model="Clothes.Name">
                            <br/>
                            <textarea style="width: 100%; font-size: 25px;" placeholder="Description" ng-model="Clothes.Description"></textarea>
                            <br/>
                            <br/>
                            Department
                            <select style="width: 100%; font-size: 25px;" ng-model="Clothes.DepartmentId">
                                <option ng-repeat="department in Clothes.Departments" value="@{{department.Id}}">@{{department.Name}}</option>
                            </select>
                            <br/>
                            <br/>

                            Kind<br/>
                            <input style="width: 46.5%; font-size: 25px;" type="text" name="Kind" placeholder="Name of kind" ng-model="Clothes.Kind">
                            OR
                            <select  style="width: 50%; font-size: 25px;" ng-model="Clothes.Kind">
                                <option ng-repeat="kind in Clothes.Kinds">@{{kind.Name}}</option>
                            </select>
                            <br/>
                            <br/>

                            Brand<br/>
                            <input style="width: 46.5%; font-size: 25px;" type="text" name="Brand" placeholder="Name of brand" ng-model="Clothes.Brand">
                            OR
                            <select style="width: 50%; font-size: 25px;" ng-model="Clothes.Brand">
                                <option ng-repeat="brand in Clothes.Brands">@{{brand.Name}}</option>
                            </select>
                            <br/>
                            <br/>

                            Style<br/>
                            <input style="width: 46.5%; font-size: 25px;" type="text" name="Style" placeholder="Name of style" ng-model="Clothes.Style">
                            OR
                            <select style="width: 50%; font-size: 25px;"  ng-model="Clothes.Style">
                                <option ng-repeat="style in Clothes.Styles">@{{style.Name}}</option>
                            </select>
                            <br/>
                            <br/>

                            <table style="text-align: center; width: 100%; border-collapse: collapse; border: 2px solid #222;" border="1">
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
                                    <td >
                                        <input style="font-size: 20px; width: 300px" type="file" name="Images" accept="image/x-png,image/gif,image/jpeg" placeholder="Images" ng-model="Clothes.Images[$index]" fileread="Clothes.Images[$index]" multiple>
                                    </td>
                                    <td>
                                        <input style="font-size: 20px;" type="text" name="Color" placeholder="Color of clothes" ng-model="Clothes.Color[$index]">
                                        OR
                                        <select style="font-size: 20px;" ng-model="Clothes.Color[$index]">
                                            <option ng-repeat="color in Clothes.Colors">@{{color.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input style="font-size: 20px;" type="text" name="Size" placeholder="Size of clothes" ng-model="Clothes.Size[$index]" >
                                        OR
                                        <select style="font-size: 20px;" ng-model="Clothes.Size[$index]">
                                            <option ng-repeat="size in Clothes.Sizes">@{{size.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input style="width: 95%; text-align: center; font-size: 20px;" type="number" min="1" placeholder="Count" ng-model="Clothes.Count[$index]">
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <button style="font-size: 20px; background: #222; color: white; width: 33%; text-align: center; font-size: 20px;" ng-click="AddRow()">Add new Row</button>
                                        <button style="font-size: 20px; background: #222; color: white; width: 33%; text-align: center; font-size: 20px;" ng-click="Duplicate()">Duplicate the last row</button>
                                        <button style="font-size: 20px; background: #222; color: white; width: 33%; text-align: center; font-size: 20px;" ng-click="DeleteRow()">Delete last Row</button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            <br/>

                            <div style="font-size: 25px; text-align: center">
                                $<input style="width: 98%; font-size: 25px;"  type="number" min="0.01" placeholder="Price" ng-model="Clothes.Price">
                            </div>
                            <br/>
                            <div style="width: 100%; text-align: center">
                                <button style="font-size: 20px; background: #222; color: white;" ng-click="CreateNewClothes()">Create new clothes</button>
                                <button style="font-size: 20px; background: #222; color: white;"  ng-click="NotModal($event)">Cancel</button>
                            </div>

                        </div>

                    </div>
                </div>
        @endif
            <!--#endregion -->
        </header>
        <main ng-controller="Clothes">
            <div style="width: 100%; text-align: center">
                <button style="height: 100px; font-size: 50px" ng-repeat="item in Clothes.Options.Department track by $index" ng-click="Department(item.Id)">@{{item.Name}}</button>
                <button style="height: 100px; width: 200px; font-size: 50px" ng-click="Department()">All</button>
                @if(!\App\Http\Controllers\Controller::Auth())
                    <button style="height: 100px; width: 200px; font-size: 50px" ng-click="ModalShoppingBasket()"><img src="img/shopping-bag.png" width="50">@{{Shopping.Basket.length}}</button>
                    <div ng-style="modalShoppingBasket" ng-click="NotModalShoppingBasket($event)" class="modal">
                        <div class="modal-content">
                            <table>
                                <thead>
                                <tr>
                                    <th>Clothes</th>
                                    <th>Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in Shopping.Basket track by $index">
                                    <td>
                                        <img style="max-width: 100px; max-height: 100px" ng-src="/img/@{{item.Images[0]}}"> @{{ item.Name }}<br/>
                                        @{{ item.Color }}
                                        @{{ item.Size }}
                                        <button ng-click="RemoveItem($index)">Delete</button>
                                    </td>
                                    <td>$@{{ item.Price / 100 * item.Count }}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2">
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
                                        </form></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            </div>


            <hr/>
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
                    <div ng-repeat="item in Clothes.Options.Clothes track by $index" @if(\App\Http\Controllers\Controller::Auth()) ng-click="ManagerItemEdit(item)" @else ng-click="GetItem(item)" @endif>
                        <img ng-src="/img/@{{item[0].Images[0]}}">
                        <br/>
                        <span style="font-size: 25px;">@{{item[0].Name}}</span><br/>
                        <span style="font-size: 30px"><b>$@{{item[0].Price / 100}} </b></span><br/>
                    </div>
                </div>
                <hr/>
            </div>



            {{--<button ng-click="Modal()">Add new Sales Manager</button>--}}
            @if(\App\Http\Controllers\Controller::Auth())
                <div>
                    <button ng-click="Modal()">Edit clothes</button>
                    <div ng-style="modal" ng-click="NotModal($event)" class="modal">

                        <div class="modal-content">
                            <span>Edit clothes</span>
                            <input type="text" name="Name" placeholder="Name of clothes" ng-model="ManagerItem[0].Name">
                            <br/>
                            <textarea placeholder="Description" ng-model="ManagerItem[0].Description"></textarea>
                            <br/>

                            Kind: @{{ ManagerItem[0].Kind }}<br/>
                            Brand: @{{ ManagerItem[0].Brand }}<br/>
                            Style: @{{ ManagerItem[0].Style }}<br/>

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
                                <tr ng-repeat="item in ManagerItem track by $index">
                                    <td>@{{$index + 1}}</td>
                                    <td>
                                        {{--@{{item.Images}}--}}
                                        <input type="file" name="Images" accept="image/x-png,image/gif,image/jpeg" placeholder="Images" ng-model="item.Images" fileread="item.Images" multiple>
                                    </td>
                                    <td>
                                        <input type="text" name="Color" placeholder="Color of clothes" ng-model="item.Color">
                                        OR
                                        <select ng-model="item.Color">
                                            <option ng-repeat="color in Clothes.Options.Colors">@{{color.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="Size" placeholder="Size of clothes" ng-model="item.Size" >
                                        OR
                                        <select ng-model="item.Size">
                                            <option ng-repeat="size in Clothes.Options.Sizes">@{{size.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" min="1" placeholder="Count" ng-model="item.Count">
                                    </td>
                                    <td>
                                        <button ng-click="RemoveRow($index)">Delete the row</button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td>
                                        #
                                    </td>
                                    <td>
                                        <input type="file" name="Images" accept="image/x-png,image/gif,image/jpeg" placeholder="Images" ng-model="NewClothes.Images" fileread="NewClothes.Images" multiple>
                                    </td>
                                    <td>
                                        <input type="text" name="Color" placeholder="Color of clothes" ng-model="NewClothes.Color">
                                        OR
                                        <select ng-model="NewClothes.Color">
                                            <option ng-repeat="color in Clothes.Options.Colors">@{{color.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="Size" placeholder="Size of clothes" ng-model="NewClothes.Size" >
                                        OR
                                        <select ng-model="NewClothes.Size">
                                            <option ng-repeat="size in Clothes.Options.Sizes">@{{size.Name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" min="1" placeholder="Count" ng-model="NewClothes.Count">
                                    </td>
                                    <td>
                                        <button ng-click="AddRow()">Create clothes</button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>

                            $<input type="number" min="0.01" placeholder="Price" ng-model="ManagerItem[0].Price">
                            <br/>

                            <button ng-click="SaveClothes()">Save clothes</button>
                            <button ng-click="NotModal($event); GetClothes();">Cancel</button>
                        </div>

                    </div>
                </div>
            @else
                <div ng-style="modal" ng-click="NotModal($event)" class="modal">
                    <div class="modal-content">
                        <span>@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Name }}</span><br/>
                        Images: <img style="max-width: 500px; max-height: 500px" ng-src="/img/@{{Shopping.SelectItem[Shopping.SelectItem.Index].Images[Shopping.SelectItem.IndexImage]}}">
                        <div>
                            <img style="max-width: 50px; max-height: 50px"
                                 ng-repeat="item in Shopping.SelectItem[Shopping.SelectItem.Index].Images track by $index"
                                 ng-src="/img/@{{item}}" ng-click="ItemChangeImage($index)">
                        </div>
                        Description: <span>@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Description }}</span><br/>
                        Kind: <span>@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Kind }}</span><br/>
                        Brand: <span>@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Brand }}</span><br/>
                        Style: <span>@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Style }}</span><br/>
                        Select the color and size:
                        <select ng-model="Shopping.SelectItem.Index" ng-change="Shopping.SelectItem.IndexImage = 0; Shopping.SelectItem.SelectCount = 1">
                            <option ng-repeat="item in Shopping.SelectItem track by $index" value="@{{$index}}">
                                @{{item.Color}}
                                @{{item.Size}}
                            </option>
                        </select>
                        Count: <input type="number" min="1" max="@{{Shopping.SelectItem[Shopping.SelectItem.Index].Count}}" ng-model="Shopping.SelectItem.SelectCount">
                        Price: <span>$@{{ Shopping.SelectItem[Shopping.SelectItem.Index].Price/100 * Shopping.SelectItem.SelectCount }}</span><br/>
                        <button ng-click="AddShoppingBag()">Add to Cart</button>
                        @{{Shopping.SelectItem.Index}}
                    </div>
                </div>
            @endif
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