<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Laravel2faController extends Controller
{
	/**
	 * Default 2fa setup view/form
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function setup()
	{
		$qr = Laravel2fa::generateQrCode();
		$enabled = Laravel2fa::enabled();

		return view('2fa::setup')->with([
			'user' => Auth::guard(config('2fa.guard', 'web'))->user(),
			'qr' => $qr,
			'enabled' => $enabled
		]);
	}

	/**
	 * Validate the setup request
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function validateSetup(Request $request)
	{
		$code = $request->get(config('2fa.code_input_name'));

		if(!Laravel2fa::validate($code)){
			return back()->withErrors([
				config('2fa.code_input_name') => trans('2fa::base.invalid_code')
			]);
		}

		// Enable 2fa for the authenticated user
		Laravel2fa::enable();

		// If remember is set, remember the current device
		if($request->get(config('2fa.remember_input_name'))){
			Laravel2fa::remember();
		}

        return redirect()->intended();
	}

	/**
	 * return the 2fa authentication view/form
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function auth()
	{
		// If not enabled, redirect to the setup view/form
		if(!Laravel2fa::enabled()){
			return redirect(route('2fa::setup'));
		}

		return view('2fa::auth');
	}

	/**
	 * Validate the 2fa authentication request
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function validateAuth(Request $request)
	{
		$code = $request->get(config('2fa.code_input_name'));

		// Check if the code is valid, otherwise return with an error
		if(!Laravel2fa::validate($code)){
			return back()->withErrors([
				config('2fa.code_input_name') => trans('2fa::base.invalid_code')
			]);
		}

		// If remember is set, remember the current device
		if($request->get(config('2fa.remember_input_name'))){
			Laravel2fa::remember();
		}

        return redirect()->intended();
	}

	/**
	 * Disable 2fa for the authenticated user
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function disable()
	{
		Laravel2fa::disable();
		return back();
	}
}
