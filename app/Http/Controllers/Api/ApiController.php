<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;

abstract class ApiController extends Controller
{
    use RespondsWithJson;
}
