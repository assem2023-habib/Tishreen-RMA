<?php

namespace App\Enums;

enum HttpStatus: int
{
    case OK = 200;
    case CREATED = 201;
    case NOT_CONTENT = 204; // الطلب ناجح لكن لا يوجد محتوى للارجاع
    case MOVED_PERMANETLY = 301; // تم نقل المورد نهائيا
    case FOUND = 302; // إعادة توجيه مؤقته
    case BAD_REQUEST = 400; // الطلب غير صالح
    case UNAUTHORIZED = 401; // المستخدم غير مصرح له بالدخول اي انه غير مصادق
    case FORBIDDEN = 403; // ممنوع الوصول للمورد
    case NOT_FOUND = 404; // المورد غير موجود
    case UNPROCESSABLE_ENTITY = 422; // البيانات المرسلة غير صالحة
    case INTERNET_SERVER_ERROR = 500; // خطأ داخلي في المخدم
    case BAD_GATEWAY = 502; // مشكلة في الخادم الوسيط
    case SERVICE_UNAVAILABLE = 503; // الخدمة غير متاحة مؤقتا
}
