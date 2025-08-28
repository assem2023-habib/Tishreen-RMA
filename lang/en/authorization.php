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

    'authorization_already_exists' => 'Authorization already exists for this parcel and user.',
    'create_authorization_success' => 'Authorization created successfully',

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

    // Guest user fields

    'authorized_guest_first_name_required' => 'The first name of the guest is required.',
    'authorized_guest_first_name_string' => 'The first name of the guest must be a string.',
    'authorized_guest_first_name_max' => 'The first name of the guest may not be greater than 50 characters.',

    'authorized_guest_last_name_string' => 'The last name of the guest must be a string.',
    'authorized_guest_last_name_max' => 'The last name of the guest may not be greater than 50 characters.',

    'authorized_guest_phone_required' => 'The phone number of the guest is required.',
    'authorized_guest_phone_string' => 'The phone number of the guest must be a string.',
    'authorized_guest_phone_min' => 'The phone number of the guest must be at least 6 characters.',
    'authorized_guest_phone_max' => 'The phone number of the guest may not be greater than 20 characters.',
    'authorized_guest_phone_regex' => 'The phone number of the guest format is invalid.',

    'authorized_guest_address_string' => 'The address of the guest must be a string.',
    'authorized_guest_address_max' => 'The address of the guest may not be greater than 255 characters.',

    'authorized_guest_national_number_string' => 'The national number of the guest must be a string.',
    'authorized_guest_national_number_max' => 'The national number of the guest may not be greater than 20 characters.',

    'authorized_guest_city_id_numeric' => 'The city ID of the guest must be a number.',
    'authorized_guest_city_id_exists' => 'The selected city does not exist.',

    'authorized_guest_birthday_date' => 'The birthday of the guest must be a valid date.',
    'authorized_guest_birthday_before' => 'The birthday of the guest must be a date before today.',

    // Other fields
    'used_at_date' => 'The used date must be a valid date.',
    'cancellation_reason_string' => 'The cancellation reason must be a string.',

    'cannot_update_authorization' => 'Cannot update the authorization.',
    'authorization_updated_successfully' => 'Authorization updated successfully.',
    'cannot_create_authorization' => 'Cannot create the authorization.',
    'no_authorizations_found' => 'No authorizations were found.',
    'authorization_deleted_successfully' => 'Authorization deleted successfully.',
];
