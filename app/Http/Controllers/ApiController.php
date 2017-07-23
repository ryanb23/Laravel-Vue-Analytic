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

    private function getImagePath($url){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        return $protocol.$_SERVER['SERVER_NAME'].'/images/'.$url;
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
        $fullName = $_POST['fullName'];
        $pathName = $_POST['pathName'];
        $domainName = $_POST['domainName'];
        $is_valid = 0;

        $querystring = parse_url($fullName, PHP_URL_QUERY);
        parse_str($querystring, $vars);
        $exp_id = $vars['exp_id'];

        $user_info = User::with('experiments')->whereHas('experiments', function($query) use($exp_id){
            $query->where('id',$exp_id);
        })->where('domain_url',$domainName)
        ->first();

        if(isset($user_info->experiments) && count($user_info->experiments))
        {
            $experiments = $user_info->experiments;
            $rules = json_decode($experiments[0]->rules);
            $options = json_decode($experiments[0]->options);
            if($rules != '' && count($rules))
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
            return response()->error('no match',201);
        }
    }
}
