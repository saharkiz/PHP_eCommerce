<?php
class ModelAuctionBidding extends Model {
    
    
    
    public function placeBid($bid) {
        debuglog("got to the place");
        $sql = "SELECT * FROM '" . DB_PREFIX . "bid_history'
        WHERE auction_id = '" . $this->db->excape($bid['auction_id']) . "'
        ORDER BY bid_date ASC";
        
        $query = $this->db->query($sql);
        $result = $query->row;
        
        if(!$result) {
            $this->db->query("INSERT INTO '" . DB_PREFIX . "bid_history'
            SET
            auction_id = '" . $this->db->escape($bid['auction_id']) . "',
            bidder_id = '" . $this->db->escape($bid['bidder_id']) . "',
            bid_date = " . NOW() . ",
            bid_amount = '" . $this->db->escape($bid['bid_amount']) . "',
            quantity = '1',
            proxy_bidder_id = '" . $this->db->escape($bid['bidder_id']) . "',
            proxy_bid_amount = '" . $this->db->escape($bid['bid_amount']) . "'");
            
            $result = $this->db->getLastId();
        }
        
        return $result;
    }
    
    public function getCurrentBid($data) {
        $this->db->query("SELECT * FROM '" . DB_PREFIX . "bid_history'
                         WHERE auction_id = '" . $data . "'
                         OREDER BY bid_id DESC");
        return $this->db->query->row;
    }
    
    
} // End of Model