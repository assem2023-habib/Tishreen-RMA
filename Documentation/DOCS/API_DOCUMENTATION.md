# وثائق API - نظام إدارة الطرود والشحن

## 📚 فهرس المحتويات

1. [نظرة عامة على API](#نظرة-عامة-على-api)
2. [المصادقة والتوثيق](#المصادقة-والتوثيق)
3. [رموز الحالة](#رموز-الحالة)
4. [معالجة الأخطاء](#معالجة-الأخطاء)
5. [Endpoints المصادقة](#endpoints-المصادقة)
6. [Endpoints الطرود](#endpoints-الطرود)
7. [Endpoints التخويل](#endpoints-التخويل)
8. [Endpoints المواعيد](#endpoints-المواعيد)
9. [Endpoints الفروع والمسارات](#endpoints-الفروع-والمسارات)
10. [Endpoints التقييم](#endpoints-التقييم)
11. [Endpoints Telegram](#endpoints-telegram)
12. [أمثلة الاستخدام](#أمثلة-الاستخدام)

---

## نظرة عامة على API

### Base URL
```
https://your-domain.com/api/v1
```

### Content Type
```
Content-Type: application/json
Accept: application/json
```

### Rate Limiting
- **Limit**: 6 requests per minute
- **Headers**: 
  - `X-RateLimit-Limit`
  - `X-RateLimit-Remaining`
  - `X-RateLimit-Reset`

---

## المصادقة والتوثيق

### Laravel Passport
النظام يستخدم Laravel Passport للمصادقة مع OAuth2.

### الحصول على Token
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

### استخدام Token
```http
Authorization: Bearer {access_token}
```

### تجديد Token
```http
POST /api/v1/refresh
Authorization: Bearer {refresh_token}
```

---

## رموز الحالة

| الكود | المعنى | الوصف |
|-------|--------|--------|
| 200 | OK | الطلب نجح |
| 201 | Created | تم إنشاء المورد بنجاح |
| 400 | Bad Request | طلب غير صحيح |
| 401 | Unauthorized | غير مصرح |
| 403 | Forbidden | ممنوع |
| 404 | Not Found | غير موجود |
| 422 | Unprocessable Entity | خطأ في التحقق |
| 429 | Too Many Requests | طلبات كثيرة |
| 500 | Internal Server Error | خطأ في الخادم |

---

## معالجة الأخطاء

### تنسيق الخطأ
```json
{
    "status": false,
    "message": "Error message",
    "errors": {
        "field_name": ["Error details"]
    }
}
```

### أمثلة الأخطاء
```json
{
    "status": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

## Endpoints المصادقة

### تسجيل مستخدم جديد
```http
POST /api/v1/register
```

**Request Body:**
```json
{
    "first_name": "أحمد",
    "last_name": "محمد",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+963912345678",
    "birthday": "1990-01-01",
    "city_id": 1,
    "national_number": "12345678901"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "تم إنشاء المستخدم والتوكن بنجاح.",
    "data": {
        "user": {
            "id": 1,
            "first_name": "أحمد",
            "last_name": "محمد",
            "email": "ahmed@example.com",
            "user_name": "ahmed_mohamed",
            "phone": "+963912345678",
            "city_id": 1,
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
    }
}
```

### تسجيل الدخول
```http
POST /api/v1/login
```

**Request Body:**
```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تسجيل الدخول بنجاح.",
    "data": {
        "user": {
            "id": 1,
            "first_name": "أحمد",
            "last_name": "محمد",
            "email": "ahmed@example.com",
            "user_name": "ahmed_mohamed",
            "phone": "+963912345678",
            "city_id": 1,
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
    }
}
```

### تسجيل الخروج
```http
GET /api/v1/logout
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تسجيل الخروج بنجاح."
}
```

### بيانات المستخدم الحالي
```http
GET /api/v1/me
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم استرجاع بيانات المستخدم المصادق عليه بنجاح.",
    "data": {
        "id": 1,
        "first_name": "أحمد",
        "last_name": "محمد",
        "email": "ahmed@example.com",
        "user_name": "ahmed_mohamed",
        "phone": "+963912345678",
        "city_id": 1,
        "email_verified_at": "2024-01-01T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### نسيان كلمة المرور
```http
POST /api/v1/forgot-password
```

**Request Body:**
```json
{
    "email": "ahmed@example.com"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم إرسال رمز التحقق (OTP)."
}
```

### تعيين كلمة مرور جديدة
```http
POST /api/v1/new-password
```

**Request Body:**
```json
{
    "email": "ahmed@example.com",
    "otp_code": "123456",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم إعادة تعيين كلمة المرور بنجاح."
}
```

### تحقق من البريد الإلكتروني
```http
POST /api/v1/verify-email
```

**Request Body:**
```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم إرسال رمز التحقق إلى بريدك الإلكتروني."
}
```

### تأكيد رمز التحقق
```http
POST /api/v1/confirm-email-otp
```

**Request Body:**
```json
{
    "email": "ahmed@example.com",
    "otp_code": "123456"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم التحقق من البريد الإلكتروني بنجاح."
}
```

---

## Endpoints الطرود

### قائمة الطرود
```http
GET /api/v1/parcel
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "all Parcels for the user : ahmed_mohamed",
    "data": {
        "parcels": [
            {
                "id": 1,
                "sender_id": 1,
                "sender_type": "User",
                "route_id": 1,
                "reciver_name": "محمد علي",
                "reciver_address": "دمشق، سوريا",
                "reciver_phone": "+963912345679",
                "weight": 2.500,
                "is_paid": 0,
                "parcel_status": "Pending",
                "tracking_number": "ABC123DEF4"
            }
        ]
    }
}
```

### إنشاء طرد جديد
```http
POST /api/v1/parcel
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "route_id": 1,
    "reciver_name": "محمد علي",
    "reciver_address": "دمشق، سوريا",
    "reciver_phone": "+963912345679",
    "weight": 2.5,
    "is_paid": false
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "parcel created successfuly",
    "data": {
        "parcel": {
            "id": 1,
            "sender_id": 1,
            "sender_type": "User",
            "route_id": 1,
            "reciver_name": "محمد علي",
            "reciver_address": "دمشق، سوريا",
            "reciver_phone": "+963912345679",
            "weight": 2.500,
            "cost": 1250.000,
            "is_paid": 0,
            "parcel_status": "Pending",
            "tracking_number": "ABC123DEF4",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### تفاصيل طرد
```http
GET /api/v1/parcel/{id}
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم ايجاد الطرد.",
    "data": {
        "parcel": {
            "id": 1,
            "sender_id": 1,
            "sender_type": "User",
            "route_id": 1,
            "reciver_name": "محمد علي",
            "reciver_address": "دمشق، سوريا",
            "reciver_phone": "+963912345679",
            "weight": 2.500,
            "cost": 1250.000,
            "is_paid": 0,
            "parcel_status": "Pending",
            "tracking_number": "ABC123DEF4"
        }
    }
}
```

### تحديث طرد
```http
PUT /api/v1/parcel/{id}
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "reciver_name": "محمد علي أحمد",
    "reciver_address": "حلب، سوريا",
    "reciver_phone": "+963912345680",
    "weight": 3.0
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تعديل الطرد بنجاح",
    "data": {
        "parcel": {
            "id": 1,
            "sender_id": 1,
            "sender_type": "User",
            "route_id": 1,
            "reciver_name": "محمد علي أحمد",
            "reciver_address": "حلب، سوريا",
            "reciver_phone": "+963912345680",
            "weight": 3.000,
            "cost": 1500.000,
            "is_paid": 0,
            "parcel_status": "Pending",
            "tracking_number": "ABC123DEF4",
            "updated_at": "2024-01-01T01:00:00.000000Z"
        }
    }
}
```

### حذف طرد
```http
DELETE /api/v1/parcel/{id}
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "id": 1
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم حذف الطرد بنجاح"
}
```

---

## Endpoints التخويل

### قائمة التخويلات
```http
GET /api/v1/authorization
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم استرجاع جميع التخويلات بنجاح.",
    "data": {
        "authorizations": [
            {
                "id": 1,
                "user_id": 1,
                "parcel_id": 1,
                "authorized_user_id": 2,
                "authorized_user_type": "User",
                "authorized_code": "XYZ789ABC1",
                "authorized_status": "active",
                "generated_at": "2024-01-01T00:00:00.000000Z",
                "expired_at": "2024-01-08T00:00:00.000000Z",
                "used_at": null,
                "cancellation_reason": null,
                "parcel": {
                    "id": 1,
                    "tracking_number": "ABC123DEF4",
                    "reciver_name": "محمد علي"
                },
                "authorizedUser": {
                    "id": 2,
                    "user_name": "sara_ahmed",
                    "first_name": "سارة",
                    "last_name": "أحمد"
                }
            }
        ]
    }
}
```

### إنشاء تخويل
```http
POST /api/v1/authorization
Authorization: Bearer {access_token}
```

**Request Body (مستخدم مسجل):**
```json
{
    "parcel_id": 1,
    "authorized_user_id": 2
}
```

**Request Body (مستخدم ضيف):**
```json
{
    "parcel_id": 1,
    "authorized_guest": [
        {
            "first_name": "سارة",
            "last_name": "أحمد",
            "phone": "+963912345680",
            "address": "دمشق، سوريا",
            "national_number": "12345678902",
            "city_id": 1,
            "birthday": "1992-05-15"
        }
    ]
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "تم إنشاء التخويل بنجاح",
    "data": {
        "authorization": {
            "id": 1,
            "user_id": 1,
            "parcel_id": 1,
            "authorized_user_id": 2,
            "authorized_user_type": "User",
            "authorized_code": "XYZ789ABC1",
            "authorized_status": "active",
            "generated_at": "2024-01-01T00:00:00.000000Z",
            "expired_at": "2024-01-08T00:00:00.000000Z",
            "used_at": null,
            "cancellation_reason": null
        }
    }
}
```

### تفاصيل تخويل
```http
GET /api/v1/authorization/{id}
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم استرجاع التخويل بنجاح.",
    "data": {
        "authorization": {
            "id": 1,
            "user_id": 1,
            "parcel_id": 1,
            "authorized_user_id": 2,
            "authorized_user_type": "User",
            "authorized_code": "XYZ789ABC1",
            "authorized_status": "active",
            "generated_at": "2024-01-01T00:00:00.000000Z",
            "expired_at": "2024-01-08T00:00:00.000000Z",
            "used_at": null,
            "cancellation_reason": null
        }
    }
}
```

### تحديث تخويل
```http
PUT /api/v1/authorization/{id}
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "authorized_user_id": 3,
    "cancellation_reason": "تغيير في الخطة"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تحديث التخويل بنجاح.",
    "data": {
        "authorization": {
            "id": 1,
            "user_id": 1,
            "parcel_id": 1,
            "authorized_user_id": 3,
            "authorized_user_type": "User",
            "authorized_code": "XYZ789ABC1",
            "authorized_status": "active",
            "generated_at": "2024-01-01T00:00:00.000000Z",
            "expired_at": "2024-01-08T00:00:00.000000Z",
            "used_at": null,
            "cancellation_reason": "تغيير في الخطة"
        }
    }
}
```

### استخدام تخويل
```http
POST /api/v1/authorization/use/{id}
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تسجيل استخدام التخويل بنجاح.",
    "data": {
        "authorization": {
            "id": 1,
            "user_id": 1,
            "parcel_id": 1,
            "authorized_user_id": 2,
            "authorized_user_type": "User",
            "authorized_code": "XYZ789ABC1",
            "authorized_status": "used",
            "generated_at": "2024-01-01T00:00:00.000000Z",
            "expired_at": "2024-01-08T00:00:00.000000Z",
            "used_at": "2024-01-02T10:30:00.000000Z",
            "cancellation_reason": null
        }
    }
}
```

### حذف تخويل
```http
DELETE /api/v1/authorization/{id}
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم حذف التخويل بنجاح."
}
```

---

## Endpoints المواعيد

### المواعيد المتاحة
```http
GET /api/v1/get-getCalender/{tracking_number}
```

**Response (200):**
```json
{
    "success": true,
    "parcel": {
        "id": 1,
        "tracking_number": "ABC123DEF4",
        "reciver_name": "محمد علي",
        "parcel_status": "In_transit"
    },
    "available_appointments": [
        {
            "id": 1,
            "branch_id": 2,
            "date": "2024-01-15",
            "time": "09:00:00",
            "booked": false
        },
        {
            "id": 2,
            "branch_id": 2,
            "date": "2024-01-15",
            "time": "10:00:00",
            "booked": false
        }
    ]
}
```

### حجز موعد
```http
POST /api/v1/book-appointment
```

**Request Body:**
```json
{
    "tracking_number": "ABC123DEF4",
    "appointment_id": 1,
    "user_id": 1
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Appointment successfully booked.",
    "appointment": {
        "id": 1,
        "user_id": 1,
        "branch_id": 2,
        "date": "2024-01-15",
        "time": "09:00:00",
        "booked": true
    }
}
```

---

## Endpoints الفروع والمسارات

### قائمة الدول
```http
GET /api/v1/countries
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "countries": [
            {
                "id": 1,
                "name": "سوريا",
                "code": "SY",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### مدن الدولة
```http
GET /api/v1/countries/{country_id}/cities
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "cities": [
            {
                "id": 1,
                "name": "دمشق",
                "country_id": 1,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### قائمة الفروع
```http
GET /api/v1/branches
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "branches": [
            {
                "id": 1,
                "branch_name": "فرع دمشق المركزي",
                "city_id": 1,
                "address": "شارع الحمراء، دمشق",
                "phone": "+963112345678",
                "email": "damascus@rma.com",
                "latitude": 33.5138,
                "longitude": 36.2765,
                "status": 1,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### فروع المدينة
```http
GET /api/v1/branches/{cityId}
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "branches": [
            {
                "id": 1,
                "branch_name": "فرع دمشق المركزي",
                "city_id": 1,
                "address": "شارع الحمراء، دمشق",
                "phone": "+963112345678",
                "email": "damascus@rma.com",
                "latitude": 33.5138,
                "longitude": 36.2765,
                "status": 1
            }
        ]
    }
}
```

### تفاصيل فرع
```http
GET /api/v1/branches/{id}/detail
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "branch": {
            "id": 1,
            "branch_name": "فرع دمشق المركزي",
            "city_id": 1,
            "address": "شارع الحمراء، دمشق",
            "phone": "+963112345678",
            "email": "damascus@rma.com",
            "latitude": 33.5138,
            "longitude": 36.2765,
            "status": 1,
            "city": {
                "id": 1,
                "name": "دمشق",
                "country_id": 1
            }
        }
    }
}
```

### المسارات
```http
GET /api/v1/routes
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "routes": [
            {
                "id": 1,
                "from_branch_id": 1,
                "to_branch_id": 2,
                "estimated_departure_time": "08:00:00",
                "estimated_arrival_time": "14:00:00",
                "from_branch": {
                    "id": 1,
                    "branch_name": "فرع دمشق المركزي"
                },
                "to_branch": {
                    "id": 2,
                    "branch_name": "فرع حلب المركزي"
                }
            }
        ]
    }
}
```

### مسارات اليوم
```http
GET /api/v1/routes/{day}
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "routes": [
            {
                "id": 1,
                "from_branch_id": 1,
                "to_branch_id": 2,
                "estimated_departure_time": "08:00:00",
                "estimated_arrival_time": "14:00:00",
                "day": "monday",
                "is_active": true
            }
        ]
    }
}
```

---

## Endpoints التقييم

### قائمة التقييمات
```http
GET /api/v1/rates
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "status": true,
    "data": {
        "rates": [
            {
                "id": 1,
                "user_id": 1,
                "rateable_id": 1,
                "rateable_type": "App\\Models\\Branch",
                "rating": 5,
                "comment": "خدمة ممتازة",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### إنشاء تقييم
```http
POST /api/v1/rates
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "rateable_id": 1,
    "rateable_type": "App\\Models\\Branch",
    "rating": 5,
    "comment": "خدمة ممتازة وسريعة"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "تم إنشاء التقييم بنجاح",
    "data": {
        "rate": {
            "id": 1,
            "user_id": 1,
            "rateable_id": 1,
            "rateable_type": "App\\Models\\Branch",
            "rating": 5,
            "comment": "خدمة ممتازة وسريعة",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

---

## Endpoints Telegram

### إرسال OTP عبر Telegram
```http
POST /api/v1/telegram/otp/send
```

**Request Body:**
```json
{
    "chat_id": "123456789"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "otp Send!"
}
```

### التحقق من OTP
```http
POST /api/v1/telegram/otp/verify
```

**Request Body:**
```json
{
    "chat_id": "123456789",
    "otp": "123456"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Otp verfied!."
}
```

### Webhook للبوت
```http
POST /api/v1/telegram/webhook
```

**Request Body:**
```json
{
    "update_id": 123456789,
    "message": {
        "message_id": 1,
        "from": {
            "id": 123456789,
            "is_bot": false,
            "first_name": "أحمد",
            "last_name": "محمد",
            "username": "ahmed_mohamed"
        },
        "chat": {
            "id": 123456789,
            "first_name": "أحمد",
            "last_name": "محمد",
            "username": "ahmed_mohamed",
            "type": "private"
        },
        "date": 1640995200,
        "text": "/start"
    }
}
```

---

## أمثلة الاستخدام

### مثال كامل: إنشاء طرد وتتبع حالته

#### 1. تسجيل الدخول
```bash
curl -X POST "https://your-domain.com/api/v1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "password": "password123"
  }'
```

#### 2. إنشاء طرد
```bash
curl -X POST "https://your-domain.com/api/v1/parcel" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "route_id": 1,
    "reciver_name": "محمد علي",
    "reciver_address": "دمشق، سوريا",
    "reciver_phone": "+963912345679",
    "weight": 2.5,
    "is_paid": false
  }'
```

#### 3. تتبع الطرد
```bash
curl -X GET "https://your-domain.com/api/v1/parcel/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

#### 4. إنشاء تخويل
```bash
curl -X POST "https://your-domain.com/api/v1/authorization" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "parcel_id": 1,
    "authorized_user_id": 2
  }'
```

#### 5. حجز موعد
```bash
curl -X GET "https://your-domain.com/api/v1/get-getCalender/ABC123DEF4"
```

```bash
curl -X POST "https://your-domain.com/api/v1/book-appointment" \
  -H "Content-Type: application/json" \
  -d '{
    "tracking_number": "ABC123DEF4",
    "appointment_id": 1,
    "user_id": 1
  }'
```

---

## ملاحظات مهمة

### 1. Rate Limiting
- الحد الأقصى: 6 طلبات في الدقيقة
- عند تجاوز الحد: HTTP 429

### 2. Pagination
- الدعم: `?page=1&per_page=10`
- Headers: `X-Total-Count`, `X-Per-Page`, `X-Current-Page`

### 3. Filtering
- الدعم: `?filter[status]=pending`
- Sorting: `?sort=created_at&order=desc`

### 4. Error Handling
- جميع الأخطاء تتبع نفس التنسيق
- رسائل الخطأ باللغة العربية والإنجليزية

### 5. Security
- جميع الطلبات المحمية تتطلب Bearer Token
- HTTPS مطلوب في الإنتاج
- CORS مُعد للاستخدام مع التطبيقات

---

**هذه الوثائق تغطي جميع API endpoints المتاحة في النظام مع أمثلة شاملة للاستخدام.**
