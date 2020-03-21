<?php
class ControllereventmoduleEventModule extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('event_module/event_module');

		$this->document->setTitle($this->language->get('heading_title'));

		//$this->load->model('event_module/event_module');

		$this->getList();
	}
    public function add() {
		$this->load->language('event_module/event_module');

		$this->document->setTitle($this->language->get('heading_title'));

		//$this->load->model('event_module/event_module');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('eventmodule/event_module', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}
	protected function getList() {
	    
	    if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
	    $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('eventmodule/event_module', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['add'] = $this->url->link('eventmodule/event_module/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('eventmodule/event_module/delete', 'user_token=' . $this->session->data['user_token'], true);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('event_module/event_module_list', $data));
	}

	protected function getForm() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('eventmodule/event_module/add', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		if (!isset($this->request->get['event_id'])) {
			$data['action'] = $this->url->link('eventmodule/event_module/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('eventmodule/event_module/edit', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $this->request->get['event_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('eventmodule/event_module', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('event_module/event_module_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'event_module/event_module')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		

		return !$this->error;
	}

}
