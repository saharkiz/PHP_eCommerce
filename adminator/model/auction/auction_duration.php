<?php
class ModelAuctionAuctionDuration extends Model {
    
    public function getAllDurations($data){
        
        $sql = "SELECT * FROM " . DB_PREFIX . "durations ";
        
        if (isset($data['sort']) && !empty($data['sort'])) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY duration";
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
        
        $duration_data = $this->db->query($sql);
        return $duration_data->rows;
    }
    
    public function getTotalDurations(){
        $sql = "SELECT COUNT(DISTINCT duration_id) AS total FROM " . DB_PREFIX . "durations";
        $query = $this->db->query($sql);

		return $query->row['total'];
    }
    
    public function getDuration($Id){
        $sql = "SELECT duration, description FROM " . DB_PREFIX . "durations WHERE duration_id = '" . $this->db->escape($Id) . "'";
        $results = $this->db->query($sql);
        return $results->row;
    }
    
    public function addDuration($data){
        $this->db->query("INSERT INTO " . DB_PREFIX . "durations
        SET duration = '" . $this->db->escape($data['duration']) . "',
        description = '" . $this->db->escape($data['description']) . "'");
    }
    
    public function deleteDuration($id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "durations WHERE duration_id = '" . $this->db->escape($id) . "'");
    }
    
    public function editDuration($id, $data){
        $this->db->query("UPDATE " . DB_PREFIX . "durations
        SET duration = '" . $this->db->escape($data['duration']) . "',
        description = '" . $this->db->escape($data['description']) . "'
        WHERE duration_id = '" . $this->db->escape($id) . "'");
    }
    
} // End of Model