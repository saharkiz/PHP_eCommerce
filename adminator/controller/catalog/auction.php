<?php
class ControllerCatalogAuction extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			// testing
			$testdata = $this->request->post;
			$allow_custom_start_date = $this->config->get('config_auction_custom_start_date');
			$allow_custom_end_date = $this->config->get('config_auction_custom_end_date');

			$query = "SELECT NOW() as currenttime";
			$current_datetime = $this->db->query($query)->row;
			$testdata['date_created'] = $current_datetime['currenttime'];

			// date($this->language->get('datetime_format'), strtotime($current_datetime)),
			
			if(!$allow_custom_start_date){
					$testdata['custom_start_date'] = $current_datetime['currenttime'];
			}
			
			if(!$allow_custom_end_date) {
				$myEndDate = strtotime($testdata['custom_start_date']) + ($testdata['duration'] * 86400);
				$testdata['custom_end_date'] = date($this->language->get('datetime_format'), $myEndDate);
			}
			
			if($allow_custom_end_date && !isset($testdata['duration'])){
				
				$testdata['duration'] = (strtotime($testdata['custom_end_date']) - strtotime($testdata['custom_start_date'])) / 86400;
			}
			
			if($testdata['auction_type'] == '0'){
				$testdata['initial_quantity'] = '1';
			}
			
			foreach($testdata['auction_description'] as $language => $descriptions) {
				$testdata['auction_description'][$language]['meta_title'] = 'Auctioning ' . $descriptions['name'];
				$testdata['auction_description'][$language]['meta_description'] = strip_tags($descriptions['description']);
				$seader = $testdata['auction_description'][$language]['meta_title'] . ' ' . (null !== $descriptions['subname'] ? $descriptions['subname'] .' ': '') . $descriptions['description'];
				$testdata['auction_description'][$language]['meta_keyword'] = $seader;
			}
			
			$this->model_catalog_auction->addAuction($testdata);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

// fill out auction stuff			
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_auction_status'])) {
				$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		
/* POSTING HERE
 *
 *
*/
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			// testing
			$testdata = $this->request->post;
			$allow_custom_start_date = $this->config->get('config_auction_custom_start_date');
			$allow_custom_end_date = $this->config->get('config_auction_custom_end_date');

			$query = "SELECT NOW() as currenttime";
			$current_datetime = $this->db->query($query)->row;
			
			if(!$allow_custom_start_date && !isset($testdata['custom_start_date'])){
					$testdata['custom_start_date'] = $current_datetime['currenttime'];
			}
			
			if(!$allow_custom_end_date) {
				$myEndDate = strtotime($testdata['custom_start_date']) + ($testdata['duration'] * 86400);
				$testdata['custom_end_date'] = date($this->language->get('datetime_format'), $myEndDate);
			}
			
			if($allow_custom_end_date && !isset($testdata['duration'])){
				$testdata['duration'] = (strtotime($testdata['custom_end_date']) - strtotime($testdata['custom_start_date'])) / 86400;
			}
			
			if($testdata['auction_type'] == '0'){
				$testdata['initial_quantity'] = '1';
			}
			
			foreach($testdata['auction_description'] as $language => $descriptions) {
				$testdata['auction_description'][$language]['meta_title'] = 'Auctioning ' . $descriptions['name'];
				$testdata['auction_description'][$language]['meta_description'] = strip_tags($descriptions['description']);
				$seader = $testdata['auction_description'][$language]['meta_title'] . ' ' . (null !== $descriptions['subname'] ? $descriptions['subname'] .' ': '') . $descriptions['description'];
				$testdata['auction_description'][$language]['meta_keyword'] = $seader;
			}
			
			$testdata['auction_id'] = $this->request->get['auction_id'];
			$this->model_catalog_auction->editAuction($testdata);
			//$this->model_catalog_auction->editAuction($this->request->get['auction_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

// Fill out auction stuff here			
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_auction_status'])) {
				$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
// End Posting here
		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $auction_id) {
				$this->model_catalog_auction->deleteAuction($auction_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

// Fill out auction stuff here			
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_auction_status'])) {
				$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $auction_id) {
				$this->model_catalog_auction->copyAuction($auction_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

// Fill out auction stuff here			
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_auction_status'])) {
				$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_auction_id'])) {
			$filter_auction_id = $this->request->get['filter_auction_id'];
		} else {
			$filter_auction_id = null;
		}

		if (isset($this->request->get['filter_seller'])) {
			$filter_seller = $this->request->get['filter_seller'];
		} else {
			$filter_seller = null;
		}

		if (isset($this->request->get['filter_auction_status'])) {
			$filter_auction_status = $this->request->get['filter_auction_status'];
		} else {
			$filter_auction_status = null;
		}

		if (isset($this->request->get['filter_auction_type'])) {
			$filter_auction_type = $this->request->get['filter_auction_type'];
		} else {
			$filter_auction_type = null;
		}
		
		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = null;
		}

		if (isset($this->request->get['filter_date_created'])) {
			$filter_date_created = $this->request->get['filter_date_created'];
		} else {
			$filter_date_created = null;
		}

		if (isset($this->request->get['filter_start_date'])) {
			$filter_start_date = $this->request->get['filter_start_date'];
		} else {
			$filter_start_date = null;
		}
		
		if (isset($this->request->get['filter_end_date'])) {
			$filter_end_date = $this->request->get['filter_end_date'];
		} else {
			$filter_end_date = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			debuglog($sort);
		} else {
			$sort = 'a.auction_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_auction_id'])) {
			$url .= '&filter_auction_id=' . $this->request->get['filter_auction_id'];
		}

		if (isset($this->request->get['filter_seller'])) {
			$url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_auction_status'])) {
			$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
		}

		if (isset($this->request->get['filter_auction_type'])) {
			$url .= '&filter_auction_type=' . $this->request->get['filter_auction_type'];
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}
		
		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['auction'])) {
			$url .= '&auction=' . $this->request->get['auction'];
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
			'href' => $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		//$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		//$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['add'] = $this->url->link('catalog/auction/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('catalog/auction/delete', 'user_token=' . $this->session->data['user_token'], true);
		$data['copy'] = $this->url->link('catalog/auction/copy', 'user_token=' . $this->session->data['user_token'], true);

		$data['auctions'] = array();

		$filter_data = array(
			'filter_auction_id'      => $filter_auction_id,
			'filter_seller'	   => $filter_seller,
			'filter_auction_status'  => $filter_auction_status,
			'filter_auction_type'         => $filter_auction_type,
			'filter_date_created'    => $filter_date_created,
			'filter_start_date' => $filter_start_date,
			'filter_end_date' => $filter_end_date,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
		

		$results = $this->model_catalog_auction->getAuctions($filter_data);
		$this->load->model('tool/image');
		
		foreach ($results as $result) {
			
			if (isset($result['main_image']) && is_file(DIR_IMAGE . $result['main_image'])) {
			$thumb = $this->model_tool_image->resize($result['main_image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			
			$data['auctions'][] = array(
				'auction_id'        => $result['auction_id'],
				'image'				=>$thumb,
				'placeholder'		=> $this->model_tool_image->resize('no_image.png', 100, 100),
				'type'				=> $result['auction_type'] ? 'Dutch':'Regular',
				'seller'            => ucwords($result['firstname'] . ' ' . $result['lastname']),
				'auction_status'    => $result['status_name'] ? $result['status_name'] : $this->language->get('text_missing'),
				'title'       		=> $result['title'],
				'subtitle'			=> $result['subtitle'],
				'date_created'      => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				'start_date'        => date($this->language->get('datetime_format'), strtotime($result['start_date'])),
                'end_date'          => date($this->language->get('datetime_format'), strtotime($result['end_date'])),
				'view'              => $this->url->link('catalog/auction/info', 'user_token=' . $this->session->data['user_token'] . '&auction_id=' . $result['auction_id'] . $url, true),
				'edit'              => $this->url->link('catalog/auction/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_id=' . $result['auction_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_regular'] = $this->language->get('text_regular');
		$data['text_dutch'] = $this->language->get('text_dutch');
		$data['column_image'] = $this->language->get('column_image');
		$data['column_type'] = $this->language->get('column_type');
		$data['column_seller'] = $this->language->get('column_seller');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_createdate'] = $this->language->get('column_createdate');
		$data['column_startdate'] = $this->language->get('column_startdate');
		$data['column_enddate'] = $this->language->get('column_enddate');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_seller'] = $this->language->get('entry_seller');
		$data['entry_auction_status'] = $this->language->get('entry_auction_status');
		$data['entry_date_created'] = $this->language->get('entry_date_created');
		$data['entry_start_date'] = $this->language->get('entry_start_date');
		$data['entry_end_date'] = $this->language->get('entry_end_date');

		//$data['button_invoice_print'] = $this->language->get('button_invoice_print');
		//$data['button_shipping_print'] = $this->language->get('button_shipping_print');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_ip_add'] = $this->language->get('button_ip_add');

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

		if (isset($this->request->get['filter_auction_id'])) {
			$url .= '&filter_auction_id=' . $this->request->get['filter_auction_id'];
		}

		if (isset($this->request->get['filter_seller'])) {
			$url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_auction_status'])) {
			$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}
		
		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_auction'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=a.auction_id' . $url, true);
		$data['sort_type'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=a.auction_type' . $url, true);
		$data['sort_seller'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=seller' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=a.status' . $url, true);
		$data['sort_title'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.title' . $url, true);
		$data['sort_createdate'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=a.date_created' . $url, true);
		$data['sort_startdate'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.start_date' . $url, true);
		$data['sort_enddate'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.end_date' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_auction_id'])) {
			$url .= '&filter_auction_id=' . $this->request->get['filter_auction_id'];
		}

		if (isset($this->request->get['filter_seller'])) {
			$url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_auction_status'])) {
			$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}
		
		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$auction_total = $this->model_catalog_auction->getTotalAuctions($filter_data);

		$pagination = new Pagination();
		$pagination->total = $auction_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($auction_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($auction_total - $this->config->get('config_limit_admin'))) ? $auction_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $auction_total, ceil($auction_total / $this->config->get('config_limit_admin')));

		$data['filter_auction_id'] = $filter_auction_id;
		$data['filter_seller'] = $filter_seller;
		$data['filter_auction_status'] = $filter_auction_status;
		$data['filter_type'] = $filter_type;
		$data['filter_date_created'] = $filter_date_created;
		$data['filter_start_date'] = $filter_start_date;
		$data['filter_end_date'] = $filter_end_date;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('localisation/auction_status');

		$data['auction_statuses'] = $this->model_localisation_auction_status->getAuctionStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_list', $data));
	}

/*
 *
 *
 *		Start Here
 *
 *
 *
 */
	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['auction_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');

		// Tabs
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_options'] = $this->language->get('tab_options');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_seller'] = $this->language->get('tab_seller');
		$data['tab_bid_history'] = $this->language->get('tab_bid_history');
		$data['tab_reviews'] = $this->language->get('tab_reviews');
		$data['tab_design'] = $this->language->get('tab_design');
		$data['tab_fees'] = $this->language->get('tab_fees');
		
		
		// Tab General ****************************************************
		// Entry items on General Tab
		$data['entry_name'] = $this->language->get('entry_name');
		// if admin allows
		$data['entry_subname'] = $this->language->get('entry_subname');
		//
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_tag'] = $this->language->get('entry_tag');
		
		// Help items on General Tab
		$data['help_tag'] = $this->language->get('help_tag');
		$data['help_keyword'] = $this->language->get('help_keyword');
		
		// End of General Tab ********************************************
		
		// Tab Data ******************************************************
		// Entry items on Data Tab
		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_auction_status'] = $this->language->get('entry_auction_status');
		$data['entry_min_bid'] = $this->language->get('entry_min_bid');
		$data['entry_reserve_price'] = $this->language->get('entry_reserve_price');
		// these only available if custom start and end date is allowed by admin
		$data['entry_start_date'] = $this->language->get('entry_start_date');
		$data['entry_end_date'] = $this->language->get('entry_end_date');
		// else set durations available
		$data['entry_duration'] = $this->language->get('entry_duration');
		//
		// If admin allows
		$data['entry_increments'] = $this->language->get('entry_increments');
		$data['entry_bid_from'] = $this->language->get('entry_bid_from');
		$data['entry_bid_to'] = $this->language->get('entry_bid_to');
		$data['entry_bid_increment'] = $this->language->get('entry_bid_increment');
		$data['text_bid_increments'] = $this->language->get('text_bid_increments');
		$data['text_bid_low'] = $this->language->get('text_bid_low');
		$data['text_bid_high'] = $this->language->get('text_bid_high');
		$data['text_bid_increment'] = $this->language->get('text_bid_increment');
		//
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_international_shipping'] = $this->language->get('entry_international_shipping');
		$data['entry_shipping_cost'] = $this->language->get('entry_shipping_cost');
		$data['entry_additional_shipping'] = $this->language->get('entry_additional_shipping');
		$data['entry_initial_quantity'] = $this->language->get('entry_initial_quantity');
		// Help items on Data
		$data['help_type'] = $this->language->get('help_type');
		$data['help_min_bid'] = $this->language->get('help_min_bid');
		$data['help_price'] = $this->language->get('help_price');
		$data['help_reserve_price'] = $this->language->get('help_reserve_price');
		$data['help_custom_dates'] = $this->language->get('help_custom_dates');
		$data['help_duration'] = $this->language->get('help_duration');
		$data['help_bid_increments'] = $this->language->get('help_bid_increments');
		$data['help_shipping_cost'] = $this->language->get('help_shipping_cost');
		$data['help_additional_shipping'] = $this->language->get('help_additional_shipping');
		
		
		// End of Tab Data ************************************************
		// Tab Options
		// Entry for tab options
		$data['entry_buy_now_only'] = $this->language->get('entry_buy_now_only');
		$data['entry_buy_now_price'] = $this->language->get('entry_buy_now_price');
		// If admin allows
		$data['entry_auto_relist'] = $this->language->get('entry_auto_relist');
		$data['entry_auto_relist_times'] = $this->language->get('entry_auto_relist_times');
		//
		$data['entry_bolded'] = $this->language->get('entry_bolded');
		$data['entry_on_carousel'] = $this->language->get('entry_on_carousel');
		$data['entry_featured'] = $this->language->get('entry_featured');
		$data['entry_highlighted'] = $this->language->get('entry_highlighted');
		$data['entry_slideshow'] = $this->language->get('entry_slideshow');
		$data['entry_social_media'] = $this->language->get('entry_social_media');
		
		// Help for tab options
		$data['help_auto_relist'] = $this->language->get('help_auto_relist');
		$data['help_auto_relist_times'] = $this->language->get('help_auto_relist_times');
		$data['help_buy_now_only'] = $this->language->get('help_buy_now_only');
		$data['help_buy_now_price'] = $this->language->get('help_buy_now_price');
		$data['help_bolded_item'] = $this->language->get('help_bolded_item');
		$data['help_on_carousel'] = $this->language->get('help_on_carousel');
		$data['help_featured'] = $this->language->get('help_featured');
		$data['help_highlighted'] = $this->language->get('help_highlighted');
		$data['help_slideshow'] = $this->language->get('help_slideshow');
		$data['help_social_media'] = $this->language->get('help_social_media');
		
		// End of Tab Options
		
		
		// Tab Links
		// Entry items for tab Links
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_category'] = $this->language->get('entry_category');
		//$data['entry_buy_now'] = $this->language->get('entry_buy_now');

		// Help items for tab Links
		$data['help_category'] = $this->language->get('help_category');
		
		// End of Tab Links ***********************************************


		
		// Tab Images
		// Entry items for tab Image
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_additional_image'] = $this->language->get('entry_additional_image');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');		
		
		// End of Images Tab **********************************************
		
		
		// Sellers Info Tab
		// Entry items for tab Seller
		$data['entry_sellers_name'] = $this->language->get('entry_sellers_name');
		$data['entry_sellers_email'] = $this->language->get('entry_sellers_email');
		$data['entry_sellers_address1'] = $this->language->get('entry_sellers_address1');
		$data['entry_sellers_address2'] = $this->language->get('entry_sellers_address2');
		$data['entry_sellers_city'] = $this->language->get('entry_sellers_city');
		$data['entry_sellers_zone'] = $this->language->get('entry_sellers_zone');
		$data['entry_sellers_country'] = $this->language->get('entry_sellers_country');
		$data['entry_sellers_postcode'] = $this->language->get('entry_sellers_postcode');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		
		// Text items for tab Seller
		$data['text_sales_history'] = $this->language->get('text_sales_history');
		$data['text_bidding_history'] = $this->language->get('text_bidding_history');
		// Sale History columns
		$data['column_item'] = $this->language->get('column_item');
		$data['column_date_sold'] = $this->language->get('column_date_sold');
		$data['column_amount_sold'] = $this->language->get('column_amount_sold');
		$data['column_num_relisted'] = $this->language->get('column_num_relisted');
		$data['column_winning_bidder'] = $this->language->get('column_winning_bidder');
		$data['column_highest_bid'] = $this->language->get('column_highest_bid');
		$data['column_date_placed'] = $this->language->get('column_date_placed');
		$data['column_won_lost'] = $this->language->get('column_won_lost');
		
		
		// End of Seller Info Tab ******************************************
		
		
		// Bidding History Tab
		// Entry items for tab bidding
		// Text items for tab bidding
		// uses the bidding history text item from seller tab
		$data['text_closing_date'] = $this->language->get('text_closing_date');
		$data['text_reserved_bid'] = $this->language->get('text_reserved_bid');
		// Bid History Columns
		$data['column_bid_name'] = $this->language->get('column_bid_name');
		$data['column_bid_date'] = $this->language->get('column_bid_date');
		$data['column_bid_amount'] = $this->language->get('column_bid_amount');
		$data['column_bid_proxy'] = $this->language->get('column_bid_proxy');
				
		
		// End of Bid History Tab
		
		// Reviews and Complaints Tab
		// Entry items for reviews tab
		
		// text items for reviews tab
		
		// help items for reviews tab
		
		
		// End of Reviews and Complaints Tab
		
		
		// Design Tab
		// Entry items for design tab
		$data['entry_layout'] = $this->language->get('entry_layout');
		// already have this $data['entry_store'] = $this->language->get('entry_store');
		
		// text items for design tab
		
		// help items for design tab
		
		
		// End of Design Tab
		
		// Fees Tab
		// Columns
		$data['column_fee_name'] = $this->language->get('column_fee_name');
		$data['column_fee_amount'] = $this->language->get('column_fee_amount');
		$data['column_fee_date'] = $this->language->get('column_fee_date');
		

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_seller_file'] = $this->language->get('button_seller_file');
		$data['button_remove'] = $this->language->get('button_remove');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_auction_status'])) {
			$url .= '&filter_auction_status=' . $this->request->get['filter_auction_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
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
			'href' => $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['auction_id'])) {
			$data['action'] = $this->url->link('catalog/auction/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/auction/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_id=' . $this->request->get['auction_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$auction_info = array();
		$auction_options = array();
		
		if (isset($this->request->get['auction_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$auction_info = $this->model_catalog_auction->getAuction($this->request->get['auction_id']);
			$auction_options = $this->model_catalog_auction->getAuctionOptions($this->request->get['auction_id']);
		}
		

		if(!null ==$auction_info) {
		$data['seller_file'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $auction_info['customer_id'] . $url, true);
		$data['date_created'] = $auction_info['date_created'];
		} else {
			$data['seller_file'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['date_created'] = '';
		}
		
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['allow_subtitles'] = $this->config->get('config_auction_subtitles');
		$data['allow_auto_relist'] = $this->config->get('config_auction_auto_relist');
		$data['allow_custom_start_date'] = $this->config->get('config_auction_custom_start_date');
		$data['allow_custom_end_date'] = $this->config->get('config_auction_custom_end_date');
		$data['allow_custom_bid_increments'] = $this->config->get('config_auction_bid_increments');
		
		
		
		
		if (isset($this->request->post['auction_description'])) {
			$data['auction_description'] = $this->request->post['auction_description'];
		} elseif (isset($this->request->get['auction_id'])) {
			$data['auction_description'] = $this->model_catalog_auction->getAuctionDescriptions($this->request->get['auction_id']);
		} else {
			$data['auction_description'] = array();
		}

		if (isset($this->request->post['bolded_item'])) {
			$data['bolded_item'] = $this->request->post['bolded_item'];
		} elseif (!empty($auction_options)) {
			$data['bolded_item'] = $auction_options['bolded_item'];
		} else {
			$data['bolded_item'] = '0';
		}
		
		if (isset($this->request->post['on_carousel'])) {
			$data['on_carousel'] = $this->request->post['on_carousel'];
		} elseif (!empty($auction_options)) {
			$data['on_carousel'] = $auction_options['on_carousel'];
		} else {
			$data['on_carousel'] = '0';
		}
		
		if (isset($this->request->post['featured'])) {
			$data['featured'] = $this->request->post['featured'];
		} elseif (!empty($auction_options)) {
			$data['featured'] = $auction_options['featured'];
		} else {
			$data['featured'] = '0';
		}
		
		if (isset($this->request->post['highlighted'])) {
			$data['highlighted'] = $this->request->post['highlighted'];
		} elseif (!empty($auction_options)) {
			$data['highlighted'] = $auction_options['highlighted'];
		} else {
			$data['highlighted'] = '0';
		}
		
		if (isset($this->request->post['slideshow'])) {
			$data['slideshow'] = $this->request->post['slideshow'];
		} elseif (!empty($auction_options)) {
			$data['slideshow'] = $auction_options['slideshow'];
		} else {
			$data['slideshow'] = '0';
		}
		
		if (isset($this->request->post['buy_now_only'])) {
			$data['buy_now_only'] = $this->request->post['buy_now_only'];
		} elseif (!empty($auction_options)) {
			$data['buy_now_only'] = $auction_options['buy_now_only'];
		} else {
			$data['buy_now_only'] = '0';
		}
		
		if (isset($this->request->post['social_media'])) {
			$data['social_media'] = $this->request->post['social_media'];
		} elseif (!empty($auction_options)) {
			$data['social_media'] = $auction_options['social_media'];
		} else {
			$data['social_media'] = '0';
		}
		
		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($auction_info)) {
			$data['keyword'] = $auction_info['keyword'];
		} else {
			$data['keyword'] = '';
		}
		
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['auction_store'])) {
			$data['auction_store'] = $this->request->post['auction_store'];
		} elseif (isset($this->request->get['auction_id'])) {
			$data['auction_store'] = $this->model_catalog_auction->getAuctionStores($this->request->get['auction_id']);
		} else {
			$data['auction_store'] = array(0);
		}
		//debuglog($data['auction_store']);
		
		$this->load->model('auction/auction_setting');
		$data['types'] = $this->model_auction_auction_setting->getAuctionTypes();
		$data['statuses'] = $this->model_auction_auction_setting->getAuctionStatuses();

	
		if(isset($this->request->post['auction_type'])) {
			$data['auction_type'] = $this->request->post['auction_type'];
		} elseif (isset($this->request->get['auction_id'])) {
			$data['auction_type'] = $this->model_catalog_auction->getAuctionTypes($this->request->get['auction_id']);
		} else {
			$data['auction_type'] = array(0);
		}

		 
		if (isset($this->request->post['auction_status'])) {
			$data['auction_status'] = $this->request->post['auction_status'];
		} elseif (!empty($auction_info)) {
			$data['auction_status'] = $auction_info['status'];
		} else {
			$data['auction_status'] = '0';
		}
		
		if (isset($this->request->post['initial_quantity'])) {
			if ($this->request->post['auction_status'] == '0') {
				$data['initial_quantity'] = '1';
			} else {
				$data['initial_quantity'] = $this->request->post['initial_quantity'];
			}
		} elseif (!empty($auction_info)) {
			$data['initial_quantity'] = $auction_info['initial_quantity'];
		} else {
			$data['initial_quantity'] = '1';
		}
		
		if (isset($this->request->post['min_bid'])) {
			$data['min_bid'] = $this->request->post['min_bid'];
		} elseif (!empty($auction_info)) {
			$data['min_bid'] = $auction_info['min_bid'];
		} else {
			$data['min_bid'] = '';
		}
		
		if (isset($this->request->post['reserve_price'])) {
			$data['reserve_price'] = $this->request->post['reserve_price'];
		} elseif (!empty($auction_info)) {
			$data['reserve_price'] = $auction_info['reserve_price'];
		} else {
			$data['reserve_price'] = '';
		}
		
		if (isset($this->request->post['buy_now_price'])) {
			$data['buy_now_price'] = $this->request->post['buy_now_price'];
		} elseif (!empty($auction_info)) {
			$data['buy_now_price'] = $auction_info['buy_now_price'];
		} else {
			$data['buy_now_price'] = '';
		}
		
		
		if (isset($this->request->post['auto_relist'])) {
			$data['auto_relist'] = $this->request->post['auto_relist'];
		} elseif (!empty($auction_options)) {
			$data['auto_relist'] = $auction_options['auto_relist'];
		} else {
			$data['auto_relist'] = '';
		}
		
		if (isset($this->request->post['num_relist'])) {
			$data['num_relist'] = $this->request->post['num_relist'];
		} elseif (!empty($auction_info)) {
			$data['num_relist'] = $auction_info['num_relist'];
		} else {
			$data['num_relist'] = '';
		}

		
		
		if (isset($this->request->post['custom_start_date'])) {
			$data['custom_start_date'] = $this->request->post['custom_start_date'];
		} elseif (isset($auction_info['start_date'])) {
			$data['custom_start_date'] = $auction_info['start_date'];
		} else {
			$data['custom_start_date'] = '';
		}
		
		
		if (isset($this->request->post['custom_end_date'])) {
			$data['custom_end_date'] = $this->request->post['custom_end_date'];
		} elseif (isset($auction_info['end_date'])) {
			$data['custom_end_date'] = $auction_info['end_date'];
		} else {
			$data['custom_end_date'] = '';
		}
		
		// $data['custom_end_date'] = date_add(date_create($data['custom_start_date']),$this->request->post['duration']);
		
		if(!$data['allow_custom_end_date']) {
			$this->load->model('auction/auction_duration');
			$filter = '';
			$data['durations'] = $this->model_auction_auction_duration->getAllDurations($filter);
		}
		
		if (isset($this->request->post['duration'])) {
			$data['duration'] = $this->request->post['duration'];
		} elseif (isset($auction_info['duration'])) {
			$data['duration'] = $auction_info['duration'];
		} else {
			$data['duration'] = '';
		}
		
		// Bid Increments
		
		$this->load->model('auction/bid_increments');
		$data['bid_increments'] = $this->model_auction_bid_increments->getAllIncrements($filter);
		
		
		
		
		
		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($auction_info)) {
			$data['shipping'] = $auction_info['shipping'];
		} else {
			$data['shipping'] = 0;
		}
		
		if (isset($this->request->post['shipping_cost'])) {
			$data['shipping_cost'] = $this->request->post['shipping_cost'];
		} elseif (!empty($auction_info)) {
			$data['shipping_cost'] = $auction_info['shipping_cost'];
		} else {
			$data['shipping_cost'] = 0;
		}
		
		if (isset($this->request->post['international_shipping'])) {
			$data['international_shipping'] = $this->request->post['international_shipping'];
		} elseif (!empty($auction_info)) {
			$data['international_shipping'] = $auction_info['international_shipping'];
		} else {
			$data['international_shipping'] = 0;
		}
		
		if (isset($this->request->post['additional_shipping'])) {
			$data['additional_shipping'] = $this->request->post['additional_shipping'];
		} elseif (!empty($auction_info)) {
			$data['additional_shipping'] = $auction_info['additional_shipping'];
		} else {
			$data['additional_shipping'] = 0;
		}

		/*if (isset($this->request->post['start_date'])) {
			$data['start_date'] = $this->request->post['start_date'];
		} elseif (!empty($auction_info)) {
			$data['start_date'] = ($auction_info['start_date'] != '0000-00-00') ? $auction_info['start_date'] : '';
		} else {
			$data['start_date'] = date('Y-m-d H:i:s');
		}*/


		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}


		


		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['auction_category'])) {
			$categories = $this->request->post['auction_category'];
		} elseif (isset($this->request->get['auction_id'])) {
			$categories = $this->model_catalog_auction->getAuctionCategories($this->request->get['auction_id']);
		} else {
			$categories = array();
		}

		$data['auction_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['auction_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}


		// Sellers Information
		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();


		$this->load->model('customer/customer');
		if(isset($auction_info['customer_id'])){
		$customer_info = $this->model_customer_customer->getCustomer($auction_info['customer_id']);
		$customer_address = $this->model_customer_customer->getAddress($customer_info['address_id']);

		$data['seller_info'] = $customer_address;
		$data['seller_info']['customer_group'] = $this->model_customer_customer_group->getCustomerGroup($customer_info['customer_group_id']);
		$data['seller_info']['email'] = $customer_info['email'];
		} else {
			$data['seller_info'] = array(
				'firstname' => '',
				'lastname' => '',
				'email' => '',
				'address_1' => '',
				'address_2' => '',
				'customer_group' => array(
					'name' => ''),
				'city' => '',
				'zone' => '',
				'country' => '',
				'postcode' => '',
				'customer_id' => ''
			);
		}

		// Image
		$data['allow_extra_images'] = $this->config->get('config_auction_picture_gallery');
		$data['max_additional_images'] = $this->config->get('config_auction_max_gallery_pictures');
		
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($auction_info)) {
			$data['image'] = $auction_info['main_image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($auction_info) && is_file(DIR_IMAGE . $auction_info['main_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($auction_info['main_image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['auction_image'])) {
			$auction_images = $this->request->post['auction_image'];
		} elseif (isset($this->request->get['auction_id'])) {
			$auction_images = $this->model_catalog_auction->getAuctionImages($this->request->get['auction_id']);
		} else {
			$auction_images = array();
		}

		$data['auction_images'] = array();

		foreach ($auction_images as $auction_image) {
			if (is_file(DIR_IMAGE . $auction_image['image'])) {
				$image = $auction_image['image'];
				$thumb = $auction_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['auction_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $auction_image['sort_order']
			);
		}
		
		//debuglog($data['auction_images']);
		//debuglog(count($auction_images));
		// Fees
		
		$fees_data = array(
			'store'				=> (isset($auction_info['store_id'])) ? $auction_info['store_id'] :'0',
			'bolded_item'		=> (isset($auction_options['bolded_item'])) ? $auction_options['bolded_item'] : '0',
			'on_carousel'		=> (isset($auction_options['on_carousel'])) ? $auction_options['on_carousel'] : '0',
			'buy_now_only'		=> (isset($auction_options['buy_now_only'])) ? $auction_options['buy_now_only'] : '0',
			'featured'			=> (isset($auction_options['featured'])) ? $auction_options['featured'] : '0',
			'highlighted'		=> (isset($auction_options['highlighted'])) ? $auction_options['highlighted'] : '0',
			'slideshow'			=> (isset($auction_options['slideshow'])) ? $auction_options['slideshow'] : '0',
			'social_media'		=> (isset($auction_options['social_media'])) ? $auction_options['social_media'] : '0',
			'auto_relist'		=> (isset($auction_options['auto_relist'])) ? $auction_info['num_relist'] : '0',
			'reserve_price'		=> (isset($auction_info['reserve_price'])) ? $auction_info['reserve_price'] : '0',
			'buy_now_price'		=> (isset($auction_info['buy_now_price'])) ? $auction_info['buy_now_price'] : '0',
			'extra_images'		=> count($auction_images),
			'subtitle'			=> (isset($auction_info['subtitle'])) ? $auction_info['subtitle'] : '0' 
		);
		
		$this->load->model('auction/auction_fees');
		$data['all_fees'] = $this->model_auction_auction_fees->getAllCharges($fees_data);

		if (isset($this->request->post['auction_layout'])) {
			$data['auction_layout'] = $this->request->post['auction_layout'];
		} elseif (isset($this->request->get['auction_id'])) {
			$data['auction_layout'] = $this->model_catalog_auction->getAuctionLayouts($this->request->get['auction_id']);
		} else {
			$data['auction_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/auction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['auction_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			
		}
		
		// *************************************************************************************************************************************
		// check that the end date is later than the start date.  if custom start dates are not allowed then the start date is NOW,
		// end date must be after this.  If custom end dates are not allowed then duration must exist so that it can be added to the start date.
		// *************************************************************************************************************************************

		if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['auction_id']) && $url_alias_info['query'] != 'auction_id=' . $this->request->get['auction_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['auction_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/auction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/auction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	
	public function autocomplete() {
		$json = array();
	
		if (isset($this->request->get['filter_sellers'])) {
			
			if (isset($this->request->get['filter_sellers'])) {
				$filter_name = $this->request->get['filter_sellers'];
			} else {
				$filter_name = '';
			}
			
			if (isset($this->request->get['filter_customer_group_id'])) {
				$filter_group = $this->request->get['filter_customer_group_id'];
			} else {
				$filter_group = '';
			}
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'				=> $filter_name,
				'filter_customer_group_id'  => $filter_group,
				'start'        => 0,
				'limit'        => $limit
			);

			$this->load->model('customer/customer');

			$results = $this->model_customer_customer->getCustomers($filter_data);
			
			foreach ($results as $result) {
				
				$address = $this->model_customer_customer->getAddress($result['address_id']);

				$json[] = array(
					'customer_id'		=> $result['customer_id'],
					'seller'		=> strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'email'	=> strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
					'customer_group'	=> strip_tags(html_entity_decode($result['customer_group'], ENT_QUOTES, 'UTF-8')),
					'address1'	=> strip_tags(html_entity_decode($address['address_1'], ENT_QUOTES, 'UTF-8')),
					'address2'	=> strip_tags(html_entity_decode($address['address_2'], ENT_QUOTES, 'UTF-8')),
					'city'		=> strip_tags(html_entity_decode($address['city'], ENT_QUOTES, 'UTF-8')),
					'zone'		=> strip_tags(html_entity_decode($address['zone'], ENT_QUOTES, 'UTF-8')),
					'country'	=> strip_tags(html_entity_decode($address['country'], ENT_QUOTES, 'UTF-8')),
					'postcode'	=> strip_tags(html_entity_decode($address['postcode'], ENT_QUOTES, 'UTF-8'))
					);
			}					
		}

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/auction');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_auction->getAuctions($filter_data);

			foreach ($results as $result) {

				$json[] = array(
					//'auction_id' => $result['auction_id'],
					//'seller'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'auction_id'        => $result['auction_id'],
					'image'				=> HTTP_CATALOG . 'image/no_image.png',
					'type'				=> $result['auction_type'] ? 'Dutch':'Regular',
					'seller'            => ucwords($result['firstname'] . ' ' . $result['lastname']),
					'auction_status'    => $result['status_name'] ? $result['status_name'] : $this->language->get('text_missing'),
					'title'       		=> $result['title'],
					'date_created'      => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
					'start_date'        => date($this->language->get('date_format_short'), strtotime($result['start_date'])),
					'end_date'          => date($this->language->get('date_format_short'), strtotime($result['end_date'])),
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
