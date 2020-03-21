<?php
class ModelAuctionBidIncrements extends Model {
    
    public function getAllIncrements($data){
        
        $sql = "SELECT * FROM " . DB_PREFIX . "bid_increments ";
        
        if (isset($data['sort']) && !empty($data['sort'])) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY increment_id";
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
        
        $increment_data = $this->db->query($sql);
        return $increment_data->rows;
    }
    
    public function getTotalIncrements(){
        $sql = "SELECT COUNT(DISTINCT increment_id) AS total FROM " . DB_PREFIX . "bid_increments";
        $query = $this->db->query($sql);

		return $query->row['total'];
    }
    
    public function getIncrement($Id){
        $sql = "SELECT bid_low, bid_high, increment FROM " . DB_PREFIX . "bid_increments WHERE increment_id = '" . $this->db->escape($Id) . "'";
        $results = $this->db->query($sql);
        return $results->row;
    }
    
    public function addIncrement($data){
        $this->db->query("INSERT INTO " . DB_PREFIX . "bid_increments
        SET
		bid_low = '" . $this->db->escape($data['bid_low']) . "',
		bid_high = '" . $this->db->escape($data['bid_high']) . "',
        increment = '" . $this->db->escape($data['increment']) . "'");
    }
    
    public function deleteIncrement($id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "bid_increments WHERE increment_id = '" . $this->db->escape($id) . "'");
    }
    
    public function editIncrement($id, $data){
        $this->db->query("UPDATE " . DB_PREFIX . "bid_increments
        SET
		bid_low = '" . $this->db->escape($data['bid_low']) . "',
		bid_high = '" . $this->db->escape($data['bid_high']) . "', 
        increment = '" . $this->db->escape($data['increment']) . "'
        WHERE increment_id = '" . $this->db->escape($id) . "'");
    }
    
	public function getNextIncrement($data) {
		$this->db->query("SELECT increment FROM " . DB_PREFIX . "bid_increments
						 WHERE
						 bid_low <= '" . $data . "'
						 AND
						 bid_high >= '" . $data . "'");
		return $this->query->row;
	}
} // End of Model