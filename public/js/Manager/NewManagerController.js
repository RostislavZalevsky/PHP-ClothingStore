app.controller('NewManager', function ($scope, $http) {
    $scope.Modal = function () {
        $scope.modal = {'display': 'block'}
    }

    $scope.NotModal = function (event) {
        if (event.target == event.currentTarget) {
            $scope.modal = {'display': 'none'}
        }
    }

    $scope.CreateNewManager = function () {
        $http.post('/NewManager', $scope.Manager)//{ FullName: $scope.Manager.FullName, Email: $scope.Manager.Email, Phone: $scope.Manager.Phone }
            .then(function successCallback(response) {
                $scope.Manager = null;
                $scope.modal = {'display': 'none'}
            }, function errorCallback(response) {
                alert(response.data.message);
            });
    }
});