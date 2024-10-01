// Hook into PMPro after checkout to trigger adding a new user to Constant Contact
add_action('pmpro_after_checkout', 'check_and_add_member_to_constant_contact');

function check_and_add_member_to_constant_contact($user_id) {
    // Get user data
    $user_info = get_userdata($user_id);
    $email = $user_info->user_email;
    $username = $user_info->user_login; // Use the username as first name for new contacts

    // Get user's membership level
    $membership_level = pmpro_getMembershipLevelForUser($user_id);
    $level_id = $membership_level->id; // ID of the membership level

    // Constant Contact API credentials
    $api_key = 'API KEY GOES HERE';
    $access_token = 'ACCESS TOKEN GOES HERE';
    
    // Define list IDs based on membership level
    $individual_list_id = 'LIST NUMBER 1 ID GOES HERE'; // Individual memberships
    $group_list_id = 'LIST NUMBER 2 ID GOES HERE';      // Group memberships

    // Determine list to add the user to based on membership level- need level ID
    $list_id = ($level_id === LEVEL_ID_NUM_GOES_HERE) ? $group_list_id : $individual_list_id;

    // Step 1: Check if the email already exists in Constant Contact
    $url_check = 'https://api.cc.email/v3/contacts?email=' . urlencode($email);
    $response_check = wp_remote_get($url_check, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type'  => 'application/json',
        ),
    ));

    // Handle the response from the check
    if (is_wp_error($response_check)) {
        error_log('Error checking Constant Contact: ' . $response_check->get_error_message());
        return; // Stop execution if there's an error
    }

    $body = wp_remote_retrieve_body($response_check);
    $data_check = json_decode($body, true);

    // Step 2: If the contact doesn't exist, proceed to add
    if (empty($data_check['contacts'])) {
        $data_add = array(
            'email_addresses' => array(
                array('email_address' => $email)
            ),
            'first_name' => $username,
            'list_memberships' => array($list_id) // Add user to the correct list
        );

        $url_add = 'https://api.cc.email/v3/contacts/sign_up_form';
        $response_add = wp_remote_post($url_add, array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ),
            'body' => json_encode($data_add),
        ));

        if (is_wp_error($response_add)) {
            error_log('Constant Contact API error (add): ' . $response_add->get_error_message());
        } else {
            error_log('User successfully added to Constant Contact list.');
        }
    } else {
        // Step 3: If the contact exists, update their list memberships
        $contact_id = $data_check['contacts'][0]['contact_id']; // Get contact ID

        $data_update = array(
            'list_memberships' => array($list_id) // Add user to the new list
        );

        $url_update = 'https://api.cc.email/v3/contacts/' . $contact_id;
        $response_update = wp_remote_request($url_update, array(
            'method' => 'PATCH',
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ),
            'body' => json_encode($data_update),
        ));

        if (is_wp_error($response_update)) {
            error_log('Constant Contact API error (update): ' . $response_update->get_error_message());
        } else {
            error_log('User successfully updated in Constant Contact.');
        }
    }
}
