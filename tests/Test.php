<?php

require __DIR__ . "/../vendor/autoload.php";

use JxnuSpider\Auth\Login;
use JxnuSpider\Student\StudentInfoCollector as InfoCollector;

$primal_verify_data = Login::getCaptcha();

if (isset($_POST['captcha']) && $_POST['captcha'])
{
    $user = [
        'id' => '...',
        'pwd' => '...',
        'type' => '...'
    ];
    $verifyData = [
        'viewStatus' => $_POST['viewStatus'],
        'eventValidation' => $_POST['eventValidation'],
        'captcha' => $_POST['captcha']
    ];
    $cookie = Login::login($user, $verifyData);

    $user_info = InfoCollector::getInfo($cookie, 'name', 'gender');
    print_r($user_info);
}
?>

<form action="" method="post">
    <input name="viewStatus" type="text" hidden value="<?php echo $primal_verify_data['viewStatus']; ?>">
    <input name="eventValidation" type="text" hidden value="<?php echo $primal_verify_data['eventValidation']; ?>">
    <img src="https://jwc.jxnu.edu.cn/Portal/<?php echo $primal_verify_data['captchaSrc']; ?>" height="40px;">
    验证码：<input name="captcha" type="text" >
    <button type="submit">提交</button>
</form>