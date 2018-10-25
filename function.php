<?php
// 获取毫秒级时间戳
	function getMillisecond() {
	    list($t1, $t2) = explode(' ', microtime());
	    return (float)sprintf('%.0f', (floatval($t1)+floatval($t2))*1000);
	}
/**
 * 功能：邮件发送函数
 * @param string $to 目标邮箱
 * @param string $subject 邮件主题（标题）
 * @param string $to 邮件内容
 * @return bool true
 */
 //  function sendMail($to,$subject, $content) {
 //     vendor('Mailer.PHPMailer');
 //     vendor('Mailer.SMTP'); 
 
 //    $mail = new PHPMailer(); //实例化
 //    $mail->IsSMTP(); // 启用SMTP
 //    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以新浪邮箱为例）
 //    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
 //    $mail->Username = C('MAIL_USERNAME'); //发件人邮箱名，从config.php中获得
 //    $mail->Password = C('MAIL_PASSWORD') ; //发件人邮箱密码
 //    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
 //    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
 //    $mail->AddAddress($to,"Dear customers");
 //    $mail->WordWrap = 50; //设置每行字符长度
 //    $mail->Port = 25;
 //    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
 //    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
 //    $mail->Subject = $content;
 //    $mail->Body = 'Hello, your ITOMTE webiste (www.itomte.com) <b> verification code:'.$subject.'</b>';
 //    $mail->AltBody = "麦趣科技"; //邮件正文不支持HTML的备用显示
 //    // 发送邮件
 //    if (!$mail->Send()) {
 //        return $mail->ErrorInfo;
 //        return FALSE;
 //    } else {
 //        return TRUE;
 //    }
 // }
 /**
*   发送邮箱
*   @param $to 目标邮箱 $name 标题
*   @param $body 邮箱内容
*   @return true  错误信息
 **/

function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
     vendor('Mailer.PHPMailer');
     vendor('Mailer.SMTP'); 
 
    $mail = new PHPMailer(); //实例化
    // $mail = new \Lib\PHPMailer\Phpmailer();
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
  //  $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = C('MAIL_HOST');  // SMTP 服务器
    $mail->Port       = 25;  // SMTP服务器的端口号
    $mail->Username   = C('MAIL_USERNAME');  // SMTP服务器用户名
    $mail->Password   = C('MAIL_PASSWORD');  // SMTP服务器密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress(C('MAIL_USERNAME'), $name);
    $mail->AddCC($to); //添加抄送人      163邮箱限制 先发送邮箱自己账号 再抄送用户账号
    // return $mail;
    if(is_array($attachment)){ // 添加附件 
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}


 //生成唯一订单号
function build_order_no(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
?>