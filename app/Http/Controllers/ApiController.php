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

    private function getImagePath($filename){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        return $protocol.$_SERVER['SERVER_NAME'].'/'.env('UPLOAD_DIR').'/'.$filename;
    }

    private function checkRule($str1, $str2, $operator){
        $valid = 0;
        switch ($operator) {
            case 'contain':
                $valid = preg_match('/'.$str2.'/i', $str1);
                break;
            case 'equalto':
                $valid = ($str1 === $str2);
                break;
            case 'not_contain':
                $valid = !preg_match('/'.$str2.'/i', $str1);
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

    public function getExpInfo()
    {
        header('content-type: application/json; charset=utf-8');
        header("access-control-allow-origin: *");


        $fullName = $_POST['fullName'];
        $pathName = $_POST['pathName'];
        $domainName = $_POST['domainName'];
        $is_valid = 0;

        $querystring = parse_url($fullName, PHP_URL_QUERY);
        parse_str($querystring, $vars);
        $exp_id = isset($vars['exp_id']) ? $vars['exp_id'] : null ;

        $experiment = Experiments::with('user')->find($exp_id);

        if($experiment)
        {
            if (isset($experiment->user->domain_url)) {
                if ($experiment->user->domain_url === $domainName) {
                    $rules = json_decode($experiment->rules);
                    $options = json_decode($experiment->options);
                    $is_valid = 1;
                    if($rules != '' && isset($rules->variable))
                    {
                        switch($rules->variable)
                        {
                            case 'domain':
                                $is_valid = self::checkRule($domainName,$rules->value, $rules->operator);
                                break;
                            case 'pagepath':
                                $is_valid = self::checkRule($pathName,$rules->value, $rules->operator);
                                break;
                            default:
                                $is_valid =0;
                                break;
                        }
                    }
                    if($is_valid)
                    {
                        foreach($options as &$item)
                        {
                            if($item->type == 'image')
                            {
                                $item->value = self::getImagePath($item->value);
                            }
                        }
                        return response()->success($options);
                    }else{
                        // Rule doesn't match
                        return response()->error('Rule not match', 405);
                    }
                } else {
                    // User is accessing experiment that doesn't belong to user
                    return response()->error('Forbidden', 403);
                }
            }
        }else{
            // No experiment found
            return response()->error('Not found', 404);
        }
    }
}
