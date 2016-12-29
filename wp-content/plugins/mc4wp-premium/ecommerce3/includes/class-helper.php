<?php


class MC4WP_Ecommerce_Helper {

	/**
	 * @var WPDB
	 */
	private $db;

	/**
	 * MC4WP_Ecommerce_Helper constructor.
	 */
	public function __construct() {
		$this->db = $GLOBALS['wpdb'];
	}

	public function get_order_ids( $untracked_only = false ) {
		$query = $this->get_order_query( 'DISTINCT(p.id)', $untracked_only );
		return $this->db->get_col( $query );
	}

    public function get_tracked_order_ids() {
        $tracked_ids = $this->get_order_ids();
        $untracked_ids = $this->get_untracked_order_ids();
        return array_diff( $tracked_ids, $untracked_ids );
    }

    public function get_untracked_order_ids() {
        $query = $this->get_order_query( 'DISTINCT(p.id)', true );
        return $this->db->get_col( $query );
    }

	public function get_order_count( $untracked_only = false ) {
		$query = $this->get_order_query( 'COUNT(DISTINCT(p.id))', $untracked_only );
		return $this->db->get_var( $query );
	}

	public function get_product_ids() {
		$query = $this->get_product_query( 'DISTINCT(p.id)' );
		return $this->db->get_col( $query );
	}

	public function get_tracked_product_ids() {
		$tracked_ids = $this->get_product_ids();
		$untracked_ids = $this->get_untracked_product_ids();
		return array_diff( $tracked_ids, $untracked_ids );
	}

	public function get_untracked_product_ids() {
		$query = $this->get_product_query( 'DISTINCT(p.id)', true );
		return $this->db->get_col( $query );
	}

	public function get_product_count( $untracked_only = false ) {
		$query = $this->get_product_query( 'COUNT(DISTINCT(p.id))', $untracked_only );
		return (int) $this->db->get_var( $query );
	}

	/**
	 * @param string $select
	 * @param bool $untracked_only
	 *
	 * @return string
	 */
	private function get_product_query( $select = 'p.*', $untracked_only = false ) {
		$query = "SELECT %s 
			FROM {$this->db->posts}	p 
				LEFT JOIN {$this->db->postmeta} pm ON pm.post_id = p.id 
			WHERE p.post_type = 'product' 
				AND p.post_status IN('publish', 'draft', 'private')";
		$query = sprintf( $query, $select ) . ' ';

        if( $untracked_only ) {
            $query .= $this->get_where_clause_for_untracked_objects_only();
        }

        // order by descending product ID so we start with newest orders first
        if( strpos( $select, 'COUNT' ) === false ) {
            $query .= " ORDER BY p.id DESC";
        }

		return $query;
	}

	/**
	 * @param string $select
	 * @param bool $untracked_only
	 *
	 * @return string
	 */
	private function get_order_query( $select = 'p.*', $untracked_only = false ) {
		$query = "
			SELECT %s 
			FROM {$this->db->posts}	p 
				LEFT JOIN {$this->db->postmeta} pm ON pm.post_id = p.id 
			WHERE p.post_type = 'shop_order' 
				AND p.post_status IN( %s )
				AND ( pm.meta_key = '_billing_email' OR pm.meta_key = '_customer_user' ) 
				AND pm.meta_value != ''";

        // IMPORTANT: not all orders have a _billing_email meta value. 

        // add IN clause for order statuses
		$order_statuses = mc4wp_ecommerce_get_order_statuses();
		$query = sprintf( $query, $select . ' ',  "'" . join( "', '", $this->db->_escape( $order_statuses ) ) . "'" );

		if( $untracked_only ) {
            $query .= $this->get_where_clause_for_untracked_objects_only();
		}

        // order by descending product ID so we start with newest orders first
        if( strpos( $select, 'COUNT' ) === false ) {
            $query .= " ORDER BY p.id DESC";
        }

		return $query;
	}

    /**
     * @param $email_address
     *
     * @return string
     */
    public function get_total_spent_for_email( $email_address ) {
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $in = join( "', '", $this->db->_escape( $order_statuses ) );

        $query = "SELECT SUM(meta2.meta_value)
			FROM {$this->db->posts} as posts
			LEFT JOIN {$this->db->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$this->db->postmeta} AS meta2 ON posts.ID = meta2.post_id
			WHERE   meta.meta_key       = '_billing_email'
			AND     meta.meta_value     = %s
			AND     posts.post_type     = 'shop_order'
			AND     posts.post_status   IN( '{$in}' )
			AND     meta2.meta_key      = '_order_total'
		";

        $query = $this->db->prepare( $query, $email_address );

        $result = $this->db->get_var( $query );
        return floatval( $result );
    }

    /**
     * @param $email_address
     *
     * @return string
     */
    public function get_order_count_for_email( $email_address ) {
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $in = join( "', '", $this->db->_escape( $order_statuses ) );

        $query = "SELECT COUNT(DISTINCT(posts.id))
			FROM {$this->db->posts} as posts
			LEFT JOIN {$this->db->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_billing_email'
			AND     meta.meta_value     = %s
			AND     posts.post_type     = 'shop_order'
			AND     posts.post_status   IN( '{$in}' )
		";

        $query = $this->db->prepare( $query, $email_address );
        $result = $this->db->get_var( $query );
        return (int) $result;
    }

    /**
     * @return string
     */
    private function get_where_clause_for_untracked_objects_only() {
        $query = " AND NOT EXISTS( 
				SELECT meta_key 
				FROM {$this->db->postmeta} pm2
				WHERE pm2.meta_key = %s 
					AND pm2.post_id = p.id 
				)";

        $query = $this->db->prepare( $query, MC4WP_Ecommerce::META_KEY );
        return $query;
    }


}