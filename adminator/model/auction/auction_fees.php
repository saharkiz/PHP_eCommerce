<?php
class ModelAuctionAuctionFees extends Model {
    
    public function getAllFees($store) {
        $sql = "SELECT * FROM " . DB_PREFIX . "setting
        WHERE store_id = '" . $this->db->escape($store) . "' AND code LIKE 'fees_%'";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function getSpecificCharges($data) {
        if (!isset($data)) {
            return false;
        }
        switch ($data['type']) {
            case "reserve_bid":
                return $data['reserve_price'] ? $this->getReservePriceFees($data['store'], $data['reserve_price']) : '0';
                break;
            case "buy_now":
                return $data['buy_now_only'] ? $this->getBuyNowOnlyFees($data['store'], $data['buy_now_price']) : '0';
                break;
            case "bold_item":
                return $data['bolded_item'] ? $this->getBoldItemFees() : '0';
                break;
            case "featured":
                return $data['featured'] ? $this->getFeaturedFees() : '0';
                break;
            case "highlighted":
                return $data['highlighted'] ? $this->getHighlightedFees() : '0';
                break;
            case "auto_relist":
                return $data['relist'] ? $this->getRelistFees() : '0';
                break;
            case "slideshow":
                return $data['slideshow'] ? $this->getSlideshowFees() : '0';
                break;
            case "social_media":
                return $data['social_media'] ? $this->getSocialMediaFees() : '0';
                break;
            case "subtitle":
                return $data['subtitle'] ? $this->getSubTitleFees() : '0';
                break;
            case "on_carousel":
                return $data['on_carousel'] ? $this->getOnCarouselFees() : '0';
                break;
            default:
                return '0';
        }
    }
    
    public function getAllCharges($data) {
        $fees_to_charge = array(
            array(
                'fee_name'  =>  'For setting a Reserve Bid',
                'fee_charge'        => $data['reserve_price'] ? $this->getReservePriceFees($data['store'], $data['reserve_price']) : '0'
            ),
            array(
                'fee_name'  =>  'For setting the auction as Buy Now Only',
                'fee_charge'            => $data['buy_now_only'] ? $this->getBuyNowOnlyFees($data['store'], $data['buy_now_price']) : '0'
            ),
            array(
                'fee_name'  => 'For setting the auction to be Bolded',
                'fee_charge'          => $data['bolded_item'] ? $this->getBoldItemFees() : '0'
            ),
            array(
                'fee_name'  =>  'For setting the auction to be Featured',
                'fee_charge'           => $data['featured'] ? $this->getFeaturedFees() : '0'
            ),
            array(
                'fee_name'  =>  'For setting the auction to be highlighted',
                'fee_charge'        => $data['highlighted'] ? $this->getHighlightedFees() : '0'
            ),
            array(
                'fee_name'  =>  'For automatically relisting the auction',
                'fee_charge'             => $data['auto_relist'] ? $this->getRelistFees() : '0'
            ),
            array(
                'fee_name'  =>  'For placing the auction in the slideshow',
                'fee_charge'          => $data['slideshow'] ? $this->getSlideshowFees() : '0'
            ),
            array(
                'fee_name'  =>  'For placing your auction on our social media sites',
                'fee_charge'       => $data['social_media'] ? $this->getSocialMediaFees() : '0'
            ),
            array(
                'fee_name'  =>  'For setting a subtitle',
                'fee_charge'           => $data['subtitle'] ? $this->getSubTitleFees() : '0'
            ),
            array(
                'fee_name'  =>  'For uploading extra images',
                'fee_charge'           => $data['extra_images'] ? $this->getImagesFees($data['extra_images']) : '0'
            ),
            array(
                'fee_name'  =>  'For placing your auction on the carousel',
                'fee_charge'        => $data['on_carousel'] ? $this->getOnCarouselFees() : '0'
            )
        );
        
        //$reserve = $this->getReservePriceFees($data['store'], $data['reserve_price']);
        
        return $fees_to_charge;
    }
    
    protected function getReservePriceFees($store, $amount) {
        $sql = "SELECT * FROM " . DB_PREFIX . "setting
        WHERE store_id = '" . $this->db->escape($store) . "' AND code = 'fees_reserve_price'
        ORDER BY 'key' ASC";
        $query = $this->db->query($sql);
        $rates = $query->rows;
        if ($rates[5]['value'] == '0') {
            return '0';
        }
        
        $lastfee = count($rates) - 5;
        $allfees = array_slice($rates,0,5);
        if ($amount >= $allfees[0]['value'] && $amount <= $allfees[1]['value']){
            if ($allfees[4]['value']) {
                if ($allfees[3]['value'] == 'percent') {
                    $chargethem = $amount * ($allfees[2]['value'] / 100);
                } else {
                    $chargethem = $allfees[2]['value'];
                }
            }
            return $chargethem;
        }
        
        for ($x=6; $x <= $lastfee; $x+=5) {
            $allfees = array_slice($rates,$x,5);
            if ($amount >= $allfees[0]['value'] && $amount <= $allfees[1]['value']){
                if ($allfees[4]['value']) {
                    if ($allfees[3]['value'] == 'percent') {
                        $chargethem = $amount * ($allfees[2]['value'] / 100);
                    } else {
                        $chargethem = $allfees[2]['value'];
                    }
                }
            }
        }
        
        
        
        return $chargethem;
    }
    
    protected function getBuyNowOnlyFees($store, $amount) {
        $sql = "SELECT * FROM " . DB_PREFIX . "setting
        WHERE store_id = '" . $this->db->escape($store) . "' AND code = 'fees_buy_now'
        ORDER BY 'key' ASC";
        $query = $this->db->query($sql);
        $rates = $query->rows;
        if ($rates[5]['value'] == '0') {
            return '0';
        }
        
        $lastfee = count($rates) - 5;
        $allfees = array_slice($rates,0,5);
        if ($amount >= $allfees[0]['value'] && $amount <= $allfees[1]['value']){
            if ($allfees[4]['value']) {
                if ($allfees[3]['value'] == 'percent') {
                    $chargethem = $amount * ($allfees[2]['value'] / 100);
                } else {
                    $chargethem = $allfees[2]['value'];
                }
            }
            return $chargethem;
        }
        
        for ($x=6; $x <= $lastfee; $x+=5) {
            $allfees = array_slice($rates,$x,5);
            if ($amount >= $allfees[0]['value'] && $amount <= $allfees[1]['value']){
                if ($allfees[4]['value']) {
                    if ($allfees[3]['value'] == 'percent') {
                        $chargethem = $amount * ($allfees[2]['value'] / 100);
                    } else {
                        $chargethem = $allfees[2]['value'];
                    }
                }
            }
        }
        
        
        
        return $chargethem;
    }
    
    protected function getSignupFees() {
        /*$sql = "SELECT * FROM " . DB_PREFIX . "setting
        WHERE store_id = '" . $this->db->escape($store) . "' AND code = 'fees_signup'";
        $query = $this->db->query($sql);
        return $query->rows;*/
        $fee = '0';
        $status = $this->config->get('fees_signup_status');
        if ($status) {
            $fee = $this->config->get('fees_signup_fee');
        }
        return $fee;
    }
    
    protected function getFeaturedFees() {
        $fee = '0';
        $status = $this->config->get('fees_featured_status');
        if ($status) {
            $fee = $this->config->get('fees_featured_fee');
        }
        return $fee;
    }
    
    protected function getRelistFees() {
        $fee = '0';
        $status = $this->config->get('fees_relist_status');
        if ($status) {
            $fee = $this->config->get('fees_relist_fee');
        }
        return $fee;
    }
    
    protected function getOnCarouselFees() {
        $fee = '0';
        $status = $this->config->get('fees_carousel_status');
        if ($status) {
            $fee = $this->config->get('fees_carousel_fee');
        }
        return $fee;
    }
    
    protected function getImagesFees($num_extra_images) {
        $fee = '0';
        $status = $this->config->get('fees_image_upload_status');
        if ($status) {
            $fee = $this->config->get('fees_image_upload_fee') * $num_extra_images;
        }
        
        return $fee;
    }
    
    protected function getHighlightedFees() {
        $fee = '0';
        $status = $this->config->get('fees_highlighted_status');
        if ($status) {
            $fee = $this->config->get('fees_highlighted_fee');
        }
        return $fee;
    }
    
    protected function getBoldItemFees() {
        $fee = '0';
        $status = $this->config->get('fees_bold_item_status');
        if ($status) {
            $fee = $this->config->get('fees_bold_item_fee');
        }
        return $fee;
    }
    
    protected function getSetupFees() {
        $fee = '0';
        $status = $this->config->get('fees_auction_setup_status');
        if ($status) {
            $fee = $this->config->get('fees_auction_setup_fee');
        }
        return $fee;
    }
    
    protected function getSlideshowFees() {
        $fee = '0';
        $status = $this->config->get('fees_slideshow_status');
        if ($status) {
            $fee = $this->config->get('fees_slideshow_fee');
        }
        return $fee;
    }
    
    protected function getSocialMediaFees() {
        $fee = '0';
        $status = $this->config->get('fees_social_media_status');
        if ($status) {
            $fee = $this->config->get('fees_social_media_fee');
        }
        return $fee;
    }
    
    protected function getSubTitleFees() {
        $fee = '0';
        $status = $this->config->get('fees_subtitle_status');
        if ($status) {
            $fee = $this->config->get('fees_subtitle_fee');
        }
        return $fee;
    }
    
    
} // End of Model