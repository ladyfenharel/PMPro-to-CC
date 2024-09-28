# Add New Members from PMPro into a Constant Contact List
This guide walks you through the process of automatically adding new members from the WordPress plugin Paid Memberships Pro (PMPro) to your Constant Contact lists. Members are added to specific lists based on their membership level.

## Requirements
- [x] WordPress website
- [x] [Paid Memberships Pro plugin]([url](https://www.paidmembershipspro.com/))
- [x] Constant Contact Account
- [x] [Constant Contact Developer Account]([url](https://developer.constantcontact.com/))

## How to Use This Code
1. **Generate a Constant Contact API Key**
   - Log in to your Constant Contact Developer account and create an API key.
2. **Obtain an Access Token**
   - After generating the API key, use OAuth 2.0 to retrieve an access token.
3. **Retrieve List IDs from Constant Contact**
   - Log in to Constant Contact and find the IDs of the lists you want to use.
4. **Determine Membership Level IDs**
   - Identify the membership level IDs in Paid Memberships Pro that correspond to the Constant Contact lists.
5. **Input the Necessary Data**
   - Add your API key, access token, and Constant Contact list IDs to the placeholders, shown in the code snippet below.


``` javascript
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
```

6. **Add the Code to functions.php**
   - In your WordPress admin panel, navigate to Appearance > Theme File Editor. Add the code snippet to your `functions.php` file to enable the automatic addition of members to the correct Constant Contact list.


## Possible Issues for Contributions
- Error Handling for API Requests
  - Add robust error handling for API requests to Constant Contact (e.g., handling expired tokens, connection failures, invalid list IDs, etc.).
- Settings Interface in WordPress Admin
  - Create a user-friendly settings page in the WordPress admin panel to manage API keys, tokens, and list IDs, instead of hardcoding them in `functions.php`.
- Support for Membership Level Changes
  - Implement functionality to automatically update the member’s Constant Contact list if their membership level changes in PMPro.
- Optimize API Call Performance
  - Optimize the performance of API calls to reduce load times and prevent potential bottlenecks during high traffic.


