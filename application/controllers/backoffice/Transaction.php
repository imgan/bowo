<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'core/Backoffice_Controller.php';

class Transaction extends Backoffice_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Transaction_model');
    $this->load->model('Product_model');
    $this->load->model('Transaction_detail_model');
    $this->load->model('Transaction_shipping_model');
    $this->load->model('Affiliator_commision_model');
    $this->load->model('Maintenance_commision_model');
    $this->load->model('Provider_commision_model');
    $this->load->model('Commision_model');
    $this->load->model('Notification_model');
  }

  public function datatable()
  {
      header('Content-Type: application/json');
      echo $this->Transaction_model->datatable_backoffice();
  }

  public function commision()
  {
      header('Content-Type: application/json');
      echo $this->Transaction_model->commision_backoffice(
        $this->input->get('user') ?? 0
      );
  }

	public function index()
	{
    $this->data['page_title'] = 'Transaksi';
    $this->data['page'] = 'backoffice/transaction/index';

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Produk',
        'link' => 1
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  function detail($order_id)
  {
    $this->data['detail']['transaction'] = $this->Transaction_model->findByOrderId($order_id);
    $this->data['detail']['product'] = $this->Transaction_detail_model->findByTransaction($this->data['detail']['transaction']->id ?? 0);
    $this->data['detail']['shipping'] = $this->Transaction_shipping_model->findByTransaction($this->data['detail']['transaction']->id ?? 0);

    $this->data['page_title'] = 'Detail Transaksi ' . $order_id;
    $this->data['page'] = 'backoffice/transaction/detail';
    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Transaksi',
        'link' => '#'
      ],
      [
        'title' => $order_id,
        'link' => '#'
      ]
    ];
		$this->load->view('backoffice/index', $this->data);
  }

  public function report()
  {
    $this->data['page_title'] = 'Laporan Transaksi';
    $this->data['page'] = 'backoffice/transaction/report';

    $this->data['breadcrumbs'] = [
      [
        'title' => 'Apps',
        'link' => '#'
      ],
      [
        'title' => 'Transaksi',
        'link' => ''
      ]
    ];

		$this->load->view('backoffice/index', $this->data);
  }

  public function update_no_receipt($id = 0)
  {
    $this->db->trans_start();

    $value = $this->input->post('value');

    $transaction = $this->Transaction_model->get_transaction($id);

    $this->Transaction_model->update_transaction($id, [
      'no_receipt' => $value,
      'item_status' => 'send'
    ]);
    
    if($transaction['item_status'] != 'send') {
      $this->Notification_model->add_notification([
        'user_id' => $transaction['user_id'],
        'content' => "Pesanan <strong>{$transaction['order_id']}</strong> Dikirim",
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    } else {
      $this->Notification_model->add_notification([
        'user_id' => $transaction['user_id'],
        'content' => "Pesanan <strong>{$transaction['order_id']}</strong> Update Resi",
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
          
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE){
      $this->db->trans_rollback();
    }else{
      $this->db->trans_commit();
    } 

    // toJson([
    //   'success' => true,
    //   'message' => 'Success',
    //   'data' => $this->Commision_model->generate_commision_transaction($id)
    // ]);
    toJson([
      'success' => true,
      'message' => 'Success',
      'data' => null
    ]);
  }

  public function update_status($id = 0)
  {
    $this->db->trans_start();

    $value = $this->input->post('value');

    $this->Transaction_model->update_transaction($id, [
      'item_status' => $value
    ]);

    if($value == 'complete') {
      $this->Commision_model->generate_commision_transaction($id);
    }

    $status = '';

    if($value == 'process') {
      $status = 'Diproses';
    } else if($value == 'complete') {
      $status = 'Selesai';
    }

    $transaction = $this->Transaction_model->get_transaction($id);

    $this->Notification_model->add_notification([
      'user_id' => $transaction['user_id'],
      'content' => "Pesanan <strong>{$transaction['order_id']}</strong> $status",
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE){
      $this->db->trans_rollback();
    }else{
      $this->db->trans_commit();
    } 

    toJson([
      'success' => true,
      'message' => 'Success',
      'data' => null
    ]);
  }

  public function update_cod_price($id = 0)
  {
    $this->db->trans_start();

    $value = $this->input->post('value');

    $this->Transaction_model->update_transaction($id, [
      'cash_on_delivery_markup' => $value
    ]);

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE){
      $this->db->trans_rollback();
    }else{
      $this->db->trans_commit();
    } 

    toJson([
      'success' => true,
      'message' => 'Success',
      'data' => null
    ]);
  }
}