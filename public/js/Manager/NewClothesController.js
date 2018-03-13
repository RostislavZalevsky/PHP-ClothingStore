app.directive("fileread", [function () {
    return {
        scope: {
            fileread: "="
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                scope.$apply(function () {
                    scope.fileread = changeEvent.target.files;
                });
            });
        }
    }
}])
    .controller('NewClothes', function ($scope, $http, $q) {
        $scope.Clothes = {
            Name: "",
            Description: "",
            DepartmentId: '1',
            Departments: [{}],
            Kinds: [{}],
            Brands: [{}],
            Styles: [{}],
            Images: [null],
            Color: [null],
            Colors: [{}],
            Size: [null],
            Sizes: [{}],
            Count: [1]
        };

        $scope.SetClothes = function () {
            $http.post('GetAllClothes', null).then(function (response) {
                $scope.Clothes.Departments = response.data.Departments;
                $scope.Clothes.Kinds = response.data.Kinds;
                $scope.Clothes.Brands = response.data.Brands;
                $scope.Clothes.Styles = response.data.Styles;
                $scope.Clothes.Colors = response.data.Colors;
                $scope.Clothes.Sizes = response.data.Sizes;
            });
        };
        $scope.SetClothes();

        const clothesEvent = new Vue({
            created() {
                Echo.channel('clothesChannel')
                    .listen('ClothesEvent', (e) => {
                    $scope.SetClothes();
                    console.log(e.message);
                });
            }
        });

        $scope.Modal = function () {
            $scope.modal = {'display': 'block'}
        };

        $scope.NotModal = function (event) {
            if (event.target == event.currentTarget) {
                $scope.modal = {'display': 'none'}
            }
        };

        $scope.AddRow = function () {
            $scope.Clothes.Images.push(null);
            $scope.Clothes.Color.push(null);
            $scope.Clothes.Size.push(null);
            $scope.Clothes.Count.push(1);
        };

        $scope.Duplicate = function () {
            $scope.Clothes.Images.push($scope.Clothes.Images[$scope.Clothes.Images.length-1]);
            $scope.Clothes.Color.push($scope.Clothes.Color[$scope.Clothes.Color.length-1]);
            $scope.Clothes.Size.push($scope.Clothes.Size[$scope.Clothes.Size.length-1]);
            $scope.Clothes.Count.push($scope.Clothes.Count[$scope.Clothes.Count.length-1]);
        };

        $scope.DeleteRow = function () {
            $scope.Clothes.Images.pop();
            $scope.Clothes.Color.pop();
            $scope.Clothes.Size.pop();
            $scope.Clothes.Count.pop();
        };
        
        $scope.CreateNewClothes = function () {
            $scope.Clothes.Price = parseFloat($scope.Clothes.Price.toFixed(2));
            var promises = [];

            for(image = 0; image < $scope.Clothes.Images.length; image++)
            {
                var fd = new FormData();
                for (file = 0; file < ($scope.Clothes.Images[image]).length; file++)
                    fd.append("files[" + file + "]", ($scope.Clothes.Images[image])[file]);
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
                    ($scope.Clothes.Images[data[0]]) = [];

                    for (i = 1; i < data.length; i++)
                        ($scope.Clothes.Images[data[0]]).push(data[i]);
                }, function ()
                {
                    alert("Error uploading file");
                });

                promises.push(promise);
            }

            $q.all(promises).then(function () {
                $http.post('/NewClothes', $scope.Clothes)
                    .then(function successCallback(response) {
                        console.log(response.data);

                        $scope.Clothes.Name = "";
                        $scope.Clothes.Description = "";
                        $scope.Clothes.DepartmentId; 1;
                        $scope.Clothes.Images = [];
                        $scope.Clothes.Color = [];
                        $scope.Clothes.Size = [];
                        $scope.Clothes.Count = [];

                        $scope.DeleteRow();
                        $scope.AddRow();
                    }, function errorCallback() {
                        alert('Error creating clothes');
                    });
            });
        };
    });