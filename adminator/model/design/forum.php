<?php
class ModelDesignForum extends Model {
	
	public function addForum($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "forum SET name = '" . $this->db->escape($data['name']) . "',description = '" . $this->db->escape($data['description']) . "', username = '".$data['username']."', email = '".$data['email']."' , status = '" . (int)$data['status'] . "'");

		$forum_id = $this->db->getLastId();

		if (isset($data['forum_image'])) {
			foreach ($data['forum_image'] as $forum_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image SET forum_id = '" . (int)$forum_id . "', image = '" .  $this->db->escape($forum_image['image']) . "'");

				$forum_image_id = $this->db->getLastId();

				foreach ($forum_image['forum_image_description'] as $language_id => $forum_image_description) {				
					$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image_description SET forum_image_id = '" . (int)$forum_image_id . "', language_id = '" . (int)$language_id . "', forum_id = '" . (int)$forum_id . "', title = '" .  $this->db->escape($forum_image_description['title']) . "'");
				}
			}
		}		
	}

	public function editForum($forum_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "forum SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', status = '" . (int)$data['status'] . "' WHERE forum_id = '" . (int)$forum_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image WHERE forum_id = '" . (int)$forum_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image_description WHERE forum_id = '" . (int)$forum_id . "'");

		if (isset($data['forum_image'])) {
			foreach ($data['forum_image'] as $forum_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image SET forum_id = '" . (int)$forum_id . "', image = '" .  $this->db->escape($forum_image['image']) . "'");

				$forum_image_id = $this->db->getLastId();

				foreach ($forum_image['forum_image_description'] as $language_id => $forum_image_description) {				
					$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image_description SET forum_image_id = '" . (int)$forum_image_id . "', language_id = '" . (int)$language_id . "', forum_id = '" . (int)$forum_id . "', title = '" .  $this->db->escape($forum_image_description['title']) . "'");
				}
			}
		}			
	}
	
	public function editForumReply($forum_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "forum_reply SET reply = '" . $this->db->escape($data['reply']) . "', status = '" . (int)$data['status'] . "' WHERE id = '" . (int)$forum_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image WHERE forum_id = '" . (int)$forum_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image_description WHERE forum_id = '" . (int)$forum_id . "'");

		if (isset($data['forum_image'])) {
			foreach ($data['forum_image'] as $forum_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image SET forum_id = '" . (int)$forum_id . "', image = '" .  $this->db->escape($forum_image['image']) . "'");

				$forum_image_id = $this->db->getLastId();

				foreach ($forum_image['forum_image_description'] as $language_id => $forum_image_description) {				
					$this->db->query("INSERT INTO " . DB_PREFIX . "forum_image_description SET forum_image_id = '" . (int)$forum_image_id . "', language_id = '" . (int)$language_id . "', forum_id = '" . (int)$forum_id . "', title = '" .  $this->db->escape($forum_image_description['title']) . "'");
				}
			}
		}			
	}

	public function deleteForum($forum_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum WHERE forum_id = '" . (int)$forum_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image WHERE forum_id = '" . (int)$forum_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_reply WHERE forum_id = '" . (int)$forum_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_image_description WHERE forum_id = '" . (int)$forum_id . "'");
	}
	
	public function deleteForumReply($forum_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "forum_reply WHERE id = '" . (int)$forum_id . "'");
	}

	public function getForum($forum_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "forum WHERE forum_id = '" . (int)$forum_id . "'");

		return $query->row;
	}
	
	public function getForumReply($forum_id) {
		$query = $this->db->query("SELECT a.status,a.forum_id,a.reply,b.name FROM " . DB_PREFIX . "forum_reply AS a LEFT JOIN " . DB_PREFIX . "forum AS b ON a.forum_id = b.forum_id  WHERE a.id = '" . (int)$forum_id . "'");

		return $query->row;
	}

	public function getForums($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "forum";

		$sort_data = array(
			'name',
			'status'
		);	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}					

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getForumImages($forum_id) {
		$forum_image_data = array();

		$forum_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "forum_image WHERE forum_id = '" . (int)$forum_id . "'");

		foreach ($forum_image_query->rows as $forum_image) {
			$forum_image_description_data = array();

			$forum_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "forum_image_description WHERE forum_image_id = '" . (int)$forum_image['forum_image_id'] . "' AND forum_id = '" . (int)$forum_id . "'");

			foreach ($forum_image_description_query->rows as $forum_image_description) {			
				$forum_image_description_data[$forum_image_description['language_id']] = array('title' => $forum_image_description['title']);
			}

			$forum_image_data[] = array(
				'forum_image_description' => $forum_image_description_data,				
				'image'                    => $forum_image['image']	
			);
		}

		return $forum_image_data;
	}

	public function getTotalForums() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "forum");

		return $query->row['total'];
	}	
	public function getTotalForumsReply() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "forum_reply");

		return $query->row['total'];
	}
	
	public function getForumsReply($data = array()) {
		$sql = "SELECT a.id,a.status,a.reply,a.forum_id,b.name FROM " . DB_PREFIX . "forum_reply AS a LEFT JOIN "  . DB_PREFIX . "forum AS b ON a.forum_id = b.forum_id ";

		$sort_data = array(
			'name',
			'status'
		);	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}					

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getEmail($id) {
		$query = $this->db->query("SELECT email FROM " . DB_PREFIX . "user WHERE user_id=".$id);
		return $query->row['email'];
	} 
}
?>