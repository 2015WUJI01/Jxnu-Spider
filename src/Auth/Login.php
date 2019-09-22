<?php

namespace JxnuSpider\Auth;

use QL\QueryList;
use QL\Services\HttpService;

class Login
{
    public static function getCaptcha()
    {
        $ql = QueryList::get('http://jwc.jxnu.edu.cn/Portal/LoginAccount.aspx?t=account')
            ->removeHead();

        {
            // TODO: 如果访问失败，需要提示或进行处理
        }

        $data = [
            'viewStatus' => $ql->find('#__VIEWSTATE')->val(),
            'eventValidation' => $ql->find('#__EVENTVALIDATION')->val(),
            'captchaSrc' => $ql->find('#_ctl0_cphContent_imgPasscode')->attr('src'),
        ];
        $ql->destruct();
        return $data;
    }

    public static function login(array $user, array $verifyData)
    {
        // 处理非法请求
        if (!isset($user['id']) || !$user['id']) {
            return '学号为空';
        }
        if (!isset($user['pwd']) || !$user['pwd']) {
            return '密码为空';
        }
        if (!isset($user['type']) || !$user['type']) {
            $user['type'] = 'Student'; //默认作为 Student 登录
        }

        // 模拟登录验证合法性
        $ql = QueryList::post('http://jwc.jxnu.edu.cn/Portal/LoginAccount.aspx?t=account',[
            '__EVENTTARGET' => '',
            '__EVENTARGUMENT' => '',
            '__LASTFOCUS' => '',
            '__VIEWSTATE' => $verifyData['viewStatus'],
            '__EVENTVALIDATION' => $verifyData['eventValidation'],
            '_ctl0:cphContent:ddlUserType' => $user['type'],
            '_ctl0:cphContent:txtUserNum' => $user['id'],
            '_ctl0:cphContent:txtPassword' => $user['pwd'],
            '_ctl0:cphContent:btnLogin' => '登录',
            '_ctl0:cphContent:txtCheckCode' => $verifyData['captcha']
        ])->removeHead();

        {
            // TODO: 如果访问失败，需要提示或进行处理
        }

        // 返回结果
        $cookie_jar = HttpService::getCookieJar()->getCookieByName('JwOAUserSettingNew');

        {
            // TODO: 如果访问失败，需要提示或进行处理
            $ql->destruct();
            return $cookie_jar;
        }
    }
}