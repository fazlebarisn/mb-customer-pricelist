<?php 
/*
 * Plugin Name:       MB Synchronize all Customer Pricelist
 * Description:       This plugin synchronizes all Customer meta from a database
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CanSoft
 * Author URI:        https://cansoft.com/
 */
// Include your functions here

// require_once( plugin_dir_path( __FILE__ ) . '/inc/all-functions/mb-customer-meta-sync.php');


require_once( plugin_dir_path( __FILE__ ) . '/inc/all-functions/get-customer-id-by-customer_code-meta-value-pricelist.php');
require_once( plugin_dir_path( __FILE__ ) . '/inc/all-functions/mb-customer-pricelist-sync.php');


 require_once( plugin_dir_path( __FILE__ ) . '/inc/api/fetch-all-customer-pricelist-from-ezposcustomer-table.php');


//WORDPRESS HOOK FOR ADD A CRON JOB EVERY 2 Min

function mb_customer_pricelist_cron_schedules($schedules){
    if(!isset($schedules['every_twelve_hours'])){
        $schedules['every_twelve_hours'] = array(
            'interval' => 12*60*60, // Every 12 hours
            'display' => __('Every 12 hours'));
    }
    return $schedules;
}

add_filter('cron_schedules','mb_customer_pricelist_cron_schedules');




// Enqueue all assets
function mbcustomer_pricelist_all_assets(){
    wp_enqueue_script('mbcp-customer-pricelist-script', plugin_dir_url( __FILE__ ) . 'assets/admin/js/script.js', null, time(), true);
}
add_action( 'admin_enqueue_scripts', 'mbcustomer_pricelist_all_assets' );


/**
 * Add menu page for this plugin
 */
function mb_pricelist_customer_pricelist_sync_menu_pages(){
    //add_menu_page('Mb Customer Sync', 'Customer Pricelist Sync', 'manage_options', 'mb-customer-pricelist-sync', 'customer_pricelist_sync_page');

    add_submenu_page( 'mb_syncs', 'Customer Pricelist Sync', 'Customer Pricelist Sync', 'manage_options', 'mb-customer-pricelist-sync', 'customer_pricelist_sync_page' );
}
add_action( 'admin_menu', 'mb_pricelist_customer_pricelist_sync_menu_pages',999 );

/**
 * Main function for product sync
 */
function customer_pricelist_sync_page(){
    ?>
    <style>
        .wrap .d-flex {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
        }
    </style>
        <div class="wrap">
            <h1>This Page for Sincronize all Customer Pricelist</h1><br>
            <div class="d-flex">
            	<form method="GET">

	                <input type="hidden" name="page-number" value="1">
	                <input type="hidden" name="page" value="mb-customer-pricelist-sync">

	                <?php submit_button('Get All Customer Pricelist from ezposcustomer Table', 'primary', 'mb-customer-pricelist-sync'); ?>

	            </form>

                <form method="POST">
                    <?php 
                        submit_button( 'Start ezposcustomer Cron Now', 'primary', 'mb-ezposcustomer-sync-cron' );
                        // submit_button( 'Menual Start', 'primary', 'mb-ezposcustomer-menual-sync-cron' );
                    ?>
                </form>
            </div>
          
            <?php 

                if(isset($_GET['pageno'])){
                        
                    $pageno = $_GET['pageno'] ?? 1;
                    
                    $all_customer_pricelist = fetch_all_customer_pricelist_form_ezposcustomer_table($pageno);

                    //dd($all_customer_meta);
                    // $api_ids = [];

                    // $start = microtime(true);
                    $arraychunk = array_chunk($all_customer_pricelist, 100);

                    foreach ($arraychunk as $all_pricelists) {
                   
                        foreach($all_pricelists as $_c_pricelist){
                            
                        	//dd($_c_meta);
                            //$api_ids[] = $_q_location['id'];

                       
                           //get customer Id using customer_code meta value
                            $userId = get_user_id_by_custom_meta_value_for_customer_pricelist($_c_pricelist["CUS_Code"]);

                            if ($userId) {
                                
                                $result = update_user_meta($userId, "pricelist_type", $_c_pricelist["CUS_Pricelist"]);

                                if ($result) {
                                    echo "Data saved";
                                }else{
                                    echo "Data not saved";
                                }
                            

                            }

                        }
                    }
                    // $total = microtime(true) - $start;
                    // echo "<span style='color:red;font-weight:bold'>Total Execution Time: </span>" . $total;

                    // // API endpoint
                    // $apiUrl = 'https://modern.cansoft.com/db-clone/api/iciloc/update?key=58fff5F55dd444967ddkhzf';
                    
                    // // List of update IDs
                    // $updateIds = implode(",", $api_ids);
                    
                    // // Prepare the request payload
                    // $requestData = [
                    //     'id' => $updateIds,
                    //     'status' => 'Synced',
                    // ];

                    // // Use cURL to make the API request
                    // $ch = curl_init($apiUrl);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestData));
                    // $response = curl_exec($ch);

                    // //$total = microtime(true) - $start;

                    // // Check for errors or process the response as needed
                    // if ($response === false) {
                    //     // Handle cURL error
                    //     echo 'cURL Error: ' . curl_error($ch);
                    // } else {
                    //     // Process the API response
                    //     // $response contains the API response data
                    //     echo 'API Response: ' . $response;
                    // }

                    // // Close the cURL session
                    // curl_close($ch);


                    if(! count( $all_customer_pricelist )){
                        wp_redirect( admin_url( "/edit.php?page=mb-customer-pricelist-sync" ) );
                        exit();
                    }
                }


                // if (isset($_POST['mb-icpricp-product-sync-menual'])) {

                //     mb_customer_meta_sync(1);
                //     wp_redirect( admin_url( "/edit.php?page=mb-customer-sync" ) );
                //     exit();
                // }


                //It work when Click Strt cron  button
                if(isset($_POST['mb-ezposcustomer-sync-cron'])){
                    if (!wp_next_scheduled('mb_ezposcustomer_add_with_cron')) {
                        wp_schedule_event(time(), 'every_twelve_hours', 'mb_ezposcustomer_add_with_cron');
                    }
                    wp_redirect( admin_url( "/edit.php?page=mb-customer-pricelist-sync" ) );
                    exit();
                }

            ?>
        </div>
    <?php 
}

//For clear cron schedule
function woo_customer_pricelist_syncronization_apis_plugin_deactivation(){
    wp_clear_scheduled_hook('mb_ezposcustomer_add_with_cron');
    
}
register_deactivation_hook(__FILE__, 'woo_customer_pricelist_syncronization_apis_plugin_deactivation');


// This happend when icitem caron job is runnning


// This happend when icpricp caron job is runnning

function mb_pricelist_run_cron_for_ezposcustomer_table(){

    $start = microtime(true);

    mb_pricelist_customer_pricelist_sync(1);

    $total = microtime(true) - $start;

    $total = "Total execution time is ". $total;
    
    file_put_contents(plugin_dir_path(__FILE__) . 'cron_debug.log', $total, FILE_APPEND);
    
}

add_action('mb_ezposcustomer_add_with_cron', 'mb_pricelist_run_cron_for_ezposcustomer_table');

