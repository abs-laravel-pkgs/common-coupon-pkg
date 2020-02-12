<?php

namespace Abs\CommonCouponPkg;
use Abs\Basic\Config;
use Abs\CommonCouponPkg\Coupon;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class CouponController extends Controller {

	private $company_id;
	public function __construct() {
		$this->data['theme'] = config('custom.admin_theme');
		$this->company_id = config('custom.company_id');
	}

	public function getCoupons(Request $request) {
		$this->data['coupons'] = Coupon::
			select([
			'coupons.question',
			'coupons.answer',
		])
			->where('coupons.company_id', $this->company_id)
			->orderby('coupons.display_order', 'asc')
			->get()
		;
		$this->data['success'] = true;

		return response()->json($this->data);

	}

	public function getCouponList(Request $request) {
		$coupons = Coupon::withTrashed()
			->leftJoin('configs', 'configs.id', 'coupons.type_id')
			->select([
				'coupons.*',
				'configs.name as type',
				DB::raw('IF(coupons.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('coupons.company_id', Auth::user()->company_id)
		/*->where(function ($query) use ($request) {
				if (!empty($request->question)) {
					$query->where('coupons.question', 'LIKE', '%' . $request->question . '%');
				}
			})*/
			->orderby('coupons.id', 'desc');

		return Datatables::of($coupons)
			->addColumn('code', function ($coupons) {
				$status = $coupons->status == "Active" ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $coupons->code;
			})
			->addColumn('action', function ($coupons) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				$output .= '<a href="#!/common-coupon-pkg/coupon/edit/' . $coupons->id . '" id = "" ><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1_active . '" onmouseout=this.src="' . $img1 . '"></a>
					<a href="javascript:;"  data-toggle="modal" data-target="#coupon-delete-modal" onclick="angular.element(this).scope().deleteCoupon(' . $coupons->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete_active . '" onmouseout=this.src="' . $img_delete . '"></a>
					';
				return $output;
			})
			->make(true);
	}

	public function getCouponFormData(Request $r) {
		$id = $r->id;
		if (!$id) {
			$coupon = new Coupon;
			$action = 'Add';
		} else {
			$coupon = Coupon::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['coupon'] = $coupon;
		$this->data['extras'] = [
			'type_list' => collect(Config::where('config_type_id', 7)->select('id', 'name')->get())->prepend(['name' => 'Select Type', 'id' => '']),
		];
		$this->data['action'] = $action;
		$this->data['theme'];

		return response()->json($this->data);
	}

	public function saveCoupon(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Coupon Code is Required',
				// 'code.unique' => 'Coupon Code is already taken',
				'discount_percentage.required' => 'Discount Value is Required',
				'type_id.required' => 'Type is Required',
			];
			$validator = Validator::make($request->all(), [
				/*'code' => [
					'required:true',
					'unique:coupons,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],*/
				'code' => 'required',
				'discount_percentage' => 'required',
				'type_id' => 'required',
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$coupon = new Coupon;
				$coupon->created_by_id = Auth::user()->id;
				$coupon->created_at = Carbon::now();
				$coupon->updated_at = NULL;
			} else {
				$coupon = Coupon::withTrashed()->find($request->id);
				$coupon->updated_by_id = Auth::user()->id;
				$coupon->updated_at = Carbon::now();
			}
			$coupon->fill($request->all());
			$coupon->company_id = Auth::user()->company_id;
			if ($request->status == 'Inactive') {
				$coupon->deleted_at = Carbon::now();
				$coupon->deleted_by_id = Auth::user()->id;
			} else {
				$coupon->deleted_by_id = NULL;
				$coupon->deleted_at = NULL;
			}
			$coupon->save();

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Coupon Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Coupon Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function deleteCoupon(Request $request) {
		DB::beginTransaction();
		try {
			Coupon::withTrashed()->where('id', $request->id)->forceDelete();

			DB::commit();
			return response()->json(['success' => true, 'message' => 'Coupon Deleted Successfully']);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}
}
