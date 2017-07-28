<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Experiments;
use Auth;

class ApiController extends Controller
{
    //
    public function __construct()
    {

    }

    /**
     * Return full path of filename.
     *
     * @param   string  $filename
     * @return  string
     */
    private function getImagePath($filename) {
        $protocol = request()->secure() ? 'https://' : 'http://';
        return $protocol.$_SERVER['SERVER_NAME'].'/'.env('UPLOAD_DIR').'/'.$filename;
    }

    /**
     * Check experiment rules to see if its valid to run.
     *
     * @param   string  $str1      Variable name.
     * @param   string  $str2      Value to check against.
     * @param   string  $operator  Comparison operator.
     * @return  bool
     */
    private function checkRule($str1, $str2, $operator): bool {
        $valid = 0;
        switch ($operator) {
            case 'contain':
                $valid = strpos($str1, $str2);
                break;
            case 'equalto':
                $valid = ($str1 === $str2);
                break;
            case 'not_contain':
                $valid = !strpos($str1, $str2);
                break;
            case 'not_equalto':
                $valid = !($str1 === $str2);
                break;
            default:
                $valid = 0;
                break;
        }
        return $valid;
    }

    /**
     * API call to run experiment.
     *
     * @return  Response
     */
    public function getExpInfo()
    {
        header('content-type: application/json; charset=utf-8');
        header("access-control-allow-origin: *");

        // Retrieve parameters
        $fullName = $_POST['fullName'];
        $pathName = $_POST['pathName'];
        $domainName = $_POST['domainName'];
        $querystring = parse_url($fullName, PHP_URL_QUERY);
        parse_str($querystring, $vars);
        $exp_id = isset($vars['exp_id']) ? $vars['exp_id'] : null ;

        $is_valid = 0;
        $experiment = Experiments::with('user')->find($exp_id);
        if ($experiment) {
            if (isset($experiment->user->domain_url)) {
                if ($experiment->user->domain_url === $domainName) {
                    $rules = json_decode($experiment->rules);
                    $options = json_decode($experiment->options);
                    $is_valid = 1;
                    if ($rules != '' && isset($rules->variable)) {
                        switch ($rules->variable) {
                            case 'domain':
                                $is_valid = self::checkRule($domainName,$rules->value, $rules->operator);
                                break;
                            case 'pagepath':
                                $is_valid = self::checkRule($pathName,$rules->value, $rules->operator);
                                break;
                            default:
                                $is_valid = 0;
                                break;
                        }
                    }
                    if ($is_valid) {
                        foreach ($options as &$item) {
                            if ($item->type == 'image') {
                                $item->value = self::getImagePath($item->value);
                            }
                        }
                        return response()->success($options);
                    } else {
                        // Rule doesn't match
                        return response()->error('Mismatched rule', 403);
                    }
                } else {
                    // User is accessing experiment that doesn't belong to user
                    return response()->error('Unauthorized', 401);
                }
            } else {
                return response()->error('Check domain url', 403);
            }
        } else {
            // No experiment found
            return response()->error('Not found', 404);
        }
    }
}
