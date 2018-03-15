app.controller('Clothes', function ($scope, $http, $q) {
    $scope.Clothes = {
        Selected: {
            Department: null,
            Kinds: [],
            Brands: [],
            Styles: [],
            Sizes: [],
            Colors: []
        },
        Options: {
            Department: [],
            Kinds: [],
            Brands: [],
            Styles: [],
            Sizes: [],
            Colors: [],
            Clothes: []
        }
    };

    $scope.Shopping = {
        Basket: [],
        SelectItem: {
            Index: 0,
            SelectCount: 1,
            IndexImage: 0
        }
    };

    $scope.ManagerItem = {};

    //#region Cookie

    $scope.SetCookie = function (cname, cvalue){
        var d = new Date();
        d.setTime(d.getTime() + (60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    };

    $scope.GetCookie = function (cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    };

//#endregion

    $scope.Shopping.Basket = $scope.GetCookie('ShoppingBasket') != "" ? JSON.parse($scope.GetCookie('ShoppingBasket')) : [];

    $scope.GetClothes = function () {
        $http.post('/GetClothes', $scope.Clothes.Selected).then(function (response) {
            $scope.Clothes.Options = response.data;
            angular.forEach(response.data.Clothes, function (value, key) {
                for (i = 0; i < value.length; i++)
                    $scope.Clothes.Options.Clothes[key][i].Images = JSON.parse(value[i].Images);
            })
        }, function (reason) { console.log(reason); })
    };
    $scope.GetClothes();

    const clothesEvent = new Vue({
        created() {
            Echo.channel('clothesChannel')
                .listen('ClothesEvent', (e) => {
                $scope.GetClothes();
            console.log(e.message);
        });
        }
    });

    //#region Filter
    $scope.Department = function (id) {
        $http.post('/GetClothes', {Department: id})
            .then(function (response) {
                $scope.Clothes.Options = response.data;

                angular.forEach(response.data.Clothes, function (value, key) {
                    for (i = 0; i < value.length; i++)
                        $scope.Clothes.Options.Clothes[key][i].Images = JSON.parse(value[i].Images);
                });

                $scope.Clothes.Selected = {
                    Department: id,
                    Kinds: [],
                    Brands: [],
                    Styles: [],
                    Sizes: [],
                    Colors: []
                }
            });
    };
    $scope.Kinds = function (id) {
        index = $scope.Clothes.Selected.Kinds.indexOf(id);
        if(index < 0) $scope.Clothes.Selected.Kinds.push(id);
        else $scope.Clothes.Selected.Kinds.splice(index, 1);
        $scope.GetClothes();
    };
    $scope.Brands = function (id) {
        index = $scope.Clothes.Selected.Brands.indexOf(id);
        if(index < 0) $scope.Clothes.Selected.Brands.push(id);
        else $scope.Clothes.Selected.Brands.splice(index, 1);
        $scope.GetClothes();
    };
    $scope.Styles = function (id) {
        index = $scope.Clothes.Selected.Styles.indexOf(id);
        if(index < 0) $scope.Clothes.Selected.Styles.push(id);
        else $scope.Clothes.Selected.Styles.splice(index, 1);
        $scope.GetClothes();
    };
    $scope.Sizes = function (id) {
        index = $scope.Clothes.Selected.Sizes.indexOf(id);
        if(index < 0) $scope.Clothes.Selected.Sizes.push(id);
        else $scope.Clothes.Selected.Sizes.splice(index, 1);
        $scope.GetClothes();
    };
    $scope.Colors = function (id) {
        index = $scope.Clothes.Selected.Colors.indexOf(id);
        if(index < 0) $scope.Clothes.Selected.Colors.push(id);
        else $scope.Clothes.Selected.Colors.splice(index, 1);
        $scope.GetClothes();
    };
//#endregion

    //#region Modal
    $scope.Modal = function () {
        $scope.modal = {'display': 'block'}
    };

    $scope.NotModal = function (event) {
        if (event.target == event.currentTarget) {
            $scope.modal = {'display': 'none'}
        }
    };
//#endregion
    //#region ModalShoppingBasket
    $scope.ModalShoppingBasket = function () {
        $scope.modalShoppingBasket = {'display': 'block'}
    };

    $scope.NotModalShoppingBasket = function (event) {
        if (event.target == event.currentTarget) {
            $scope.modalShoppingBasket = {'display': 'none'}
        }
    };
//#endregion
    //#region ModalManagerItem
    $scope.ModalManagerItem = function () {
        $scope.modalManagerItem = {'display': 'block'}
    };

    $scope.NotModalManagerItem = function (event) {
        if (event.target == event.currentTarget) {
            $scope.modalManagerItem = {'display': 'none'}
        }
    };
//#endregion

    $scope.GetItem = function (item) {
        $scope.Modal();

        $scope.Shopping.SelectItem = item;
        $scope.Shopping.SelectItem.Index = 0;
        $scope.Shopping.SelectItem.SelectCount = 1;
        $scope.Shopping.SelectItem.IndexImage = 0;
    };
    $scope.AddShoppingBag = function () {
        item = $scope.Shopping.SelectItem[$scope.Shopping.SelectItem.Index];
        item.Count= $scope.Shopping.SelectItem.SelectCount;

        $scope.modal = {'display': 'none'};
        console.log($scope.GetCookie('ShoppingBasket'));

        if($scope.Shopping.Basket.length == 0) {
            $scope.Shopping.Basket.push(item);
        }
        else {
            for (i = 0; i < $scope.Shopping.Basket.length; i++) {
                if ($scope.Shopping.Basket[i].ItemOfClothesId === item.ItemOfClothesId)
                {
                    alert("You re-added to shopping bag!");
                    $scope.Shopping.Basket[i] = {};
                    $scope.Shopping.Basket[i] = item;
                    $scope.GetClothes();
                    $scope.SetCookie('ShoppingBasket', JSON.stringify($scope.Shopping.Basket));
                    return;
                }
                 if (i === $scope.Shopping.Basket.length - 1) {
                     $scope.Shopping.Basket.push(item);
                     $scope.GetClothes();
                     $scope.SetCookie('ShoppingBasket', JSON.stringify($scope.Shopping.Basket));
                     return;
                 }
            }
        }
    };

    $scope.ItemChangeImage = function (index) {
        $scope.Clothes.SelectItem.IndexImage = index;
    };

    $scope.Buy = function (item) {

    };

    $scope.RemoveItem = function (index) {
        $scope.Shopping.Basket.splice(index, 1);
        $scope.SetCookie('ShoppingBasket', JSON.stringify($scope.Shopping.Basket));
    };

    $scope.ManagerItemEdit = function (item) {
        $scope.Modal();
        $scope.ManagerItem = item;

        for (i = 0; i < $scope.ManagerItem.length; i++)
        {
            $scope.ManagerItem[i].Price = parseFloat($scope.ManagerItem[i].Price / 100);
        }
    };

    $scope.SaveClothes = function() {
        for (i = 0; i < $scope.ManagerItem.length; i++)
        {
           $scope.ManagerItem[i].Price = parseFloat($scope.ManagerItem[i].Price.toFixed(2));
        }

        var promises = [];

        for(image = 0; image < $scope.ManagerItem.length; image++)
        {
            var fd = new FormData();
            for (file = 0; file < ($scope.ManagerItem[image].Images).length; file++)
                fd.append("files[" + file + "]", ($scope.ManagerItem[image].Images)[file]);
            fd.append('image', image);

            var promise = $http.post('/Upload', fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function (response) {
                if(response.data == "") {
                    //alert("Error uploading file");
                    return;
                }

                var data = response.data;
                ($scope.ManagerItem[data[0]].Images) = [];

                for (i = 1; i < data.length; i++)
                    ($scope.ManagerItem[data[0]].Images).push(data[i]);
            }, function ()
            {
                //alert("Error uploading file");
            });

            promises.push(promise);
        }
        console.log($scope.ManagerItem);

        $q.all(promises).then(function () {
            $http.post('/EditItem', $scope.ManagerItem)
                .then(function successCallback(response) {
                    $scope.modal = {'display': 'none'}
                }, function errorCallback(error) {
                    console.log(error);
                    alert('Error creating clothes');
                });
        });
    };

    $scope.AddRow = function () {
        console.log($scope.NewClothes.Images.length);

        var promises = [];

        // for(image = 0; image < $scope.NewClothes.Images.length; image++)
        // {
        image = 0;
            var fd = new FormData();
            for (file = 0; file < ($scope.NewClothes.Images).length; file++)
                fd.append("files[" + file + "]", ($scope.NewClothes.Images)[file]);
            fd.append('image', image);

            var promise = $http.post('/Upload', fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function (response) {
                if(response.data == "") {
                    alert("Error uploading file");
                    return;
                }

                var data = response.data;
                ($scope.NewClothes.Images) = [];

                for (i = 1; i < data.length; i++)
                    ($scope.NewClothes.Images).push(data[i]);

                $scope.NewClothes.Id = $scope.ManagerItem[0].Id;

                $http.post('/NewItem', $scope.NewClothes)
                    .then(function successCallback(response) {
                        $scope.ManagerItem.push($scope.NewClothes);
                        $scope.NewClothes = null;
                        console.log(response);
                    }, function errorCallback(error) {
                        console.log(error);
                        alert('Error creating clothes');
                    });
            }, function (res)
            {
                alert("Error uploading file");
            });

            promises.push(promise);
        // }
    };

    $scope.RemoveRow = function (index) {
        $scope.ManagerItem.splice(index, 1);
    };
});