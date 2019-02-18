<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $data
     * @param bool $die
     * @param bool $backtrace
     */
    public function printPre($data, bool $die = false, bool $backtrace = false)
    {
        $output = \var_dump($data);

        echo "<pre>";
        \print_r($output);
        echo "</pre>";

        if ($die === true) {
            die();
        }

        if ($backtrace === true) {
            echo "<br /><br /><h3>Backtrace</h3>";
            \var_dump(\debug_backtrace());
        }
    }

    /**
     * @param array $array
     * @param $key
     * @param $value
     * @return array
     */
    public function array_push_assoc(array $array, $key, $value): array
    {
        $array[$key] = $value;

        return $array;
    }

}
