<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Cache;
use chillerlan\QRCode\QRCode;

class Laravel2fa {

	/**
	 * Generate a secret key
	 *
	 * @return string
	 */
	public static function generateSecret(BaseModel $model = null)
	{
		$model = self::getModel($model);

		if(!$settings = self::getSettings($model)){
			$settings = new Model();
			$settings->model_type = get_class($model);
			$settings->model_id = $model->id;
			$settings->secret = encrypt((new Google2FA())->generateSecretKey());
			$settings->save();
		}

		return decrypt($settings->secret);
	}

	public static function generateQrCode(BaseModel $model = null)
	{
		$model = self::getModel($model);

		$secret = self::generateSecret($model);

		$url = (new Google2FA())->getQRCodeUrl(
			config('app.name'),
			$model->email,
			$secret
		);

		return (new QRCode())->render($url);
	}

	public static function enable(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);
		$settings->enabled = true;
		$settings->save();
	}

	public static function disable(BaseModel $model = null)
	{
		$model = self::getModel($model);
		Model::where('model_type', get_class($model))->where('model_id', $model->id)->delete();
	}

	public static function enabled(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);
		return $settings->enabled ?? false;
	}

	/**
	 * Validate the user's input with his secret key
	 *
	 * @param $secret
	 * @param $code
	 *
	 * @return bool
	 */
	public static function validate($code, BaseModel $model = null)
	{
		$code = str_replace(' ','', trim($code));
		// If the code is already cached, its invalid
		if(!Cache::missing('2fa_' . $code)){
			return false;
		}

		$model = self::getModel($model);

		$settings = self::getSettings($model);

		// Cache the code for 5 minutes so it wont work again
		Cache::put('2fa_' . $code, true, 60 * 5);

		// Verify the code
		try {
			$valid = (bool) ( new Google2FA() )->verifyKey( decrypt($settings->secret), $code, 1 );
		} catch (\Exception $e){
			$valid = false;
		}

		if($valid) {
			session( [ '2fa_authenticated' => true ] );
		}

		return $valid;
	}

	/**
	 * Check if authenticated
	 *
	 * @return bool
	 */
	public static function authenticated(BaseModel $model = null)
	{
		$model = self::getModel($model);

		$settings = self::getSettings($model);

		if(!$settings || !$settings->enabled){
			return true;
		}

		return (bool)session('2fa_authenticated');
	}

	private static function getModel($model)
	{
		return $model ?? Auth::guard(config('2fa.guard'))->user();
	}

	private static function getSettings(BaseModel $model)
	{
		return Model::where('model_type', get_class($model))->where('model_id', $model->id)->first();
	}

}
