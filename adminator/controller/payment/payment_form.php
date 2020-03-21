<?php
class ControllerPaymentPaymentForm extends Controller {
	private $error = array();

	public function index() {
	    if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
		
		$this->load->language('page/page_form');

		$this->document->setTitle("External Payment");
        $data["ref"] = $this->generateRandomString(5);
        $data['action'] = $this->url->link('payment/payment_form/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['cancel'] = $this->url->link('payment/payment_form', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/payment_form', $data));
	}
	public function add() {
	    if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$this->load->language('page/page_form');

		$this->document->setTitle("External Payment");

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		    //----send payment link
		    
		    $this->emailpayment();
		    
		    //---end payment link
		
		
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('payment/payment_form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		else
		{
		    $this->session->data['error_warning'] = "Please fill up all fields";
		    $this->response->redirect($this->url->link('payment/payment_form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		
	}
	
	public function emailpayment()
    {    
        $this->load->language('sale/order');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}
		$order_id = $this->request->post["eventreference"];
		$data['order_id'] = $order_id;
		$data['customername'] = $this->request->post["eventtitle"];
		$data['customeremail'] = $this->request->post["eventemail"];
		$data['productname'] = $this->request->post["event_description"];
		$data['totaltext'] = $this->request->post["eventamount"];
		$data['gateway'] = $this->request->post["gateway"];
		
		$data['paynow'] = 'https://littlethingsme.com/pay?exorder_id=' . $order_id .
		    '&amount=' . $data['totaltext'] .
		    '&name=' . $data['customername'] .
		    '&email=' . $data['customeremail'] .
		    '&gateway=' . $data['gateway'] .
		    '&user_token=' . $this->session->data['user_token'];
        //email
        $this->load->model('setting/setting');
		$from = $this->model_setting_setting->getSettingValue('config_email',0);
		
		if (!$from) {
			$from = $this->config->get('config_email');
		}
        $mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo('aresh@altannan.com,htamimi@altannan.com,tamimi@altannan.com');
		$mail->setFrom($from);
		$mail->setSender(html_entity_decode('The Little Things Dubai UAE', ENT_QUOTES, 'UTF-8'));
		$mail->setSubject('Payment Link TheLittleThings Order:'.$order_id );
		$mail->setHtml($this->load->view('sale/order_externalpayment', $data));
		$mail->send();
		
		//send to customer
		$mail->setTo($this->request->post["eventemail"]);
		$mail->send();
		//remaining
		$this->response->setOutput($this->load->view('sale/order_externalpayment', $data));
	}
    function generateRandomString($length = 10) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}