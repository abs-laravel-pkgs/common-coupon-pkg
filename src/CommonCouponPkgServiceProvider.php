<?php

namespace Abs\CommonCouponPkg;

use Illuminate\Support\ServiceProvider;

class CommonCouponPkgServiceProvider extends ServiceProvider {
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		$this->loadRoutesFrom(__DIR__ . '/routes/web.php');
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
		$this->loadViewsFrom(__DIR__ . '/views', 'common-coupon-pkg');
		$this->publishes([
			__DIR__ . '/public' => base_path('public'),
			__DIR__ . '/database/seeds/client' => 'database/seeds',
			__DIR__ . '/config/config.php' => config_path('common-coupon-pkg.php'),
		]);
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
	}
}
