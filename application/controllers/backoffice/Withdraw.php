<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'core/Backoffice_Controller.php';

class Withdraw extends Backoffice_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Widthdraw_affiliate_model');
    $this->load->model('Withdraw_provider_maintenance_model');
    $this->load->model('Commision_model');
  }

  public function datatable()
  {
    header('Content-Type: application/json');
    echo $this->Widthdraw_affiliate_model->datatable();
  }

  public function provider_history()
  {
    header('Content-Type: application/json');
    echo $this->Withdraw_provider_maintenance_model->provider();
  }

  public function maintenance_history()
  {
    header('Content-Type: application/json');
    echo $this->Withdraw_provider_maintenance_model->maintenance();
  }

  public function leader_history()
  {
    header('Content-Type: application/json');
    echo $this->Withdraw_provider_maintenance_model->leader();
  }

  public function mediator_history()
  {
    header('Content-Type: application/json');
    echo $this->Withdraw_provider_maintenance_model->mediator();
  }

  public function cs_history()
  {
    header('Content-Type: application/json');
    echo $this->Withdraw_provider_maintenance_model->cs();
  }

  public function index()
	{
    $this->data['page_title'] = 'Penarikan';
    $this->data['page'] = 'backoffice/withdraw/index';

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function mediator()
	{
    $this->data['page_title'] = 'Penarikan Mediator';
    $this->data['page'] = 'backoffice/withdraw/mediator';
    $this->data['saldo'] = $this->Commision_model->get_total_commision_mediator();
    $this->data['earning_today'] = $this->Commision_model->get_earning_today_mediator();
    $this->data['earning_month'] = $this->Commision_model->get_earning_month_mediator();
    $this->data['earning_month_yesterday'] = $this->Commision_model->get_earning_month_yesterday_mediator();

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function mediator_process()
  {
    try {
      if(isset($_POST) > 0) {
        $this->db->trans_start();

        $this->data['saldo'] = $this->Commision_model->get_total_commision_mediator();

        if((int) $this->data['saldo'] <= 0) {
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/mediator'));
        }

        $this->Withdraw_provider_maintenance_model->add([
          'amount' => $this->data['saldo'],
          'type' => 'mediator',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->set('amount', "amount-{$this->data['saldo']}", FALSE);
        $this->db->update('mediator_commision');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/mediator'));
        } else {
          $this->db->trans_commit();
          $this->session->set_flashdata('success', 'Berhasil Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/mediator'));
        }
      }
    } catch (Exception $e) {
      $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
      return redirect(base_url('backoffice/withdraw/mediator'));
    }
  }

  public function cs()
	{
    $this->data['page_title'] = 'Penarikan CS';
    $this->data['page'] = 'backoffice/withdraw/cs';
    $this->data['saldo'] = $this->Commision_model->get_total_commision_cs();
    $this->data['earning_today'] = $this->Commision_model->get_earning_today_cs();
    $this->data['earning_month'] = $this->Commision_model->get_earning_month_cs();
    $this->data['earning_month_yesterday'] = $this->Commision_model->get_earning_month_yesterday_cs();

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function cs_process()
  {
    try {
      if(isset($_POST) > 0) {
        $this->db->trans_start();

        $this->data['saldo'] = $this->Commision_model->get_total_commision_cs();

        if((int) $this->data['saldo'] <= 0) {
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/cs'));
        }

        $this->Withdraw_provider_maintenance_model->add([
          'amount' => $this->data['saldo'],
          'type' => 'cs',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->set('amount', "amount-{$this->data['saldo']}", FALSE);
        $this->db->update('cs_commision');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/cs'));
        } else {
          $this->db->trans_commit();
          $this->session->set_flashdata('success', 'Berhasil Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/cs'));
        }
      }
    } catch (Exception $e) {
      $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
      return redirect(base_url('backoffice/withdraw/cs'));
    }
  }

  public function maintenance()
	{
    $this->data['page_title'] = 'Penarikan Maintenance';
    $this->data['page'] = 'backoffice/withdraw/maintenance';
    $this->data['saldo'] = $this->Commision_model->get_total_commision_maintenance();
    $this->data['earning_today'] = $this->Commision_model->get_earning_today_maintenance();
    $this->data['earning_month'] = $this->Commision_model->get_earning_month_maintenance();
    $this->data['earning_month_yesterday'] = $this->Commision_model->get_earning_month_yesterday_maintenance();

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function maintenance_process()
  {
    try {
      if(isset($_POST) > 0) {
        $this->db->trans_start();

        $this->data['saldo'] = $this->Commision_model->get_total_commision_maintenance();

        if((int) $this->data['saldo'] <= 0) {
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/maintenance'));
        }

        $this->Withdraw_provider_maintenance_model->add([
          'amount' => $this->data['saldo'],
          'type' => 'maintenance',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->set('amount', "amount-{$this->data['saldo']}", FALSE);
        $this->db->update('maintenance_commision');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/maintenance'));
        } else {
          $this->db->trans_commit();
          $this->session->set_flashdata('success', 'Berhasil Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/maintenance'));
        }
      }
    } catch (Exception $e) {
      $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
      return redirect(base_url('backoffice/withdraw/maintenance'));
    }
  }

  public function provider_process()
  {
    try {
      if(isset($_POST) > 0) {
        $this->db->trans_start();

        $this->data['saldo'] = $this->Commision_model->get_total_commision_provider();

        if((int) $this->data['saldo'] <= 0) {
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/provider'));
        }

        $this->Withdraw_provider_maintenance_model->add([
          'amount' => $this->data['saldo'],
          'type' => 'provider',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->set('amount', "amount-{$this->data['saldo']}", FALSE);
        $this->db->update('provider_commision');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/provider'));
        } else {
          $this->db->trans_commit();
          $this->session->set_flashdata('success', 'Berhasil Mencairkan Dana');
          return redirect(base_url('backoffice/withdraw/provider'));
        }
      }
    } catch (Exception $e) {
      $this->session->set_flashdata('err', 'Gagal Mencairkan Dana');
      return redirect(base_url('backoffice/withdraw/provider'));
    }
  }

  public function provider()
	{
    $this->data['page_title'] = 'Penarikan Provider';
    $this->data['page'] = 'backoffice/withdraw/provider';
    $this->data['saldo'] = $this->Commision_model->get_total_commision_provider();
    $this->data['earning_today'] = $this->Commision_model->get_earning_today_provider();
    $this->data['earning_month'] = $this->Commision_model->get_earning_month_provider();
    $this->data['earning_month_yesterday'] = $this->Commision_model->get_earning_month_yesterday_provider();

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function detail($id = 0)
	{
    $this->data['page_title'] = 'Detail Penarikan';
    $this->data['page'] = 'backoffice/withdraw/detail';

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Penarikan',
        'link' => 1
      ]
    ];

    $this->data['detail'] = $this->Widthdraw_affiliate_model->detail_with_user($id);

		$this->load->view('backoffice/index', $this->data);
  }

  public function update_status($id = 0)
  {
    try {

      $update  = $this->Widthdraw_affiliate_model->update_withdraw_affiliate($id, [
        'status' => $this->input->post('status') ?? 'pending'
      ]);

      if(!$update) {
        $this->session->set_flashdata('err', 'Gagal Update Status');
        return redirect(base_url('backoffice/withdraw/detail/' . $id));
      }

      $this->session->set_flashdata('success', 'Sukses Update Status');
      return redirect(base_url('backoffice/withdraw/detail/' . $id));
    } catch (Exception $e) {
      $this->session->set_flashdata('err', 'Gagal Update Status');
      return redirect(base_url('backoffice/withdraw/detail/' . $id));
    }
  }
}