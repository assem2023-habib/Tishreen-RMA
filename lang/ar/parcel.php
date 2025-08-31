<?php
return [ // Parcel messages
    'sender_id_required' => 'يجب إدخال معرف المرسل.',
    'sender_id_numeric' => 'نوع معرف المرسل يجب أن يكون رقمًا.',
    'sender_id_exists' => 'المستخدم الذي أدخلته غير موجود.',

    'sender_type_required' => 'نوع المرسل مطلوب.',
    'sender_type_in' => 'نوع المرسل يجب أن يكون AUTHENTICATED_USER أو GUEST.',

    'route_id_required' => 'يجب إدخال معرف المسار.',
    'route_id_numeric' => 'نوع معرف المسار يجب أن يكون رقمًا.',
    'route_id_exists' => 'المسار الذي أدخلته غير موجود.',

    'reciver_name_required' => 'اسم المستلم مطلوب.',
    'reciver_name_string' => 'اسم المستلم يجب أن يكون نصًا.',
    'reciver_name_max' => 'اسم المستلم يجب ألا يزيد عن 250 حرفًا.',
    'reciver_name_min' => 'اسم المستلم يجب ألا يقل عن حرفين.',

    'reciver_address_string' => 'عنوان المستلم يجب أن يكون نصًا.',
    'reciver_address_max' => 'عنوان المستلم يجب ألا يزيد عن 500 حرف.',

    'reciver_phone_required' => 'رقم هاتف المستلم مطلوب.',
    'reciver_phone_string' => 'رقم هاتف المستلم يجب أن يكون نصًا.',
    'reciver_phone_min' => 'رقم هاتف المستلم يجب ألا يقل عن 6 أحرف.',
    'reciver_phone_max' => 'رقم هاتف المستلم يجب ألا يزيد عن 20 حرفًا.',
    'reciver_phone_regex' => 'رقم هاتف المستلم غير صالح.',

    'weight_required' => 'وزن الطرد مطلوب.',
    'weight_numeric' => 'وزن الطرد يجب أن يكون رقمًا.',
    'weight_min' => 'وزن الطرد يجب أن يكون أكبر من صفر.',

    'cost_numeric' => 'التكلفة يجب أن تكون رقمًا.',
    'cost_min' => 'التكلفة يجب أن تكون على الأقل صفر.',

    'is_paid_required' => 'يجب تحديد ما إذا كان الطرد مدفوعًا.',
    'is_paid_boolean' => 'قيمة is_paid يجب أن تكون true أو false.',

    'parcel_status_in' => 'حالة الطرد يجب أن تكون واحدة من: PENDING, IN_TRANSIT, DELIVERED, CANCELLED.',

    'tracking_number_unique' => 'رقم التتبع يجب أن يكون فريدًا.',

    'price_policy_id_required' => 'معرف سياسة التسعير مطلوب.',
    'price_policy_id_numeric' => 'معرف سياسة التسعير يجب أن يكون رقمًا.',
    'price_policy_id_exists' => 'سياسة التسعير المحددة غير موجودة.',

    'no_parcels_found' => 'لا يوجد طرود لعرضها.',
    'no_parcel_found' => 'لا يوجد طرد للعرض.',

    'parcel_found' => 'تم ايجاد الطرد.',
    'parcels_found' => 'تم ايجاد الطرود',

    'parcel_delete_successfuly' => 'تم حذف الطرد بنجاح',
    'parcel_updated_successfuly' => 'تم تعديل الطرد بنجاح',
    'parcel_expired' => 'انتهت مدة صلاحية الطرد',
    'parcel_not_pending' => 'لا يمكنك تعديل الطرد لأن حالته ليست معلقة',
];
