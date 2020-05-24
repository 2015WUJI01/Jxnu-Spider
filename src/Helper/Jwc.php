<?php

namespace JxnuSpider\Helper;

use QL\QueryList;
use JxnuSpider\Model\Vister;
use GuzzleHttp\Exception\RequestException;

class Jwc
{
    public static $domain = 'https://jwc.jxnu.edu.cn'; //域名

    public static function url($alias = null)
    {

        $url = [
            'home' => '/Portal/Index.aspx', //首页
            'login' => '/Portal/LoginAccount.aspx?t=account', //登录页
            'infoCheck' => '/MyControl/Student_InforCheck.aspx', //基本信息（信息校对表）
        ];

        if ($alias && isset($url, $alias)) {
            return self::$domain . ($url[$alias] ?? '');
        }

        return null;
    }

    public static function getCaptcha()
    {
        $ql = QueryList::get(Jwc::url('login'))
            ->removeHead();

        $data = [
            'viewStatus' => $ql->find('#__VIEWSTATE')->val(),
            'eventValidation' => $ql->find('#__EVENTVALIDATION')->val(),
            'captchaSrc' => $ql->find('#_ctl0_cphContent_imgPasscode')->attr('src'),
        ];
        $ql->destruct();
        return $data;
    }

    public static function login(Vister $vister)
    {
        try {
            $ql = QueryList::post(Jwc::url('login'), [
                '__EVENTTARGET' => '',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => '',
                '__VIEWSTATE' => $vister->verifyData['viewStatus'],
                '__EVENTVALIDATION' => $vister->verifyData['eventValidation'],
                '_ctl0:cphContent:ddlUserType' => $vister->type,
                '_ctl0:cphContent:txtUserNum' => $vister->uid,
                '_ctl0:cphContent:txtPassword' => $vister->pwd,
                '_ctl0:cphContent:txtCheckCode' => $vister->verifyData['captcha'],
                '_ctl0:cphContent:btnLogin' => '登录',
            ], [
                'timeout' => 300,
                'headers' => [
                    'Referer' => Jwc::url('login'),
                ]
            ])->removeHead();
        } catch (RequestException $e) {
            print_r($e->getRequest());
            exit('登录过程中发生错误');
        }

        return $ql;
    }
}