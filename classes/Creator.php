<?php

require_once __DIR__ . '/Database.php';

class Creator {
    private $id;
    private $user_id;
    private $display_name;
    private $bio;
    private $profile_image;
    private $is_featured;
    private $coffee_price;
    private $created_at;
    private $db;

    public function __construct($creator_data = null) {
        $this->db = Database::getInstance();

        if ($creator_data) {
            $this->id = $creator_data['id'] ?? null;
            $this->user_id = $creator_data['user_id'] ?? null;
            $this->display_name = $creator_data['display_name'] ?? null;
            $this->bio = $creator_data['bio'] ?? null;
            $this->profile_image = $creator_data['profile_image'] ?? null;
            $this->is_featured = $creator_data['is_featured'] ?? false;
            $this->coffee_price = $creator_data['coffee_price'] ?? 5.00;
            $this->created_at = $creator_data['created_at'] ?? null;
        }
    }

    // Create a new creator profile
    public function create() {
        if (!$this->user_id || !$this->display_name) {
            return false;
        }

        $sql = "INSERT INTO creators (user_id, display_name, bio, profile_image, coffee_price) 
                VALUES (?, ?, ?, ?, ?)";

        $params = [
            $this->user_id,
            $this->display_name,
            $this->bio,
            $this->profile_image,
            $this->coffee_price
        ];        

        $this->id = $this->db->insert($sql, $params);

        return $this->id ? true : false;
    }

    // Update creator profile
    public function update() {
        if (!$this->id) {
            return false;
        }

        $sql = "UPDATE creators
                SET display_name = ?, bio =?, profile_image = ?, coffee_price = ?
                WHERE id = ?";

        $params = [
            $this->display_name,
            $this->bio,
            $this->profile_image,
            $this->coffee_price,
            $this->id
        ];   
        
        return $this->db->update($sql, $params) > 0;
    }

    // Set as featured
    public function setFeatured($featured = true) {
        if (!$this->id) {
            return false;
        }

        $this->is_featured = $featured;
        
        $sql = "UPDATE creators SET is_featured = ? WHERE id = ?";
        return $this->db->update($sql, [$featured ? 1 : 0, $this->id]) > 0;
    }

    // Get total donations received
    public function getTotalDonations() {
        if (!$this->id) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as count, SUM(amount) as total
                FROM donations
                WHERE creator_id = ? AND status = 'confirmed'";

        $result = $this->db->getRow($sql, [$this->id]);
        
        return [
            'count' => (int)($result['count'] ?? 0),
            'total' => (float)($result['total'] ?? 0)
        ];
    }

    // Get creator by ID
    public static function getById($creator_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM creators WHERE id = ?";
        $creator_data = $db->getRow($sql, [$creator_id]);

        if (!$creator_data) {
            return null;
        }

        return new Creator($creator_data);
    }

    // Get creator by user ID
    public static function getByUserId($user_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM creators WHERE user_id = ?";
        $creator_data = $db->getRow($sql, [$user_id]);

        if (!$creator_data) {
            return null;
        }

        return new Creator($creator_data);
    }

    // Get featured creators
    public static function getFeatured($limit = 6) {
        $db = Database::getInstance();
        $sql = "SELECT c.*, u.username
                FROM creators c
                JOIN users u ON c.user_id = u.id
                WHERE c.is_featured = 1
                ORDER BY c.created_at DESC
                LIMIT ?";

        $creators_data = $db->getRows($sql, [$limit]);
        
        $creators = [];
        foreach ($creators_data as $creator_data) {
            $creators[] = new Creator($creator_data);
        }

        return $creators;
    }

    // Search creators
    public static function search($query, $limit = 10, $offset = 0) {
        $db = Database::getInstance();
        $sql = "SELECT c.*, u.username
                FROM creators c
                JOIN users u ON c.user_id = u.id
                WHERE c.display_name LIKE ? OR u.username LIKE ? OR c.bio LIKE ?
                ORDER BY c.is_featured DESC, c.created_at DESC
                LIMIT ? OFFSET ?";

        $search_param = "%{$query}%";        
    }
}