<?php
class ControllerAuctionAuctionSetting extends Controller {
    
	private $error = array();

	public function index() {
		$this->load->language('auction/auction_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			//debuglog($this->request->post);
			$this->model_setting_setting->editSetting('config_auction', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('auction/auction_setting', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_auction'] = $this->language->get('text_auction');
		$data['text_auction_status'] = $this->language->get('text_auction_status');
		$data['text_auction_display'] = $this->language->get('text_auction_display');
		$data['text_auction_options'] = $this->language->get('text_auction_options');
		$data['text_auction_gallery'] = $this->language->get('text_auction_gallery');
		
		$data['entry_limit_admin'] = $this->language->get('entry_limit_admin');
		$data['entry_auction_count'] = $this->language->get('entry_auction_count');
		$data['entry_auction_dutch'] = $this->language->get('entry_auction_dutch');
		$data['entry_auction_extension'] = $this->language->get('entry_auction_extension');
		$data['entry_auction_extension_left'] = $this->language->get('entry_auction_extension_left');
		$data['entry_auction_extension_for'] = $this->language->get('entry_auction_extension_for');
		$data['entry_auction_countdown'] = $this->language->get('entry_auction_countdown');
		$data['entry_auction_countdown_time'] = $this->language->get('entry_auction_countdown_time');
		$data['entry_auction_created_status'] = $this->language->get('entry_auction_created_status');
		$data['entry_auction_open_status'] = $this->language->get('entry_auction_open_status');
		$data['entry_auction_closed_status'] = $this->language->get('entry_auction_closed_status');
		$data['entry_auction_suspended_status'] = $this->language->get('entry_auction_suspended_status');
		$data['entry_auction_moderation_status'] = $this->language->get('entry_auction_moderation_status');
		
		$data['entry_auction_picture_gallery'] = $this->language->get('entry_auction_picture_gallery');
		$data['entry_auction_max_gallery_pictures'] = $this->language->get('entry_auction_max_gallery_pictures');
		$data['entry_auction_max_picture_size'] = $this->language->get('entry_auction_max_picture_size');
		
		$data['entry_auction_proxy'] = $this->language->get('entry_auction_proxy');
		$data['entry_auction_custom_start_date'] = $this->language->get('entry_auction_custom_start_date');
		$data['entry_auction_custom_end_date'] = $this->language->get('entry_auction_custom_end_date');
		$data['entry_auction_bid_increments'] = $this->language->get('entry_auction_bid_increments');
		$data['entry_auction_subtitles'] = $this->language->get('entry_auction_subtitles');
		$data['entry_auction_additional_cat'] = $this->language->get('entry_auction_additional_cat');
		$data['entry_auction_auto_relist'] = $this->language->get('entry_auction_auto_relist');
		$data['entry_auction_max_relists'] = $this->language->get('entry_auction_max_relists');
		
		$data['help_limit_admin'] = $this->language->get('help_limit_admin');
		$data['help_auction_count'] = $this->language->get('help_auction_count');
		$data['help_auction_dutch'] = $this->language->get('help_auction_dutch');
		$data['help_auction_extension'] = $this->language->get('help_auction_extension');
		$data['help_auction_extension_left'] = $this->language->get('help_auction_extension_left');
		$data['help_auction_extension_for'] = $this->language->get('help_auction_extension_for');
		$data['help_auction_countdown'] = $this->language->get('help_auction_countdown');
		$data['help_auction_countdown_time'] = $this->language->get('help_auction_countdown_time');
		$data['help_auction_created_status'] = $this->language->get('help_auction_created_status');
		$data['help_auction_open_status'] = $this->language->get('help_auction_open_status');
		$data['help_auction_closed_status'] = $this->language->get('help_auction_closed_status');
		$data['help_auction_suspended_status'] = $this->language->get('help_auction_suspended_status');
		$data['help_auction_moderation_status'] = $this->language->get('help_auction_moderation_status');
		
		$data['help_auction_picture_gallery'] = $this->language->get('help_auction_picture_gallery');
		$data['help_auction_max_gallery_pictures'] = $this->language->get('help_auction_max_gallery_pictures');
		$data['help_auction_max_picture_size'] = $this->language->get('help_auction_max_picture_size');
		
		$data['help_auction_proxy'] = $this->language->get('help_auction_proxy');
		$data['help_auction_custom_start_date'] = $this->language->get('help_auction_custom_start_date');
		$data['help_auction_custom_end_date'] = $this->language->get('help_auction_custom_end_date');
		$data['help_auction_bid_increments'] = $this->language->get('help_auction_bid_increments');
		$data['help_auction_subtitles'] = $this->language->get('help_auction_subtitles');
		$data['help_auction_additional_cat'] = $this->language->get('help_auction_additional_cat');
		$data['help_auction_auto_relist'] = $this->language->get('help_auction_auto_relist');
		$data['help_auction_max_relists'] = $this->language->get('help_auction_max_relists');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['tab_auction'] = $this->language->get('tab_auction');
		$data['tab_display'] = $this->language->get('tab_display');
		$data['tab_option'] = $this->language->get('tab_option');
		
        // Auctions Panel
        
        if (isset($this->request->post['config_auction_limit_admin'])) {
			$data['config_auction_limit_admin'] = $this->request->post['config_auction_limit_admin'];
		} else {
			$data['config_auction_limit_admin'] = $this->config->get('config_auction_limit_admin');
		}

		if (isset($this->request->post['config_auction_count'])) {
			$data['config_auction_count'] = $this->request->post['config_auction_count'];
		} else {
			$data['config_auction_count'] = $this->config->get('config_auction_count');
		}
		
		if (isset($this->request->post['config_auction_dutch'])) {
			$data['config_auction_dutch'] = $this->request->post['config_auction_dutch'];
		} else {
			$data['config_auction_dutch'] = $this->config->get('config_auction_dutch');
		}
		
		if (isset($this->request->post['config_auction_extension'])) {
			$data['config_auction_extension'] = $this->request->post['config_auction_extension'];
		} else {
			$data['config_auction_extension'] = $this->config->get('config_auction_extension');
		}
		
		if (isset($this->request->post['config_auction_extension_left'])) {
			$data['config_auction_extension_left'] = $this->request->post['config_auction_extension_left'];
		} else {
			$data['config_auction_extension_left'] = $this->config->get('config_auction_extension_left');
		}
        
		if (isset($this->request->post['config_auction_extension_for'])) {
			$data['config_auction_extension_for'] = $this->request->post['config_auction_extension_for'];
		} else {
			$data['config_auction_extension_for'] = $this->config->get('config_auction_extension_for');
		}
		
		if (isset($this->request->post['config_auction_countdown'])) {
			$data['config_auction_countdown'] = $this->request->post['config_auction_countdown'];
		} else {
			$data['config_auction_countdown'] = $this->config->get('config_auction_countdown');
		}
		
		if (isset($this->request->post['config_auction_countdown_time'])) {
			$data['config_auction_countdown_time'] = $this->request->post['config_auction_countdown_time'];
		} else {
			$data['config_auction_countdown_time'] = $this->config->get('config_auction_countdown_time');
		}
		
		if (isset($this->request->post['config_auction_created_status'])) {
			$data['config_auction_created_status'] = $this->request->post['config_auction_created_status'];
		} else {
			$data['config_auction_created_status'] = $this->config->get('config_auction_created_status');
		}
		
		if (isset($this->request->post['config_auction_closed_status'])) {
			$data['config_auction_closed_status'] = $this->request->post['config_auction_closed_status'];
		} else {
			$data['config_auction_closed_status'] = $this->config->get('config_auction_closed_status');
		}
		
		if (isset($this->request->post['config_auction_open_status'])) {
			$data['config_auction_open_status'] = $this->request->post['config_auction_open_status'];
		} else {
			$data['config_auction_open_status'] = $this->config->get('config_auction_open_status');
		}
		
		if (isset($this->request->post['config_auction_suspended_status'])) {
			$data['config_auction_suspended_status'] = $this->request->post['config_auction_suspended_status'];
		} else {
			$data['config_auction_suspended_status'] = $this->config->get('config_auction_suspended_status');
		}
		
		if (isset($this->request->post['config_auction_moderation_status'])) {
			$data['config_auction_moderation_status'] = $this->request->post['config_auction_moderation_status'];
		} else {
			$data['config_auction_moderation_status'] = $this->config->get('config_auction_moderation_status');
		}

		$this->load->model('localisation/auction_status');
		
		$data['auction_statuses'] = $this->model_localisation_auction_status->getAuctionStatuses();
		
		// Display Panel
		
		
		if (isset($this->request->post['config_auction_picture_gallery'])) {
			$data['config_auction_picture_gallery'] = $this->request->post['config_auction_picture_gallery'];
		} else {
			$data['config_auction_picture_gallery'] = $this->config->get('config_auction_picture_gallery');
		}
		
		if (isset($this->request->post['config_auction_max_gallery_pictures'])) {
			$data['config_auction_max_gallery_pictures'] = $this->request->post['config_auction_max_gallery_pictures'];
		} else {
			$data['config_auction_max_gallery_pictures'] = $this->config->get('config_auction_max_gallery_pictures');
		}
		
		if (isset($this->request->post['config_auction_max_picture_size'])) {
			$data['config_auction_max_picture_size'] = $this->request->post['config_auction_max_picture_size'];
		} else {
			$data['config_auction_max_picture_size'] = $this->config->get('config_auction_max_picture_size');
		}
		
		
		// Options Panel
		
		if (isset($this->request->post['config_auction_proxy'])) {
			$data['config_auction_proxy'] = $this->request->post['config_auction_proxy'];
		} else {
			$data['config_auction_proxy'] = $this->config->get('config_auction_proxy');
		}
		
		if (isset($this->request->post['config_auction_custom_start_date'])) {
			$data['config_auction_custom_start_date'] = $this->request->post['config_auction_custom_start_date'];
		} else {
			$data['config_auction_custom_start_date'] = $this->config->get('config_auction_custom_start_date');
		}
		
		if (isset($this->request->post['config_auction_custom_end_date'])) {
			$data['config_auction_custom_end_date'] = $this->request->post['config_auction_custom_end_date'];
		} else {
			$data['config_auction_custom_end_date'] = $this->config->get('config_auction_custom_end_date');
		}
		
		if (isset($this->request->post['config_auction_bid_increments'])) {
			$data['config_auction_bid_increments'] = $this->request->post['config_auction_bid_increments'];
		} else {
			$data['config_auction_bid_increments'] = $this->config->get('config_auction_bid_increments');
		}
		
		if (isset($this->request->post['config_auction_subtitles'])) {
			$data['config_auction_subtitles'] = $this->request->post['config_auction_subtitles'];
		} else {
			$data['config_auction_subtitles'] = $this->config->get('config_auction_subtitles');
		}
		
		if (isset($this->request->post['config_auction_additional_cat'])) {
			$data['config_auction_additional_cat'] = $this->request->post['config_auction_additional_cat'];
		} else {
			$data['config_auction_additional_cat'] = $this->config->get('config_auction_additional_cat');
		}
		
		if (isset($this->request->post['config_auction_auto_relist'])) {
			$data['config_auction_auto_relist'] = $this->request->post['config_auction_auto_relist'];
		} else {
			$data['config_auction_auto_relist'] = $this->config->get('config_auction_auto_relist');
		}
		
		if (isset($this->request->post['config_auction_max_relists'])) {
			$data['config_auction_max_relists'] = $this->request->post['config_auction_max_relists'];
		} else {
			$data['config_auction_max_relists'] = $this->config->get('config_auction_max_relists');
		}
		
		// Errors
		
        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
        
        if (isset($this->error['limit_admin'])) {
			$data['error_limit_admin'] = $this->error['limit_admin'];
		} else {
			$data['error_limit_admin'] = '';
		}
		
		if (isset($this->error['extension_left'])) {
			$data['error_auction_extension_left'] = $this->error['extension_left'];
		} else {
			$data['error_auction_extension_left'] = '';
		}
		
		if (isset($this->error['extension_for'])) {
			$data['error_auction_extension_for'] = $this->error['extension_for'];
		} else {
			$data['error_auction_extension_for'] = '';
		}
		
		if (isset($this->error['extension_countdown'])) {
			$data['error_auction_countdown_time'] = $this->error['extension_countdown'];
		} else {
			$data['error_auction_countdown_time'] = '';
		}
		
		if (isset($this->error['relist_max'])) {
			$data['error_auction_max_relists'] = $this->error['relist_max'];
		} else {
			$data['error_auction_max_relists'] = '';
		}
		
		
		if (isset($this->error['gallery_max'])) {
			$data['error_auction_max_gallery_pictures'] = $this->error['gallery_max'];
		} else {
			$data['error_auction_max_gallery_pictures'] = '';
		}
		
		if (isset($this->error['picture_max'])) {
			$data['error_auction_max_picture_size'] = $this->error['picture_max'];
		} else {
			$data['error_auction_max_picture_size'] = '';
		}
		
		
        
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('auction/auction_setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('auction/auction_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

		$data['token'] = $this->session->data['user_token'];

        
        
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/auction_setting', $data));
	}
    
    
    protected function validate() {
		if (!$this->user->hasPermission('modify', 'auction/auction_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
        if (!$this->request->post['config_auction_limit_admin']) {
			$this->error['limit_admin'] = $this->language->get('error_limit');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		if (!$this->request->post['config_auction_extension_left'] && $this->request->post['config_auction_extension']) {
			$this->error['extension_left'] = $this->language->get('error_auction_extension_left');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		if (!$this->request->post['config_auction_extension_for'] && $this->request->post['config_auction_extension']) {
			$this->error['extension_for'] = $this->language->get('error_auction_extension_for');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		if (!$this->request->post['config_auction_countdown_time'] && $this->request->post['config_auction_countdown']) {
			$this->error['extension_countdown'] = $this->language->get('error_auction_countdown_time');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		if (!$this->request->post['config_auction_max_relists'] && $this->request->post['config_auction_auto_relist']) {
			$this->error['relist_max'] = $this->language->get('error_auction_max_relists');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		
		if (!$this->request->post['config_auction_max_gallery_pictures'] && $this->request->post['config_auction_picture_gallery']) {
			$this->error['gallery_max'] = $this->language->get('error_auction_max_gallery_pictures');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		if (!$this->request->post['config_auction_max_picture_size'] && $this->request->post['config_auction_picture_gallery']) {
			$this->error['picture_max'] = $this->language->get('error_auction_max_picture_size');
			$this->error['warning'] = $this->language->get('error_general');
		}
		
		
        	return !$this->error;
	}
    
    
	
}