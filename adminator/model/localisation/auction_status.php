<?php
class ModelLocalisationAuctionStatus extends Model {
	public function addAuctionStatus($data) {
		foreach ($data['auction_status'] as $language_id => $value) {
			if (isset($auction_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "auction_status SET auction_status_id = '" . (int)$auction_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "auction_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$auction_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('auction_status');
		
		return $auction_status_id;
	}

	public function editAuctionStatus($auction_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "auction_status WHERE auction_status_id = '" . (int)$auction_status_id . "'");

		foreach ($data['auction_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "auction_status SET auction_status_id = '" . (int)$auction_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('auction_status');
	}

	public function deleteAuctionStatus($auction_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "auction_status WHERE auction_status_id = '" . (int)$auction_status_id . "'");

		$this->cache->delete('auction_status');
	}

	public function getAuctionStatus($auction_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_status WHERE auction_status_id = '" . (int)$auction_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getAuctionStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "auction_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
		} else {
			$auction_status_data = $this->cache->get('auction_status.' . (int)$this->config->get('config_language_id'));

			if (!$auction_status_data) {
				$query = $this->db->query("SELECT auction_status_id, name FROM " . DB_PREFIX . "auction_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$auction_status_data = $query->rows;

				$this->cache->set('auction_status.' . (int)$this->config->get('config_language_id'), $auction_status_data);
			}

			return $auction_status_data;
		}
	}

	public function getAuctionStatusDescriptions($auction_status_id) {
		$auction_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_status WHERE auction_status_id = '" . (int)$auction_status_id . "'");

		foreach ($query->rows as $result) {
			$auction_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $auction_status_data;
	}

	public function getTotalAuctionStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}