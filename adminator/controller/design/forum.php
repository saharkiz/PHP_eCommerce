<?php 
class ControllerDesignForum extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');

		$this->getList();
		
	}

	public function insert() {
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$username = $this->user->getUserName();
			$user_id = $this->user->session->data['user_id'];
			$getEmail = $this->model_design_forum->getEmail($user_id);
			
			$this->request->post['username'] = $username;
			$this->request->post['email']    = $getEmail;
			
			$this->model_design_forum->addForum($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
	

	public function update() {
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');
		$data['text_form'] = 'Edit Forum Topic';

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_design_forum->editForum($this->request->get['forum_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			
		}

		$this->getForm();
	}
	
	public function updateReply() {
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_forum->editForumReply($this->request->get['forum_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			
			
		}

		$this->getFormReply();
	}

	public function delete() {
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $forum_id) {
				$this->model_design_forum->deleteForum($forum_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			
		}

		$this->getList();
	}
	
	public function deleteReply() {
		$this->load->language('design/forum');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/forum');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $forum_id) {
				$this->model_design_forum->deleteForumReply($forum_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			
		}

		$this->getForumReply();
	}

	protected function getList() {
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$data['insert'] = $this->url->link('design/forum/insert', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('design/forum/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['forums'] = array();

		$data1 = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$forum_total = $this->model_design_forum->getTotalForums();

		$results = $this->model_design_forum->getForums($data1);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('design/forum/update', 'user_token=' . $this->session->data['user_token'] . '&forum_id=' . $result['forum_id'] . $url, 'SSL')
			);

			if($result['status'] == 0) {
				$status = "Disabled";
			}else if($result['status'] == 1) {
				$status = "Enabled";	
			}else {
				$status = "Closed";
			}
			
			$data['forums'][] = array(
				'forum_id' => $result['forum_id'],
				'name'      => $result['name'],	
				'status'    => $status,				
				'selected'  => isset($this->request->post['selected']) && in_array($result['forum_id'], $this->request->post['selected']),				
				'action'    => $action
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');	

		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_delete'] = $this->language->get('button_delete');

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $forum_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($forum_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($forum_total - $this->config->get('config_limit_admin'))) ? $forum_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $forum_total, ceil($forum_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');



		$this->response->setOutput($this->load->view('design/forum_list', $data));

	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_closed'] = $this->language->get('text_closed');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');			

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_image'] = $this->language->get('entry_image');		
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_forum'] = $this->language->get('button_add_forum');
		$data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['forum_image'])) {
			$data['error_forum_image'] = $this->error['forum_image'];
		} else {
			$data['error_forum_image'] = array();
		}	

		$url = '';

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
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['forum_id'])) { 
			$data['action'] = $this->url->link('design/forum/insert', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('design/forum/update', 'user_token=' . $this->session->data['user_token'] . '&forum_id=' . $this->request->get['forum_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		if (isset($this->request->get['forum_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$forum_info = $this->model_design_forum->getForum($this->request->get['forum_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($forum_info)) {
			$data['name'] = $forum_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($forum_info)) {
			$data['description'] = $forum_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($forum_info)) {
			$data['status'] = $forum_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['forum_image'])) {
			$forum_images = $this->request->post['forum_image'];
		} elseif (isset($this->request->get['forum_id'])) {
			$forum_images = $this->model_design_forum->getForumImages($this->request->get['forum_id']);	
		} else {
			$forum_images = array();
		}

		$data['forum_images'] = array();

		foreach ($forum_images as $forum_image) {
			if ($forum_image['image'] && file_exists(DIR_IMAGE . $forum_image['image'])) {
				$image = $forum_image['image'];
			} else {
				$image = 'no_image.jpg';
			}			

			$data['forum_images'][] = array(
				'forum_image_description' => $forum_image['forum_image_description'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($image, 100, 100)
			);	
		} 

		$data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);		
		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');



		$this->response->setOutput($this->load->view('design/forum_form', $data));

	}
	
	protected function getFormReply() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_closed'] = $this->language->get('text_closed');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');			

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_reply'] = $this->language->get('entry_reply');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_image'] = $this->language->get('entry_image');		
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_forum'] = $this->language->get('button_add_forum');
		$data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['reply'])) {
			$data['error_reply'] = $this->error['reply'];
		} else {
			$data['error_reply'] = '';
		}

		if (isset($this->error['forum_image'])) {
			$data['error_forum_image'] = $this->error['forum_image'];
		} else {
			$data['error_forum_image'] = array();
		}	

		$url = '';

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
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['forum_id'])) { 
			$data['action'] = $this->url->link('design/forum/insert', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('design/forum/updateReply', 'user_token=' . $this->session->data['user_token'] . '&forum_id=' . $this->request->get['forum_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		if (isset($this->request->get['forum_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$forum_info = $this->model_design_forum->getForumReply($this->request->get['forum_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($forum_info)) {
			$data['name'] = $forum_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['reply'])) {
			$data['reply'] = $this->request->post['reply'];
		} elseif (!empty($forum_info)) {
			$data['reply'] = $forum_info['reply'];
		} else {
			$data['reply'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($forum_info)) {
			$data['status'] = $forum_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['forum_image'])) {
			$forum_images = $this->request->post['forum_image'];
		} elseif (isset($this->request->get['forum_id'])) {
			$forum_images = $this->model_design_forum->getForumImages($this->request->get['forum_id']);	
		} else {
			$forum_images = array();
		}

		$data['forum_images'] = array();

		foreach ($forum_images as $forum_image) {
			if ($forum_image['image'] && file_exists(DIR_IMAGE . $forum_image['image'])) {
				$image = $forum_image['image'];
			} else {
				$image = 'no_image.jpg';
			}			

			$data['forum_images'][] = array(
				'forum_image_description' => $forum_image['forum_image_description'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($image, 100, 100)
			);	
		} 

		$data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);		
		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');



		$this->response->setOutput($this->load->view('design/forum_form_reply', $data));
		

	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/forum')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->post['forum_image'])) {
			foreach ($this->request->post['forum_image'] as $forum_image_id => $forum_image) {
				foreach ($forum_image['forum_image_description'] as $language_id => $forum_image_description) {
					if ((utf8_strlen($forum_image_description['title']) < 2) || (utf8_strlen($forum_image_description['title']) > 64)) {
						$this->error['forum_image'][$forum_image_id][$language_id] = $this->language->get('error_title'); 
					}					
				}
			}	
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/forum')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	public function getForumReply() {
	
		$this->load->language('design/forum');

		$this->document->setTitle('Forum Reply');

		$this->load->model('design/forum');
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('design/forum', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);


		$data['breadcrumbs'][] = array(
			'text'      => 'Forum Reply',
			'href'      => $this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$data['insert'] = $this->url->link('design/forum/insertReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('design/forum/deleteReply', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['forums'] = array();

		$data1 = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$forum_total = $this->model_design_forum->getTotalForumsReply();

		$results = $this->model_design_forum->getForumsReply($data1);
		
		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('design/forum/updateReply', 'user_token=' . $this->session->data['user_token'] . '&forum_id=' . $result['id'] . $url, 'SSL')
			);

			if($result['status'] == 0) {
				$status = "Disabled";
			}else if($result['status'] == 1) {
				$status = "Enabled";	
			}else {
				$status = "Closed";
			}
			
			$data['forums'][] = array(
				'forum_id' => $result['id'],
				'name'      => $result['name'],	
				'reply'      => $result['reply'],	
				'status'    => $status,				
				'selected'  => isset($this->request->post['selected']) && in_array($result['forum_id'], $this->request->post['selected']),				
				'action'    => $action
			);
		}

		$data['heading_title'] = 'Forum Reply';

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');	

		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_delete'] = $this->language->get('button_delete');

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $forum_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('design/forum/getForumReply', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($forum_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($forum_total - $this->config->get('config_limit_admin'))) ? $forum_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $forum_total, ceil($forum_total / $this->config->get('config_limit_admin')));
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');



		$this->response->setOutput($this->load->view('design/forum_reply_list', $data));

	
	}
}
?>