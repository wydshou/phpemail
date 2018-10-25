<?php
namespace Home\Controller;
use Think\Controller;
class CreateController extends Controller{
	public function create(){
		$table=M('home_user');
		// var_dump($_SESSION);die;
		if (IS_POST && IS_AJAX) {
			if ($table->create()) {
			//获取查询条件，用于查询确认账户名是否已添加
				$where['username']=$table->username;

				if ($table->where($where)->find()) {
					$res['message']="User name already exists.";
					$res['type']=207;
					$this->ajaxReturn($res);
					
				}
				$email = I('email');
				if ($table->where(array('email'=>$email))->find()) {
					$res['message']="Email already exists.";
					$res['type']=207;
					$this->ajaxReturn($res);
				}
				//获取验证信息与时间
				$re_code = $_SESSION['re_code'];
				$re_time = $_SESSION['re_time'];
				$code = I('code'); 
				// $this->ajaxReturn($code);die;
				if ($code != $re_code) {
					$res['type'] = 201;
					$res['message'] = 'Verification code input error';
					$this->ajaxReturn($res);
				}
				//如果时间超过三分钟 该验证码失效
				if ($re_time + 180 < time()){
					session('re_code',null);
					session('re_time',null);
					$res['type'] = 201;
					$res['message'] = 'The verification code has expired.';	
					$this->ajaxReturn($res);
					
				}

				//处理时间
				$table->create_time=$table->save_time=time();
				//加密
				$table->password=md5(sha1($table->password.'mzy'));
				//调用添加函数
				$result=$table->add();
				//返回执行结果
				if ($result) {
					$res['message']='Create success';
					$res['type']=200;
					$res['url']=U('Login/login');
				}else{
					$res['message']='Create failure';
					$res['type']=204;
				}
				$this->ajaxReturn($res);
				die;
			}
		}
		layout(false); 
		$this->display();
		layout(false);
	}
/**
* 发送邮箱验证
*	@param $json 返回的值
*	@param $email 发送的邮箱
*	@param re_code 验证码 
**/

	public function fmail() 
	{	
		if (IS_POST && IS_AJAX) 
		{
			$json=array();
			$email = trim(I('email'));
			$shi = M('home_user')->where(array('email'=>$email))->find();
			if (empty($email)) {
				$json['res'] = '2';
				$json['desc'] = 'Please input the mailbox.';
			}elseif(!(strlen($email) > 6 && preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $email))){
				$json['res'] = '2';
				$json['desc'] = 'Please input the correct mailbox.';
			}elseif ($shi){
				$json['res'] = '2';
				$json['desc'] = 'The mailbox already exists.';
			}
			if (!empty($json)) {
				$this->ajaxReturn($json);
			}
			$rand_num=rand(100000,999999);
			$code = $_SESSION['re_code']; 
			$time = $_SESSION['re_time'];
			$email_content='Hello, your ITOMTE webiste (www.itomte.com) <b> verification code:'.$rand_num;
			// $this->ajaxReturn(think_send_mail($email,$rand_num,'Mike science and technology',$email_content));
			if ($time + 60 < time()){
				if (think_send_mail($email,$rand_num,'Mike science and technology',$email_content) === true){
				//发送成功  把验证码存储session 
				session('re_code',$rand_num);
				session('re_time',time());
				$result =array(
					'res' => '1',
					'desc' => 'Send success'
				);
			}else{
				$result =array(
					'res' => '2',
					'desc' => "fail in send"
				);
			}

		}else{
			$result = array(
				'res' => "2",
				'desc' => 'fail in send'
			);
			}
		$this->ajaxReturn($result);
		}
	}
}