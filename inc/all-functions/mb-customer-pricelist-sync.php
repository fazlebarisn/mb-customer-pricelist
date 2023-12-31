<?php 

/**
 * Delete woocommerce product by id
 */

function mb_customer_pricelist_sync($page = 1) {

    $all_customer_pricelist = fetch_all_customer_pricelist_form_ezposcustomer_table($page);

    // $start = microtime(true);
     $arraychunk = array_chunk($all_customer_pricelist, 100);

        foreach ($arraychunk as $all_pricelists) {
       
            foreach($all_pricelists as $_c_pricelist){
                
                //dd($_c_meta);
                //$api_ids[] = $_q_location['id'];

           
               //get customer Id using customer_code meta value
                $userId = get_user_id_by_custom_meta_value_for_customer_pricelist($_c_pricelist["CUS_Code"]);

                if ($userId) {
                    
                    update_user_meta($userId, "pricelist_type", $_c_pricelist["CUS_Pricelist"]);
                

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

    if (count($all_customer_pricelist)) {

            mb_customer_pricelist_sync($page);

        }
}