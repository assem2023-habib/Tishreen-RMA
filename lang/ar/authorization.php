<?php
return [
    // User
    'user_id_required' => 'يجب إدخال معرف المستخدم.',
    'user_id_numeric' => 'معرف المستخدم يجب أن يكون رقمًا.',
    'user_id_exists' => 'المستخدم الذي أدخلته غير موجود.',
    'user_id_different' => 'معرف المستخدم يجب أن يختلف عن المستخدم المفوض.',

    // Parcel
    'parcel_id_required' => 'معرف الطرد مطلوب.',
    'parcel_id_numeric' => 'معرف الطرد يجب أن يكون رقمًا.',
    'parcel_id_exists' => 'الطرد المحدد غير موجود.',

    // Authorized User
    'authorized_user_id_numeric' => 'معرف المستخدم المفوض يجب أن يكون رقمًا.',
    'authorized_user_id_exists' => 'المستخدم المفوض غير موجود.',
    'authorized_user_id_different' => 'المستخدم المفوض يجب أن يختلف عن صاحب الطرد.',

    'authorization_already_exists' =>  'يوجد بالفعل تخويل لهذا الطرد لهذا المستخدم.',

    // Authorized Guest
    'authorized_guest_array' => 'المفوض الضيف يجب أن يكون مصفوفة.',

    'no_authorizations_granted_by_you' => 'لا توجد تخويلات قمت بمنحها.',
    'authorizations_retrieved_successfully' => 'تم استرجاع جميع التخويلات بنجاح.',

    'create_authorization_success' => 'تم إنشاء التخويل بنجاح',

    'no_authorization_found' => 'لم يتم العثور على التخويل المطلوب.',
    'authorization_retrieved_successfully' => 'تم استرجاع التخويل بنجاح.',

    'authorized_user_id_prohibited_with' => 'لا يمكن إرسال authorized_user_id مع authorized_guest في نفس الوقت.',
    'authorized_guest_prohibited_with' => 'لا يمكن إرسال authorized_guest مع authorized_user_id في نفس الوقت.',

    'guest_first_name_required' => 'الاسم الأول للضيف مطلوب.',
    'guest_first_name_string' => 'الاسم الأول للضيف يجب أن يكون نصًا.',
    'guest_first_name_max' => 'الاسم الأول للضيف يجب ألا يزيد عن 50 حرفًا.',

    'guest_last_name_string' => 'اسم العائلة للضيف يجب أن يكون نصًا.',
    'guest_last_name_max' => 'اسم العائلة للضيف يجب ألا يزيد عن 50 حرفًا.',

    'guest_phone_required' => 'رقم هاتف الضيف مطلوب.',
    'guest_phone_string' => 'رقم هاتف الضيف يجب أن يكون نصًا.',
    'guest_phone_min' => 'رقم هاتف الضيف يجب ألا يقل عن 6 أحرف.',
    'guest_phone_max' => 'رقم هاتف الضيف يجب ألا يزيد عن 20 حرفًا.',
    'guest_phone_regex' => 'رقم هاتف الضيف غير صالح.',

    'guest_address_string' => 'عنوان الضيف يجب أن يكون نصًا.',
    'guest_address_max' => 'عنوان الضيف يجب ألا يزيد عن 255 حرفًا.',

    'guest_national_number_string' => 'الرقم الوطني للضيف يجب أن يكون نصًا.',
    'guest_national_number_max' => 'الرقم الوطني للضيف يجب ألا يزيد عن 20 حرفًا.',

    'guest_city_id_numeric' => 'معرف المدينة للضيف يجب أن يكون رقمًا.',
    'guest_city_id_exists' => 'المدينة المحددة للضيف غير موجودة.',

    'guest_birthday_date' => 'تاريخ ميلاد الضيف يجب أن يكون تاريخًا صالحًا.',
    'guest_birthday_before_today' => 'تاريخ ميلاد الضيف يجب أن يكون قبل اليوم الحالي.',

    // Guest user fields
    'authorized_guest_first_name_required' => 'الاسم الأول للضيف مطلوب.',
    'authorized_guest_first_name_string' => 'يجب أن يكون الاسم الأول للضيف نصًا.',
    'authorized_guest_first_name_max' => 'لا يمكن أن يزيد الاسم الأول للضيف عن 50 حرفًا.',

    'authorized_guest_last_name_string' => 'يجب أن يكون الاسم الأخير للضيف نصًا.',
    'authorized_guest_last_name_max' => 'لا يمكن أن يزيد الاسم الأخير للضيف عن 50 حرفًا.',

    'authorized_guest_phone_required' => 'رقم هاتف الضيف مطلوب.',
    'authorized_guest_phone_string' => 'يجب أن يكون رقم هاتف الضيف نصًا.',
    'authorized_guest_phone_min' => 'يجب ألا يقل رقم هاتف الضيف عن 6 أحرف.',
    'authorized_guest_phone_max' => 'لا يمكن أن يزيد رقم هاتف الضيف عن 20 حرفًا.',
    'authorized_guest_phone_regex' => 'صيغة رقم هاتف الضيف غير صالحة.',

    'authorized_guest_address_string' => 'يجب أن يكون عنوان الضيف نصًا.',
    'authorized_guest_address_max' => 'لا يمكن أن يزيد عنوان الضيف عن 255 حرفًا.',

    'authorized_guest_national_number_string' => 'يجب أن يكون الرقم الوطني للضيف نصًا.',
    'authorized_guest_national_number_max' => 'لا يمكن أن يزيد الرقم الوطني للضيف عن 20 حرفًا.',

    'authorized_guest_city_id_numeric' => 'يجب أن يكون معرف المدينة للضيف رقمًا.',
    'authorized_guest_city_id_exists' => 'المدينة المحددة غير موجودة.',

    'authorized_guest_birthday_date' => 'يجب أن يكون تاريخ ميلاد الضيف صالحًا.',
    'authorized_guest_birthday_before' => 'يجب أن يكون تاريخ ميلاد الضيف قبل اليوم.',

    // Other fields
    'used_at_date' => 'يجب أن يكون تاريخ الاستخدام صالحًا.',
    'cancellation_reason_string' => 'يجب أن يكون سبب الإلغاء نصًا.',

    'cannot_update_authorization' => 'لا يمكن تحديث التخويل.',
    'authorization_updated_successfully' => 'تم تحديث التخويل بنجاح.',
    'cannot_create_authorization' => 'لا يمكن إنشاء التخويل.',
    'no_authorizations_found' => 'لم يتم العثور على أي تخويلات.',
    'authorization_deleted_successfully' => 'تم حذف التخويل بنجاح.',

];
