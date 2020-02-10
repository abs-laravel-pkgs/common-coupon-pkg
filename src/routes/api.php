<?php
Route::group(['namespace' => 'Abs\CommonCouponPkg\Api', 'middleware' => ['api']], function () {
	Route::group(['prefix' => 'common-coupon-pkg/api'], function () {
		Route::group(['middleware' => ['auth:api']], function () {
			// Route::get('taxes/get', 'TaxController@getTaxes');
		});
	});
});