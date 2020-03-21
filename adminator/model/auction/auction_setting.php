<?php
class ModelAuctionAuctionSetting extends Model {
    
    public function getAuctionTypes() {
		$auction_type_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_types WHERE type = '0'");
		foreach ($query->rows as $result) {
			$auction_type_data[] = $result;
		}

		return $auction_type_data;
	}
    
    public function getAuctionStatuses() {
        $auction_status_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_status");
        foreach($query->rows as $result) {
            $auction_status_data[]=$result;
        }
        return $auction_status_data;
    }
    

    
} // End of Model