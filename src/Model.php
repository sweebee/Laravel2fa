<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    protected $table = '2fa';

    public $timestamps = false;

    protected $casts = [
    	'enabled' => 'boolean'
    ];

}
