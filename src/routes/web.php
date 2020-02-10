<?php

Route::group(['namespace' => 'Abs\CommonCouponPkg', 'middleware' => ['web', 'auth'], 'prefix' => 'common-coupon-pkg'], function () {
	//FAQs
	Route::get('/coupons/get-list', 'CouponController@getCouponList')->name('getCouponList');
	Route::get('/coupon/get-form-data', 'CouponController@getCouponFormData')->name('getCouponFormData');
	Route::post('/coupon/save', 'CouponController@saveCoupon')->name('saveCoupon');
	Route::get('/coupon/delete/{id}', 'CouponController@deleteCoupon')->name('deleteCoupon');
});

Route::group(['namespace' => 'Abs\CommonCouponPkg', 'middleware' => ['web'], 'prefix' => 'common-coupon-pkg'], function () {
	//FAQs
	Route::get('/coupons/get', 'CouponController@getCoupons')->name('getCoupons');
});
