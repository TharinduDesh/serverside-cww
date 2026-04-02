<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bidding_model extends CI_Model
{
    public function get_bid_by_user_and_feature_date($userId, $featureDate)
    {
        return $this->db
            ->where('user_id', $userId)
            ->where('feature_date', $featureDate)
            ->get('bids')
            ->row();
    }

    public function create_bid($data)
    {
        $this->db->insert('bids', $data);
        return $this->db->insert_id();
    }

    public function update_bid($bidId, $userId, $data)
    {
        return $this->db
            ->where('id', $bidId)
            ->where('user_id', $userId)
            ->update('bids', $data);
    }

    public function get_highest_bid_for_feature_date($featureDate)
    {
        return $this->db
            ->where('feature_date', $featureDate)
            ->order_by('bid_amount', 'DESC')
            ->order_by('placed_at', 'ASC')
            ->get('bids')
            ->row();
    }

    public function get_bids_for_feature_date($featureDate)
    {
        return $this->db
            ->where('feature_date', $featureDate)
            ->get('bids')
            ->result();
    }

    public function update_bid_status($bidId, $status)
    {
        return $this->db
            ->where('id', $bidId)
            ->update('bids', ['status' => $status]);
    }

    public function update_all_bid_statuses_for_feature_date($featureDate, $winningBidId)
    {
        $this->db
            ->where('feature_date', $featureDate)
            ->where('id !=', $winningBidId)
            ->update('bids', ['status' => 'outbid']);

        $this->db
            ->where('id', $winningBidId)
            ->update('bids', ['status' => 'winning']);
    }

    public function count_monthly_featured_wins($userId, $featureDate)
    {
        $monthStart = date('Y-m-01', strtotime($featureDate));
        $monthEnd = date('Y-m-t', strtotime($featureDate));

        return $this->db
            ->where('user_id', $userId)
            ->where('feature_date >=', $monthStart)
            ->where('feature_date <=', $monthEnd)
            ->count_all_results('featured_alumni');
    }

    public function get_user_bids($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('feature_date', 'DESC')
            ->get('bids')
            ->result();
    }

    public function featured_record_exists($featureDate)
    {
        return $this->db
            ->where('feature_date', $featureDate)
            ->count_all_results('featured_alumni') > 0;
    }

    public function create_featured_alumnus($data)
    {
        return $this->db->insert('featured_alumni', $data);
    }

    public function mark_final_bid_results($featureDate, $winningBidId)
    {
        $this->db
            ->where('feature_date', $featureDate)
            ->where('id !=', $winningBidId)
            ->update('bids', ['status' => 'lost']);

        $this->db
            ->where('id', $winningBidId)
            ->update('bids', ['status' => 'won']);
    }

    public function get_winning_bid_for_feature_date($featureDate)
    {
        return $this->db
            ->where('feature_date', $featureDate)
            ->order_by('bid_amount', 'DESC')
            ->order_by('placed_at', 'ASC')
            ->get('bids')
            ->row();
    }

    public function get_bids_for_feature_date_ordered($featureDate)
    {
        return $this->db
            ->where('feature_date', $featureDate)
            ->order_by('bid_amount', 'DESC')
            ->order_by('placed_at', 'ASC')
            ->get('bids')
            ->result();
    }

    public function get_featured_alumnus_for_date($featureDate)
    {
        return $this->db
            ->select('featured_alumni.*, users.first_name, users.last_name')
            ->from('featured_alumni')
            ->join('users', 'users.id = featured_alumni.user_id')
            ->where('featured_alumni.feature_date', $featureDate)
            ->get()
            ->row();
    }

    public function get_bids_with_user_details_for_feature_date($featureDate)
    {
        return $this->db
            ->select('bids.*, users.first_name, users.last_name, users.university_email')
            ->from('bids')
            ->join('users', 'users.id = bids.user_id')
            ->where('bids.feature_date', $featureDate)
            ->order_by('bids.bid_amount', 'DESC')
            ->order_by('bids.placed_at', 'ASC')
            ->get()
            ->result();
    }

    // For live outbid mails

    public function get_current_winning_bid_for_feature_date($featureDate)
    {
        return $this->db
            ->select('bids.*, users.first_name, users.last_name, users.university_email')
            ->from('bids')
            ->join('users', 'users.id = bids.user_id')
            ->where('bids.feature_date', $featureDate)
            ->where('bids.status', 'winning')
            ->get()
            ->row();
    }

    public function get_bid_with_user_details($bidId)
    {
        return $this->db
            ->select('bids.*, users.first_name, users.last_name, users.university_email')
            ->from('bids')
            ->join('users', 'users.id = bids.user_id')
            ->where('bids.id', $bidId)
            ->get()
            ->row();
    }
}