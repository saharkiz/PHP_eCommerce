<?php
class ControllerAuctionBidIncrements extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('auction/bid_increments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('auction/bid_increments');

		$this->getList();
	}
    
    protected function getList() {
        
        if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'increment_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
        
        if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['increment'])) {
			$url .= '&increment_id=' . $this->request->get['increment'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
        
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		
		$data['add'] = $this->url->link('auction/bid_increments/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('auction/bid_increments/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['increments'] = array();
        
        $filter_data = array(
            'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
        
        $results = $this->model_auction_bid_increments->getAllIncrements($filter_data);
        
        foreach($results as $result){
            $data['increments'][] = array(
                'increment_id'   => $result['increment_id'],
                'increment'          => $result['increment'],
                'low_bid'   => $result['bid_low'],
				'high_bid'   => $result['bid_high'],
                'edit'       => $this->url->link('auction/bid_increments/edit', 'user_token=' . $this->session->data['user_token'] . '&increment_id=' . $result['increment_id'] . $url, true)
            );
        }
        
        $data['heading_title'] = $this->language->get('heading_title');
        $data['column_increment'] = $this->language->get('column_increment');
        $data['column_low_bid'] = $this->language->get('column_low_bid');
		$data['column_high_bid'] = $this->language->get('column_high_bid');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        
        $data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
        
        $data['user_token'] = $this->session->data['user_token'];
        
        
        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
        
        if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
        
        $increment_total = $this->model_auction_bid_increments->getTotalIncrements();

		$pagination = new Pagination();
		$pagination->total = $increment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($increment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($increment_total - $this->config->get('config_limit_admin'))) ? $increment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $increment_total, ceil($increment_total / $this->config->get('config_limit_admin')));
        
        $data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('auction/bid_increments_list', $data));

        
        
    }
    
    protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['increment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['entry_bid_low'] = $this->language->get('entry_bid_low');
		$data['entry_bid_high'] = $this->language->get('entry_bid_high');
		$data['entry_increment'] = $this->language->get('entry_increment');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['low'])) {
			$data['error_bid_low'] = $this->error['low'];
		} else {
			$data['error_bid_low'] = array();
		}
        
        if (isset($this->error['high'])) {
			$data['error_bid_high'] = $this->error['high'];
		} else {
			$data['error_bid_high'] = array();
		}
		
		if (isset($this->error['increment'])) {
			$data['error_increment'] = $this->error['increment'];
		} else {
			$data['error_increment'] = array();
		}
        
		$this->error['warning'] = '';
		$this->error['low'] = '';
		$this->error['high'] = '';
		$this->error['increment'] = '';
		
		
        $url = '';
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['increment_id'])) {
			$data['action'] = $this->url->link('auction/bid_increments/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('auction/bid_increments/edit', 'user_token=' . $this->session->data['user_token'] . '&increment_id=' . $this->request->get['increment_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['increment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$increment_info = $this->model_auction_bid_increments->getIncrement($this->request->get['increment_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];
        
        if (isset($this->request->post['increment'])) {
			$data['increment'] = $this->request->post['increment'];
		} elseif (!empty($increment_info)) {
			$data['increment'] = $increment_info['increment'];
		} else {
			$data['increment'] = '';
		}
        
        if (isset($this->request->post['bid_low'])) {
			$data['bid_low'] = $this->request->post['bid_low'];
		} elseif (!empty($increment_info)) {
			$data['bid_low'] = $increment_info['bid_low'];
		} else {
			$data['bid_low'] = '';
		}
        
		if (isset($this->request->post['bid_high'])) {
			$data['bid_high'] = $this->request->post['bid_high'];
		} elseif (!empty($increment_info)) {
			$data['bid_high'] = $increment_info['bid_high'];
		} else {
			$data['bid_high'] = '';
		}
        
        
        
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('auction/bid_increments_form', $data));    

    }
    
    public function add() {
		$this->load->language('auction/bid_increments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('auction/bid_increments');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_auction_bid_increments->addIncrement($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('auction/bid_increments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('auction/bid_increments');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
			$this->model_auction_bid_increments->editIncrement($this->request->get['increment_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {

		$this->load->language('auction/bid_increments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('auction/bid_increments');

		if (isset($this->request->post['selected'])) {
            
			foreach ($this->request->post['selected'] as $increment_id) {
				$this->model_auction_bid_increments->deleteIncrement($increment_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('auction/bid_increments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
    
    
    protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'auction/bid_increments')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ($this->request->post['bid_low'] >= $this->request->post['bid_high']) {
			$this->error['warning'] = $this->language->get('error_high_low');
		}
		
		if (!is_numeric($this->request->post['bid_low'])) {
			$this->error['low'] = $this->language->get('error_not_number');
		}
		if (!is_numeric($this->request->post['bid_high'])) {
			$this->error['high'] = $this->language->get('error_not_number');
		}
		if (!is_numeric($this->request->post['increment'])) {
			$this->error['increment'] = $this->language->get('error_not_number');
		}
		
		if ($this->request->post['bid_low'] < 0 ){
			$this->error['low'] = $this->language->get('error_negative_number');
		}
		if ($this->request->post['bid_high'] < 0 ){
			$this->error['high'] = $this->language->get('error_negative_number');
		}
		if ($this->request->post['increment'] < 0) {
			$this->error['increment'] = $this->language->get('error_negative_number');
		}
		
		return !$this->error;
	}
	
    
    
} // End of Controller