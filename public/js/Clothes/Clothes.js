app.controller('Clothes', function ($scope, $http) {
    $scope.Modal = function () {
        $scope.modal = {'display': 'block'}
    };

    $scope.NotModal = function (event) {
        if (event.target == event.currentTarget) {
            $scope.modal = {'display': 'none'}
        }
    };

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

    $scope.GetClothes = function () {
        $http.post('/GetClothes', $scope.Clothes.Selected).then(function (response) {
            // console.log(response.data);
            $scope.Clothes.Options = response.data;
            angular.forEach(response.data.Clothes, function (value, key) {
                for (i = 0; i < value.length; i++)
                    $scope.Clothes.Options.Clothes[key][i].Images = JSON.parse(value[i].Images);
            })
        })
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

});