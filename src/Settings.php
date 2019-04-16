<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class Settings extends BaseModel
{
    protected $table = '2fa_settings';

    public $timestamps = false;

    protected $casts = [
    	'enabled' => 'boolean'
    ];

	/**
	 * @return string
	 */
	public function remember_token()
    {
	    if(!$this->remember_token) {
		    $this->remember_token = str_replace('-','',((string) Str::uuid()).((string) Str::uuid()));
		    $this->save();
	    }
	    return $this->remember_token;
    }

}
