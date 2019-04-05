<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use App\Http\Controllers\Controller;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use PragmaRX\Google2FA\Google2FA;

class Laravel2faController extends Controller
{
	public function setup()
	{
		$qr = Laravel2fa::generateQrCode();
		$enabled = Laravel2fa::enabled();
		return view('2fa::setup')->with([
			'user' => Auth::guard(config('2fa.guard'))->user(),
			'qr' => $qr,
			'enabled' => $enabled
		]);
	}

	public function validateSetup(Request $request)
	{
		$code = $request->get(config('2fa.code_input_name'));

		if(!Laravel2fa::validate($code)){
			return back()->withErrors([
				config('2fa.code_input_name') => trans('2fa::base.invalid_code')
			]);
		}

		Laravel2fa::enable();

		if($request->get(config('2fa.remember_input_name'))){
			Laravel2fa::remember();
		}

		return redirect(config('2fa.redirect'));
	}

	public function auth()
	{
		return view('2fa::auth');
	}

	public function validateAuth(Request $request)
	{
		$code = $request->get(config('2fa.code_input_name'));
		if(!Laravel2fa::validate($code)){
			return back()->withErrors([
				config('2fa.code_input_name') => trans('2fa::base.invalid_code')
			]);
		}

		if($request->get(config('2fa.remember_input_name'))){
			Laravel2fa::remember();
		}

		return redirect(config('2fa.redirect'));
	}

	public function disable()
	{
		Laravel2fa::disable();
		return back();
	}
}
