<?php

namespace JxnuSpider\Model;

use JxnuSpider\Helper\Jwc;
use QL\QueryList;
use QL\Services\HttpService;
use GuzzleHttp\Exception\RequestException;

class Student extends Vister
{
    
    public $name;
    public $gender;
    public $class_name;
    public $major_name;
    public $college_name;
    public $phone;
    
    public $photo;
    public $total_credits;
    
    public function __construct(array $user, array $verifyData)
    {
        $this->uid = $user['id'] ?? '';
        $this->pwd = $user['pwd'] ?? '';
        $this->type = 'Student';

        $this->verifyData = $verifyData;
    }

    public function login()
    {
        // 处理非法请求
        if (!$this->uid) {
            exit('学号为空');
        }
        if (!$this->pwd) {
            exit('密码为空');
        }

        // 模拟登录验证合法性
        
        $ql = Jwc::login($this);

        // 返回结果
        $this->cookie_jar = HttpService::getCookieJar()->getCookieByName('JwOAUserSettingNew') 
                    ?: HttpService::getCookieJar()->getCookieByName('JwOAUserSettingNew2019');

        $ql->destruct();

        $this->setPhotoAttribute();

        return $this->visitInfoCheckPage()->visitPhonePage()->visitScorePage();
    }

    public function getInfo()
    {
        return [
            'uid' => $this->uid,
            'pwd' => $this->pwd,
            'type' => $this->type,

            'name' => $this->name,
            'gender' => $this->gender,
            'class_name' => $this->class_name,
            'major_name' => $this->major_name,
            'college_name' => $this->college_name,
            'phone' => $this->phone,

            'photo' => $this->photo,
            'total_credits' => $this->total_credits,
        ];

    }

    public function getCookieJar()
    {
        return $this->cookie_jar;
    }

    /**
     * 学生信息校对页面
     * 学生之家 > 我的信息 > 基本信息
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private function visitInfoCheckPage()
    {
        $url = 'https://jwc.jxnu.edu.cn/MyControl/Student_InforCheck.aspx';

        $ql = QueryList::get($url, [], [
            'header' => [
                'Cookie' => $this->cookie_jar
            ]
        ])->removeHead();
        
        $this->uid          = $this->uid        ?: $ql->find('#lblXH')->text();
        $this->name         = $this->name       ?: $ql->find('#lblXM')->text();
        $this->gender       = $this->gender     ?: $ql->find('#lblXB')->text();
        $this->class_name   = $this->class_name ?: $ql->find('#lblBJ')->text();
        // 身份证号
        // 民族

        $ql->destruct();

        return $this;
    }

    /**
     * 教务在线短信平台手机号码登记窗口
     * 学生之家 > 我的信息 > 手机号码
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private function visitPhonePage()
    {
        $url = 'https://jwc.jxnu.edu.cn/MyControl/Phone.aspx';

        $ql = QueryList::get($url, [], [
            'header' => [
                'Cookie' => $this->cookie_jar
            ]
        ])->removeHead();

        $this->class_name   = $this->class_name ?: $ql->find('#lblInfor u:eq(0)')->text();
        $this->uid          = $this->uid        ?: $ql->find('#lblInfor u:eq(1)')->text();
        $this->name         = $this->name       ?: $ql->find('#lblInfor u:eq(2)')->text();
        $this->phone        = $this->phone      ?: $ql->find('#lblInfor u:eq(3)')->text();
        
        $ql->destruct();

        return $this;
    }

    /**
     * 课程成绩页
     * 学生之家 > 我的信息 > 课程成绩
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private function visitScorePage()
    {
        $url = 'https://jwc.jxnu.edu.cn/MyControl/All_Display.aspx';
        
        try {
            $ql = QueryList::get($url, [
                'UserControl' => 'xfz_cj.ascx',
                'Action' => 'Personal'
            ], [
                'header' => [
                    'Cookie' => $this->cookie_jar
                ]
            ])->removeHead();
        } catch (RequestException $e) {
            print_r($e->getRequest());
            exit('课程成绩页发生错误');
        }

        // 学号
        $this->name             = $this->name           ?: $ql->find('#_ctl5_lblMsg u:eq(4)')->text();
        $this->college_name     = $this->college_name   ?: $ql->find('#_ctl5_lblMsg u:eq(0)')->text();
        $this->major_name       = $this->major_name     ?: $ql->find('#_ctl5_lblMsg u:eq(1)')->text();
        $this->class_name       = $this->class_name     ?: $ql->find('#_ctl5_lblMsg u:eq(2)')->text();
        $this->total_credits    = $this->total_credits  ?: $ql->find('#_ctl5_lblMsg u:eq(5)')->text();

        $ql->destruct();

        return $this;
    }

    private function setPhotoAttribute()
    {
        $this->photo = $this->photo ?: 'https://jwc.jxnu.edu.cn/MyControl/All_PhotoShow.aspx?UserNum='. $this->uid .'&UserType=Student';
    }

}