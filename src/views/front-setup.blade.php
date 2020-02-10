@if(config('COMMON_COUPON_PKG.DEV'))
    <?php $common_coupon_pkg_prefix = '/packages/abs/common-coupon-pkg/src';?>
@else
    <?php $common_coupon_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var coupon_list_template_url = "{{asset($common_coupon_pkg_prefix.'/public/themes/'.$theme.'/common-coupon-pkg/coupon/coupons.html')}}";
</script>
<script type="text/javascript" src="{{asset($common_coupon_pkg_prefix.'/public/themes/'.$theme.'/common-coupon-pkg/coupon/controller.js')}}"></script>
