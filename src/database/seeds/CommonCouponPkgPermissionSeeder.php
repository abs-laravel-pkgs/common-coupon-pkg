<?php
namespace Abs\CommonCouponPkg\Database\Seeds;

use App\Permission;
use Illuminate\Database\Seeder;

class CommonCouponPkgPermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = [
			//FAQ
			[
				'display_order' => 99,
				'parent' => null,
				'name' => 'coupons',
				'display_name' => 'Coupons',
			],
			[
				'display_order' => 1,
				'parent' => 'coupons',
				'name' => 'add-coupon',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'coupons',
				'name' => 'delete-coupon',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'coupons',
				'name' => 'delete-coupon',
				'display_name' => 'Delete',
			],

		];
		Permission::createFromArrays($permissions);
	}
}