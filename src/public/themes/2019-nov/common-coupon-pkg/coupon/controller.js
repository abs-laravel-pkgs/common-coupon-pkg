app.config(['$routeProvider', function($routeProvider) {

    $routeProvider.
    when('/coupons', {
        template: '<coupons></coupons>',
        title: 'Coupons',
    });
}]);

app.component('coupons', {
    templateUrl: coupon_list_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $location) {
        $scope.loading = true;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        $http({
            url: laravel_routes['getCoupons'],
            method: 'GET',
        }).then(function(response) {
            self.coupons = response.data.coupons;
            $rootScope.loading = false;
        });
        $rootScope.loading = false;
    }
});