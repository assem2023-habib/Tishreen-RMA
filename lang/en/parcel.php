<?php
return [ // Parcel messages
    'sender_id_required' => 'You should enter the sender Id.',
    'sender_id_numeric' => 'The type of sender id should be Integer.',
    'sender_id_exists' => 'The user you entered does not exist!',

    'sender_type_required' => 'Sender type is required.',
    'sender_type_in' => 'Sender type must be AUTHENTICATED_USER or GUEST.',

    'route_id_required' => 'You should enter the route Id.',
    'route_id_numeric' => 'The type of route id should be Integer.',
    'route_id_exists' => 'The route you entered does not exist!',

    'reciver_name_required' => 'Receiver name is required.',
    'reciver_name_string' => 'Receiver name must be a string.',
    'reciver_name_max' => 'Receiver name must not exceed 250 characters.',
    'reciver_name_min' => 'Receiver name must be at least 2 characters.',

    'reciver_address_string' => 'Receiver address must be a string.',
    'reciver_address_max' => 'Receiver address must not exceed 500 characters.',

    'reciver_phone_required' => 'Receiver phone is required.',
    'reciver_phone_string' => 'Receiver phone must be a string.',
    'reciver_phone_min' => 'Receiver phone must be at least 6 characters.',
    'reciver_phone_max' => 'Receiver phone must not exceed 20 characters.',
    'reciver_phone_regex' => 'Receiver phone must be a valid phone number.',

    'weight_required' => 'Parcel weight is required.',
    'weight_numeric' => 'Parcel weight must be numeric.',
    'weight_min' => 'Parcel weight must be greater than 0.',

    'cost_numeric' => 'Cost must be numeric.',
    'cost_min' => 'Cost must be at least 0.',

    'is_paid_required' => 'You must specify if the parcel is paid.',
    'is_paid_boolean' => 'is_paid must be true or false.',

    'parcel_status_in' => 'Parcel status must be one of: PENDING, IN_TRANSIT, DELIVERED, CANCELLED.',

    'tracking_number_unique' => 'Tracking number must be unique.',

    'price_policy_id_required' => 'Price policy id is required.',
    'price_policy_id_numeric' => 'Price policy id must be numeric.',
    'price_policy_id_exists' => 'The selected price policy does not exist.',

    'no_parcels_found' => 'no parcels found.',
    'no_parcel_found' => 'no parcel found.',

    'parcel_found' => 'parcel found.',
    'parcels_found' => 'parcels found.',

    'parcel_delete_successfuly' => 'parcel deleted successfuly',

];
