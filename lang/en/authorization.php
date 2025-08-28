<?php
return [
    // User
    'user_id_required' => 'User ID is required.',
    'user_id_numeric' => 'User ID must be a number.',
    'user_id_exists' => 'The specified user does not exist.',
    'user_id_different' => 'User ID must be different from the authorized user.',

    // Parcel
    'parcel_id_required' => 'Parcel ID is required.',
    'parcel_id_numeric' => 'Parcel ID must be a number.',
    'parcel_id_exists' => 'The specified parcel does not exist.',

    // Authorized User
    'authorized_user_id_numeric' => 'Authorized user ID must be a number.',
    'authorized_user_id_exists' => 'The authorized user does not exist.',
    'authorized_user_id_different' => 'The authorized user must be different from the parcel owner.',

    // Authorized Guest
    'authorized_guest_array' => 'Authorized guest must be an array.',

    'no_authorizations_granted_by_you' => 'No authorizations granted by you were found.',
    'authorizations_retrieved_successfully' => 'All authorizations retrieved successfully.',

    'no_authorization_found' => 'Authorization not found.',
    'authorization_retrieved_successfully' => 'Authorization retrieved successfully.',

    'authorized_user_id_prohibited_with' => 'authorized_user_id cannot be sent together with authorized_guest.',
    'authorized_guest_prohibited_with' => 'authorized_guest cannot be sent together with authorized_user_id.',

    'guest_first_name_required' => 'Guest first name is required.',
    'guest_first_name_string' => 'Guest first name must be a string.',
    'guest_first_name_max' => 'Guest first name may not be greater than 50 characters.',

    'guest_last_name_string' => 'Guest last name must be a string.',
    'guest_last_name_max' => 'Guest last name may not be greater than 50 characters.',

    'guest_phone_required' => 'Guest phone number is required.',
    'guest_phone_string' => 'Guest phone number must be a string.',
    'guest_phone_min' => 'Guest phone number must be at least 6 characters.',
    'guest_phone_max' => 'Guest phone number may not be greater than 20 characters.',
    'guest_phone_regex' => 'Guest phone number format is invalid.',

    'guest_address_string' => 'Guest address must be a string.',
    'guest_address_max' => 'Guest address may not be greater than 255 characters.',

    'guest_national_number_string' => 'Guest national number must be a string.',
    'guest_national_number_max' => 'Guest national number may not be greater than 20 characters.',

    'guest_city_id_numeric' => 'Guest city ID must be numeric.',
    'guest_city_id_exists' => 'The specified guest city does not exist.',

    'guest_birthday_date' => 'Guest birthday must be a valid date.',
    'guest_birthday_before_today' => 'Guest birthday must be a date before today.',
];
