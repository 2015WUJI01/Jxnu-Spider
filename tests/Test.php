<?php

require __DIR__ . "/../vendor/autoload.php";

use JxnuSpider\Helper\Jwc;
use JxnuSpider\Model\Student;

$primal_verify_data = Jwc::getCaptcha();

if (isset($_POST['captcha']) && $_POST['captcha'])
{
    $user = [
        'id' => '...',
        'pwd' => '...',
    ];
    $verifyData = [
        'viewStatus' => $_POST['viewStatus'],
        'eventValidation' => $_POST['eventValidation'],
        'captcha' => $_POST['captcha']
    ];

    $student = new Student($user, $verifyData);
    $student->login();

    print_r($student->getInfo());
}
?>

<form action="" method="post">
    <input name="viewStatus" type="text" hidden value="<?php echo $primal_verify_data['viewStatus']; ?>">
    <input name="eventValidation" type="text" hidden value="<?php echo $primal_verify_data['eventValidation']; ?>">
    <img src="https://jwc.jxnu.edu.cn/Portal/<?php echo $primal_verify_data['captchaSrc']; ?>" height="40px;">
    验证码：<input name="captcha" type="text" >
    <button type="submit">提交</button>
</form>