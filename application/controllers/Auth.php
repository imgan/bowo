<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'core/Frontend_Controller.php';

class Auth extends Frontend_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->model('User_model');
  }


  public function login()
  {
    if($this->data['is_login']) {
      return redirect(base_url());
    }
    $this->data['page'] = 'frontend/auth/v_login';
    $this->load->view('frontend/index', $this->data);
  }

  public function register()
  {
    if($this->data['is_login']) {
      return redirect(base_url());
    }
    $this->data['page'] = 'frontend/auth/v_register';
		$this->load->view('frontend/index', $this->data);
  }

  public function logout()
  {
    $this->session->unset_userdata('user');
    $this->session->sess_destroy();
    return redirect(base_url());
  }

	public function doLogin()
	{
    validate_self_request();
    $email = htmlspecialchars($this->input->post('email', TRUE));
    $password = htmlspecialchars($this->input->post('password', TRUE));
    $redirect = htmlspecialchars($this->input->get('redirect', TRUE));

    $check = $this->User_model->checkAuth($email, $password);

    if(!$check) {
      $this->session->set_flashdata('err', 'Gagal Login, Coba lagi.');
      return redirect(base_url('auth/login?redirect=' . $redirect));
    }

    $token = $this->User_model->createToken($check);

    if(!$check || !$token) {
      $this->session->set_flashdata('err', 'Gagal Login, Coba lagi.');
      return redirect(base_url('auth/login?redirect=' . $redirect));
    }

		return redirect($redirect);
  }

  public function doRegister()
  {
    validate_self_request();
    $passwordConfirm = htmlspecialchars($this->input->get('password_confirm', TRUE));
    $password = htmlspecialchars($this->input->get('password', TRUE));
    $redirect = htmlspecialchars($this->input->get('redirect', TRUE));

    $params = [
      'first_name' => $this->input->post('first_name', TRUE),
      'last_name' => $this->input->post('last_name', TRUE),
      'email' => $this->input->post('email', TRUE),
      'phone' => $this->input->post('phone', TRUE),
      'password' => password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT),
      'referral' => $this->User_model->generate_referral(),
      'role' => $this->input->post('registrasi', TRUE) ?? 'member',
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    if($params['role'] == 'admin') {
      $params['role'] = 'member';
    }
    
    if($params['role'] == 'affiliate') {
	    if($leaderId = get_cookie('aff_data')) {
		    $checkLeader = $this->User_model->get_user($leaderId);
		    
		    if($checkLeader['role'] != 'leader') {
			    $this->session->set_flashdata('err', 'Untuk daftar sebagai Affiliator, harap melalui Refferral Leader');
			    return redirect(base_url('auth/register?redirect=' . $redirect));
		    }
		
		    $params['leader_id'] = $leaderId;
	    } else {
		    $this->session->set_flashdata('err', 'Untuk daftar sebagai Affiliator, harap melalui Refferral Leader');
		    return redirect(base_url('auth/register?redirect=' . $redirect));
	    }
    }
    
    if($affiliatorId = get_cookie('aff_data')) {
	    if($params['role'] == 'member') {
	        $checkAffiliator = $this->User_model->get_user($affiliatorId);
	    
    	    if($checkAffiliator['role'] != 'affiliate') {
    		    $this->session->set_flashdata('err', 'Referral ini hanya bisa daftar sebagai Affiliator. Jika ingin daftar sebagai Member silahkan pakai Referral Affiliator.');
    		    return redirect(base_url('auth/register?redirect=' . $redirect));
    	    }
    	
    	    $params['affiliator_id'] = $affiliatorId ?? 0;
	    }
    }
    
    // if($params['role'] != 'member' || $params['role'] != 'affiliate') {
    //   $this->session->set_flashdata('err', 'Gagal Registrasi');
    //   return redirect(base_url('auth/register?redirect=' . $redirect));
    // }

    $check = $this->User_model->checkUser($params['email']);

    if(
      $params['first_name'] == '' ||
      $params['last_name'] == '' || 
      $params['email'] == '' ||
      $params['password'] == '' ||
      $password != $passwordConfirm
    ) {
      $this->session->set_flashdata('err', 'Gagal Registrasi');
      return redirect(base_url('auth/register?redirect=' . $redirect));
    }

    if($check > 0) {
      $this->session->set_flashdata('err', 'Email sudah terdaftar.');
      return redirect(base_url('auth/register?redirect=' . $redirect));
    }

    $add = $this->User_model->add_user($params);

    $this->session->set_flashdata('success', 'Berhasil Registrasi');

    return redirect(base_url('auth/login?redirect=' . $redirect));
  }

  public function doRegisterAffiliator()
  {
    validate_self_request();
    $passwordConfirm = htmlspecialchars($this->input->get('password_confirm', TRUE));
    $password = htmlspecialchars($this->input->get('password', TRUE));
    $redirect = htmlspecialchars($this->input->get('redirect', TRUE));

    $params = [
      'leader_id' => $this->data['user']->id,
      'first_name' => $this->input->post('first_name', TRUE),
      'last_name' => $this->input->post('last_name', TRUE),
      'email' => $this->input->post('email', TRUE),
      'phone' => $this->input->post('phone', TRUE),
      'password' => password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT),
      'referral' => $this->User_model->generate_referral(),
      'role' => 'affiliate',
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    // if($params['role'] != 'member' || $params['role'] != 'affiliate') {
    //   $this->session->set_flashdata('err', 'Gagal Registrasi');
    //   return redirect(base_url('auth/register?redirect=' . $redirect));
    // }

    $check = $this->User_model->checkUser($params['email']);

    if(
      $params['first_name'] == '' ||
      $params['last_name'] == '' || 
      $params['email'] == '' ||
      $params['password'] == '' ||
      $password != $passwordConfirm
    ) {
      $this->session->set_flashdata('err', 'Gagal Registrasi');
      return redirect(base_url('member/my_affiliators?redirect=' . $redirect));
    }

    if($check > 0) {
      $this->session->set_flashdata('err', 'Email sudah terdaftar.');
      return redirect(base_url('member/my_affiliators?redirect=' . $redirect));
    }

    $add = $this->User_model->add_user($params);

    $this->session->set_flashdata('success', 'Berhasil Registrasi');

    return redirect(base_url('member/my_affiliators?redirect=' . $redirect));
  }

}
