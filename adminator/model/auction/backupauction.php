<?php
class ModelAuctionAuction extends Model {
    
	// Add an Auction
	public function addAuction($data) {
		debuglog("in the model now");
		debuglog($data);
		$this->db->query("INSERT INTO " . DB_PREFIX . "auctions
						 SET
						 store_id = '" . $this->db->escape($data['store_id']) . "',
						 customer_id = '" . $this->db->escape($data['customer_id']) . "',
						 auction_type = '" . $this->db->escape($data['auction_type']) . "',
						 status = '" . $this->db->escape($data['status']) . "',
						 date_created = NOW()");

		$auction_id = $this->db->getLastId();
		debuglog($auction_id);
		$this->db->query("INSERT INTO " . DB_PREFIX . "auction_details 
						 SET
						 auction_id = '" . (int)$auction_id . "',
						 title = '" . $this->db->escape($data['title']) . "',
						 subtitle = '" . $this->db->escape($data['subtitle']) . "',
						 start_date = '" . $this->db->escape($data['start_date']) . "',
						 end_date = '" . $this->db->escape($data['end_date']) . "',
						 min_bid = '" . $this->db->escape($data['min_bid']) . "',
						 shipping_cost = '" . $this->db->escape($data['shipping_cost']) . "',
						 additional_shipping = '" . $this->db->escape($data['additional_shipping']) . "',
						 reserve_price = '" . $this->db->escape($data['reserve_price']) . "',
						 increment = '" . $this->db->escape($data['increment']) . "',
						 shipping = '" . $this->db->escape($data['shipping']) . "',
						 payment = '" . $this->db->escape($data['payment']) . "',
						 international_shipping = '" . $this->db->escape($data['international_shipping']) . "',
						 initial_quantity = '" . $this->db->escape($data['initial_quantity']) . "',
						 buy_now_price = '" . $this->db->escape($data['buy_now_price']) . "'
						 ");
		
		$this->db->query("INSERT INTO ". DB_PREFIX . "auction_options
						 SET
						 auction_id = '" . (int)$auction_id . "',
						 proxy_bidding = '" . $this->db->escape($data['proxy_bidding']) . "',
						 custom_start_date = '" . $this->db->escape($data['custom_start_date']) . "',
						 custom_end_date = '" . $this->db->escape($data['custom_end_date']) . "',
						 custom_bid_increments = '" . $this->db->escape($data['custom_bid_increments']) . "',
						 bolded_item = '" . $this->db->escape($data['bolded_item']) . "',
						 on_carousel = '" . $this->db->escape($data['on_carousel']) . "',
						 buy_now = '" . $this->db->escape($data['buy_now']) . "',
						 featured = '" . $this->db->escape($data['featured']) . "',
						 highlighted = '" . $this->db->escape($data['highlighted']) . "',
						 slideshow = '" . $this->db->escape($data['slide_show']) . "',
						 social_media = '" . $this->db->escape($data['social_media']) . "'
						 ");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "auction SET image = '" . $this->db->escape($data['image']) . "' WHERE auction_id = '" . (int)$auction_id . "'");
		}

		foreach ($data['auction_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "auction_description
							 SET
							 auction_id = '" . (int)$auction_id . "',
							 language_id = '" . (int)$language_id . "',
							 name = '" . $this->db->escape($value['name']) . "',
							 description = '" . $this->db->escape($value['description']) . "',
							 tag = '" . $this->db->escape($value['tag']) . "',
							 meta_title = '" . $this->db->escape($value['meta_title']) . "',
							 meta_description = '" . $this->db->escape($value['meta_description']) . "',
							 meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['auction_store'])) {
			foreach ($data['auction_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "auction_to_store SET auction_id = '" . (int)$auction_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['auction_category'])) {
			foreach ($data['auction_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "auction_to_category SET auction_id = '" . (int)$auction_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
		
		if (isset($data['auction_layout'])) {
			foreach ($data['auction_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "auction_to_layout SET auction_id = '" . (int)$auction_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
		
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'auction_id=" . (int)$auction_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
		
		/* old product stuff for reference
		

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}
		

		$this->cache->delete('auction');
*/
		return $auction_id;
	}
	
	
	
	
	
    public function getAuctions($data = array(), $store_id = 0) {
        $sql = "SELECT a.auction_id AS auction_id, a.date_created AS date_created,
        aus.name AS status_name,
        CONCAT(c.firstname, ' ', c.lastname) AS seller,
         ad.title AS title, ad.start_date AS start_date, ad.end_date AS end_date  
        FROM " . DB_PREFIX . "auctions a  
         LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = a.customer_id) 
         LEFT JOIN " . DB_PREFIX . "auction_status aus ON (aus.auction_status_id = a.status AND aus.language_id = '" . (int)$this->config->get('config_language_id') . "') 
         LEFT JOIN " . DB_PREFIX . "auction_details ad ON (ad.auction_id = a.auction_id)";
        
        $auction_data = array();
        
        if (isset($data['filter_auction_status'])) {
			$implode = array();
            
			$auction_statuses = explode(',', $data['filter_auction_status']);

			foreach ($auction_statuses as $auction_status_id) {
				$implode[] = "a.status = '" . (int)$auction_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE a.status > '0'";
		}
        
        if (!empty($data['filter_auction_id'])) {
			$sql .= " AND a.auction_id = '" . (int)$data['filter_auction_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_created'])) {
			$sql .= " AND DATE(a.date_created) = DATE('" . $this->db->escape($data['filter_date_created']) . "')";
		}

		$sort_data = array(
			'auction_id',
			'seller',
			'status',
			'date_created'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.auction_id";
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
//debuglog($sql);
		$query = $this->db->query($sql);

		return $query->rows;
	}
    
    public function getAuction($auction_id, $store_id = 0) {
        $auction_data = array();
        $sql = "SELECT a.store_id, a.customer_id, a.auction_type, a.date_created, a.status, a.relisted, a.num_bids, a.current_fee, a.winning_bid, 
		ad.*,
		ades.language_id, ades.name, ades.description, ades.tag, ades.meta_title, ades.meta_description, ades.meta_keyword,
		ao.proxy_bidding, ao.custom_start_date, ao.custom_end_date, ao.custom_end_date, ao.custom_bid_increments, ao.bolded_item, ao.on_carousel, ao.buy_now, ao.extra_category, ao.featured, ao.highlighted, ao.slideshow, ao.social_media, 
		 ap.image, acs.name AS status, c.customer_group_id, c.firstname, c.lastname, c.email, c.telephone, c.address_id 
		FROM " . DB_PREFIX . "auctions a 
		 LEFT JOIN " . DB_PREFIX . "auction_details ad ON (ad.auction_id = a.auction_id)
		 LEFT JOIN " . DB_PREFIX . "auction_description ades ON (ades.auction_id = a.auction_id) 
		 LEFT JOIN " . DB_PREFIX . "auction_photos ap ON (ap.auction_id = a.auction_id)
		  LEFT JOIN " . DB_PREFIX . "auction_status acs ON (acs.auction_status_id = a.status AND acs.language_id = ades.language_id)
		  LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = a.customer_id)
		  LEFT JOIN " . DB_PREFIX . "auction_options ao ON (ao.auction_id = a.auction_id) 
		   WHERE a.auction_id = '" . (int)$auction_id . "' AND a.store_id = '" . (int)$store_id . "'";

		$sort_data = array(
			'a.auction_id',
			'ad.customer_id',
			'a.status',
			'a.date_created'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.auction_id";
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

		return $query->row;		   
		
        
    }
    
	public function getAuctionCategories($auction_id) {
		$auction_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_to_category WHERE auction_id = '" . (int)$auction_id . "'");

		foreach ($query->rows as $result) {
			$auction_category_data[] = $result['category_id'];
		}

		return $auction_category_data;
	}
	
  /*  public function getTotalAuctions($data = array()) {
        
        if(!empty($data['dashboard'])) {
            $auctions = array();
            $query = $this->db->query("SELECT COUNT(auction_id) AS other FROM "  . DB_PREFIX . "auctions WHERE status = 0");
            $result = $query->row;
            array_push($auctions, $result['other']);
            $query = $this->db->query("SELECT COUNT(auction_id) AS open FROM "  . DB_PREFIX . "auctions WHERE status = 1");
            $result = $query->row;
            array_push($auctions, $result['open']);
            $query = $this->db->query("SELECT COUNT(auction_id) AS closed FROM "  . DB_PREFIX . "auctions WHERE status = 2");
            $result = $query->row;
            array_push($auctions, $result['closed']);
            $query = $this->db->query("SELECT COUNT(auction_id) AS suspended FROM "  . DB_PREFIX . "auctions WHERE status = 3");
            $result = $query->row;
            array_push($auctions, $result['suspended']);
            return $auctions;
        }
        
        $sql = "SELECT COUNT(DISTINCT a.auction_id) AS total FROM " . DB_PREFIX . "auctions a
        LEFT JOIN " . DB_PREFIX . "auction_details ad ON (a.auction_id = ad.auction_id)
        ";
        
        $implode = array();
        
        if (!empty($data['filter_auction_id'])) {
            $implode[] = "a.auction_id = '" . (int)$data['filter_auction_id'] . "'";
        }
        if (!empty($data['filter_customer'])) {
            $implode[] = "ad.customer_id = '" . (int)$data['filter_customer'] . "'";
        }
        if (!empty($data['filter_auction_status'])) {
            $implode[] = "a.status = '" . (int)$data['filter_auction_status'] . "'";
        }
        if (!empty($data['filter_date_created'])) {
			$implode[] = "DATE(a.date_created) = DATE('" . $this->db->escape($data['filter_date_created']) . "')";
		}
		if (!empty($data['filter_start_date'])) {
			$implode[] = "DATE(ad.start_date) = DATE('" . $this->db->escape($data['filter_start_date']) . "')";
		}
		if (!empty($data['filter_end_date'])) {
			$implode[] = "DATE(ad.end_date) = DATE('" . $this->db->escape($data['filter_end_date']) . "')";
		}
		if (!empty($data['filter_title'])) {
			$sql .= " AND ad.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
		}
		if (!empty($data['filter_reserve_price'])) {
			$sql .= " AND ad.reserve_price >= '" . (float)$data['filter_reserve_price'] . "'";
		}
		if (!empty($data['filter_winning_bid'])) {
			$sql .= " AND a.winning_bid >= '" . (float)$data['filter_winning_bid'] . "'";
		}
		
		
        if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'a.auction_id',
			'ad.customer_id',
			'a.status',
			'a.date_created'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.auction_id";
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

		return $query->row['total'];
    }
	*/
	public function getAuctionImages($auction_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_photos WHERE auction_id = '" . (int)$auction_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}
	
    public function editAuction($auction_id, $store_id = 0) {
        
        
    }
    
    public function deleteAuction($auction_id, $store_id = 0) {
        
    }
    
    
    
}