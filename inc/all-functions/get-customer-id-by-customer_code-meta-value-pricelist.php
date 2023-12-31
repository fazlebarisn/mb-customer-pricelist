<?php 

/**
 * Get Customer id by customer_code Meta value
 */

function get_user_id_by_custom_meta_value_for_customer_pricelist($meta_value) {
    if (empty($meta_value)) {
        return;
    }

    $args = array(
        'meta_query' => array(
            array(
                'key' => "customer_code",
                'value' => $meta_value,
                'compare' => '=',
            ),
        ),
    );

    $users = get_users($args);

    if (!empty($users)) {
        // Get the user ID from the first user in the results (assuming meta values are unique).
        return $users[0]->ID;
    }

    return false; // User not found.
}
