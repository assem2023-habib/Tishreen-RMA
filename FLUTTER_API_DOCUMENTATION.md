# 📱 توثيق API كامل لتطبيق Flutter

## 📚 فهرس المحتويات

1. [المعلومات الأساسية](#المعلومات-الأساسية)
2. [Headers المطلوبة](#headers-المطلوبة)
3. [بنية الاستجابة](#بنية-الاستجابة)
4. [رموز الحالة HTTP](#رموز-الحالة-http)
5. [Endpoints المصادقة](#endpoints-المصادقة)
6. [Endpoints الطرود](#endpoints-الطرود)
7. [Endpoints التخويل](#endpoints-التخويل)
8. [Endpoints المواعيد](#endpoints-المواعيد)
9. [Endpoints الفروع والمسارات](#endpoints-الفروع-والمسارات)
10. [Endpoints التقييم](#endpoints-التقييم)
11. [Endpoints Telegram OTP](#endpoints-telegram-otp)
12. [أمثلة Dart/Flutter](#أمثلة-dartflutter)

---

## المعلومات الأساسية

### Base URL

```
https://your-domain.com/api/v1
```

### Rate Limiting

-   **الحد الأقصى**: 6 طلبات في الدقيقة
-   **عند تجاوز الحد**: HTTP 429 Too Many Requests

---

## Headers المطلوبة

### للطلبات العامة (بدون مصادقة)

```dart
Map<String, String> headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json',
};
```

### للطلبات المحمية (تحتاج Token)

```dart
Map<String, String> headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': 'Bearer $accessToken',
};
```

> **⚠️ ملاحظة**: جميع الـ Endpoints المحمية تتطلب إرسال الـ Token في Header الـ Authorization

---

## بنية الاستجابة

### استجابة ناجحة (Success Response)

```json
{
    "status": true,
    "message": "رسالة النجاح",
    "data": {
        // البيانات المطلوبة
    }
}
```

### استجابة خطأ (Error Response)

```json
{
    "status": false,
    "message": "رسالة الخطأ",
    "errors": {
        "field_name": ["تفاصيل الخطأ"]
    }
}
```

---

## رموز الحالة HTTP

| الكود | المعنى            | الوصف           | استخدام Flutter          |
| ----- | ----------------- | --------------- | ------------------------ |
| 200   | OK                | الطلب نجح       | معالجة عادية             |
| 201   | Created           | تم إنشاء المورد | إنشاء ناجح               |
| 400   | Bad Request       | طلب غير صحيح    | عرض رسالة خطأ            |
| 401   | Unauthorized      | غير مصرح        | توجيه لتسجيل الدخول      |
| 403   | Forbidden         | ممنوع/محظور     | الحساب محظور أو مجمد     |
| 404   | Not Found         | غير موجود       | عرض "لا توجد بيانات"     |
| 409   | Conflict          | تعارض           | العملية موجودة مسبقاً    |
| 422   | Unprocessable     | خطأ في التحقق   | عرض أخطاء الـ Validation |
| 429   | Too Many Requests | طلبات كثيرة     | انتظار وإعادة المحاولة   |
| 500   | Server Error      | خطأ في الخادم   | عرض رسالة عامة           |

---

## Endpoints المصادقة

### 1️⃣ تسجيل مستخدم جديد

| العنصر            | التفاصيل                |
| ----------------- | ----------------------- |
| **Endpoint**      | `POST /api/v1/register` |
| **Auth Required** | ❌ لا                   |
| **Content-Type**  | `application/json`      |

#### Request Body

```json
{
    "first_name": "string | مطلوب | max:255",
    "last_name": "string | مطلوب | max:255",
    "email": "string | مطلوب | unique | email format",
    "password": "string | مطلوب | min:8 | must match confirmation",
    "password_confirmation": "string | مطلوب | same as password",
    "phone": "string | مطلوب | unique",
    "birthday": "date | مطلوب | format: YYYY-MM-DD",
    "city_id": "integer | مطلوب | must exist in cities table",
    "national_number": "string | مطلوب | exactly 11 digits | unique"
}
```

#### Example Request

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

#### Success Response (201)

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
        }
    }
}
```

#### Error Response (422)

```json
{
    "status": false,
    "message": "فشل التحقق",
    "errors": {
        "email": ["البريد الإلكتروني مستخدم بالفعل."],
        "national_number": ["الرقم الوطني يجب أن يكون 11 رقم."]
    }
}
```

---

### 2️⃣ تسجيل الدخول

| العنصر            | التفاصيل             |
| ----------------- | -------------------- |
| **Endpoint**      | `POST /api/v1/login` |
| **Auth Required** | ❌ لا                |
| **Content-Type**  | `application/json`   |

#### Request Body

```json
{
    "email": "string | مطلوب | min:8 | must exist in users",
    "password": "string | مطلوب | min:6"
}
```

#### Example Request

```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

#### Success Response (200)

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

#### Error Responses

##### 401 - بيانات خاطئة

```json
{
    "status": false,
    "message": "بيانات الاعتماد غير صحيحة",
    "errors": {
        "credentials": "البريد الإلكتروني أو كلمة المرور غير صحيحة"
    }
}
```

##### 403 - الحساب محظور

```json
{
    "status": false,
    "message": "تم حظر حسابك",
    "errors": {
        "reason": "انتهاك شروط الاستخدام"
    }
}
```

##### 403 - الحساب مجمد

```json
{
    "status": false,
    "message": "حسابك مجمد مؤقتاً",
    "errors": {
        "reason": "نشاط مشبوه",
        "ends_at": "2024-01-15T00:00:00.000000Z"
    }
}
```

> **💡 ملاحظة للـ Flutter**: احفظ الـ Token في Secure Storage واستخدمه في جميع الطلبات المحمية

---

### 3️⃣ تسجيل الخروج

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/logout`            |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم تسجيل الخروج بنجاح.",
    "data": null
}
```

---

### 4️⃣ بيانات المستخدم الحالي

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/me`                |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |

#### Success Response (200)

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

---

### 5️⃣ نسيان كلمة المرور

| العنصر            | التفاصيل                       |
| ----------------- | ------------------------------ |
| **Endpoint**      | `POST /api/v1/forgot-password` |
| **Auth Required** | ❌ لا                          |
| **Content-Type**  | `application/json`             |

#### Request Body

```json
{
    "email": "string | مطلوب | must exist in users"
}
```

#### Example Request

```json
{
    "email": "ahmed@example.com"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم إرسال رمز التحقق (OTP).",
    "data": null
}
```

---

### 6️⃣ تعيين كلمة مرور جديدة

| العنصر            | التفاصيل                    |
| ----------------- | --------------------------- |
| **Endpoint**      | `POST /api/v1/new-password` |
| **Auth Required** | ❌ لا                       |
| **Content-Type**  | `application/json`          |

#### Request Body

```json
{
    "email": "string | مطلوب",
    "otp_code": "string | مطلوب | 6 digits",
    "new_password": "string | مطلوب | min:8",
    "new_password_confirmation": "string | مطلوب | same as new_password"
}
```

#### Example Request

```json
{
    "email": "ahmed@example.com",
    "otp_code": "123456",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم إعادة تعيين كلمة المرور بنجاح.",
    "data": null
}
```

#### Error Response (422)

```json
{
    "status": false,
    "message": "رمز التحقق غير صحيح أو منتهي الصلاحية"
}
```

---

### 7️⃣ إرسال رمز التحقق للبريد

| العنصر            | التفاصيل                    |
| ----------------- | --------------------------- |
| **Endpoint**      | `POST /api/v1/verify-email` |
| **Auth Required** | ❌ لا                       |
| **Content-Type**  | `application/json`          |

#### Request Body

```json
{
    "email": "string | مطلوب | must exist in users",
    "password": "string | مطلوب"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم إرسال رمز التحقق إلى بريدك الإلكتروني.",
    "data": null
}
```

---

### 8️⃣ تأكيد رمز التحقق

| العنصر            | التفاصيل                         |
| ----------------- | -------------------------------- |
| **Endpoint**      | `POST /api/v1/confirm-email-otp` |
| **Auth Required** | ❌ لا                            |
| **Content-Type**  | `application/json`               |

#### Request Body

```json
{
    "email": "string | مطلوب",
    "otp_code": "string | مطلوب | 6 digits"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم التحقق من البريد الإلكتروني بنجاح.",
    "data": null
}
```

---

## Endpoints الطرود

### 1️⃣ قائمة طرود المستخدم

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/parcel`            |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |

#### Success Response (200)

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
                "weight": 2.5,
                "cost": 1250.0,
                "is_paid": 0,
                "parcel_status": "Pending",
                "tracking_number": "ABC123DEF4",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

---

### 2️⃣ إنشاء طرد جديد

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `POST /api/v1/parcel`           |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **Content-Type**  | `application/json`              |

#### Request Body

```json
{
    "route_id": "integer | مطلوب | must exist in branch_routes",
    "reciver_name": "string | مطلوب | min:2 | max:250",
    "reciver_address": "string | مطلوب | max:500",
    "reciver_phone": "string | مطلوب | min:6 | max:20 | format: +?digits",
    "weight": "numeric | مطلوب | min:0.1",
    "is_paid": "boolean | مطلوب | true/false"
}
```

#### Example Request

```json
{
    "route_id": 1,
    "reciver_name": "محمد علي",
    "reciver_address": "دمشق، سوريا، شارع الحمراء",
    "reciver_phone": "+963912345679",
    "weight": 2.5,
    "is_paid": false
}
```

#### Success Response (201)

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
            "reciver_address": "دمشق، سوريا، شارع الحمراء",
            "reciver_phone": "+963912345679",
            "weight": 2.5,
            "cost": 1250.0,
            "is_paid": 0,
            "parcel_status": "Pending",
            "tracking_number": "ABC123DEF4",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

> **💡 ملاحظة**: الـ `cost` و `tracking_number` يتم حسابهما تلقائياً من الخادم

---

### 3️⃣ تفاصيل طرد

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/parcel/{id}`       |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **URL Param**     | `id` - معرف الطرد               |

#### Success Response (200)

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
            "weight": 2.5,
            "cost": 1250.0,
            "is_paid": 0,
            "parcel_status": "Pending",
            "tracking_number": "ABC123DEF4"
        }
    }
}
```

---

### 4️⃣ تحديث طرد

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `PUT /api/v1/parcel/{id}`       |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **Content-Type**  | `application/json`              |
| **URL Param**     | `id` - معرف الطرد               |

#### Request Body (جميع الحقول اختيارية)

```json
{
    "reciver_name": "string | اختياري | min:2 | max:250",
    "reciver_address": "string | اختياري | max:500",
    "reciver_phone": "string | اختياري | min:6 | max:20",
    "weight": "numeric | اختياري | min:0.1"
}
```

#### Example Request

```json
{
    "reciver_name": "محمد علي أحمد",
    "weight": 3.0
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم تعديل الطرد بنجاح",
    "data": {
        "parcel": {
            "id": 1,
            "reciver_name": "محمد علي أحمد",
            "weight": 3.0,
            "cost": 1500.0,
            "updated_at": "2024-01-01T01:00:00.000000Z"
        }
    }
}
```

---

### 5️⃣ حذف طرد

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `DELETE /api/v1/parcel/{id}`    |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **Content-Type**  | `application/json`              |

#### Request Body

```json
{
    "id": "integer | مطلوب"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم حذف الطرد بنجاح",
    "data": []
}
```

---

## Endpoints التخويل

### 1️⃣ قائمة التخويلات

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/authorization`     |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |

#### Success Response (200)

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

---

### 2️⃣ إنشاء تخويل

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `POST /api/v1/authorization`    |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **Content-Type**  | `application/json`              |

#### الحالة الأولى: تخويل مستخدم مسجل

```json
{
    "parcel_id": "integer | مطلوب | must exist in parcels",
    "authorized_user_id": "integer | اختياري | must exist in users | must be different from current user"
}
```

#### Example Request (مستخدم مسجل)

```json
{
    "parcel_id": 1,
    "authorized_user_id": 2
}
```

#### الحالة الثانية: تخويل ضيف (Guest)

```json
{
    "parcel_id": "integer | مطلوب",
    "authorized_guest": [
        {
            "first_name": "string | مطلوب | max:50",
            "last_name": "string | اختياري | max:50",
            "phone": "string | مطلوب | min:6 | max:20 | format: +?digits",
            "address": "string | اختياري | max:255",
            "national_number": "string | اختياري | max:20",
            "city_id": "integer | اختياري | must exist in cities",
            "birthday": "date | اختياري | must be before today"
        }
    ]
}
```

#### Example Request (ضيف)

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

#### Success Response (201)

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
            "expired_at": "2024-01-08T00:00:00.000000Z"
        }
    }
}
```

#### Error Response (409 - تعارض)

```json
{
    "status": false,
    "message": "يوجد تخويل نشط بالفعل لهذا الطرد"
}
```

---

### 3️⃣ تفاصيل تخويل

| العنصر            | التفاصيل                         |
| ----------------- | -------------------------------- |
| **Endpoint**      | `GET /api/v1/authorization/{id}` |
| **Auth Required** | ✅ نعم                           |
| **Headers**       | `Authorization: Bearer {token}`  |
| **URL Param**     | `id` - معرف التخويل              |

#### Success Response (200)

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

---

### 4️⃣ تحديث تخويل

| العنصر            | التفاصيل                         |
| ----------------- | -------------------------------- |
| **Endpoint**      | `PUT /api/v1/authorization/{id}` |
| **Auth Required** | ✅ نعم                           |
| **Headers**       | `Authorization: Bearer {token}`  |
| **Content-Type**  | `application/json`               |

#### Request Body

```json
{
    "authorized_user_id": "integer | اختياري",
    "cancellation_reason": "string | اختياري"
}
```

---

### 5️⃣ استخدام تخويل

| العنصر            | التفاصيل                              |
| ----------------- | ------------------------------------- |
| **Endpoint**      | `POST /api/v1/authorization/use/{id}` |
| **Auth Required** | ✅ نعم                                |
| **Headers**       | `Authorization: Bearer {token}`       |
| **URL Param**     | `id` - معرف التخويل                   |

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم تسجيل استخدام التخويل بنجاح.",
    "data": {
        "authorization": {
            "id": 1,
            "authorized_status": "used",
            "used_at": "2024-01-02T10:30:00.000000Z"
        }
    }
}
```

---

### 6️⃣ حذف تخويل

| العنصر            | التفاصيل                            |
| ----------------- | ----------------------------------- |
| **Endpoint**      | `DELETE /api/v1/authorization/{id}` |
| **Auth Required** | ✅ نعم                              |
| **Headers**       | `Authorization: Bearer {token}`     |

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم حذف التخويل بنجاح.",
    "data": []
}
```

---

## Endpoints المواعيد

### 1️⃣ المواعيد المتاحة لطرد

| العنصر            | التفاصيل                                        |
| ----------------- | ----------------------------------------------- |
| **Endpoint**      | `GET /api/v1/get-getCalender/{tracking_number}` |
| **Auth Required** | ❌ لا                                           |
| **URL Param**     | `tracking_number` - رقم تتبع الطرد              |

#### Success Response (200)

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

---

### 2️⃣ حجز موعد

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `POST /api/v1/book-appointment` |
| **Auth Required** | ❌ لا                           |
| **Content-Type**  | `application/json`              |

#### Request Body

```json
{
    "tracking_number": "string | مطلوب | must exist in parcels",
    "appointment_id": "integer | مطلوب | must exist in appointments",
    "user_id": "integer | مطلوب | must exist in users"
}
```

#### Example Request

```json
{
    "tracking_number": "ABC123DEF4",
    "appointment_id": 1,
    "user_id": 1
}
```

#### Success Response (200)

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

#### Error Response (400)

```json
{
    "success": false,
    "message": "This appointment is already booked."
}
```

---

## Endpoints الفروع والمسارات

### 1️⃣ قائمة الدول

| العنصر            | التفاصيل                |
| ----------------- | ----------------------- |
| **Endpoint**      | `GET /api/v1/countries` |
| **Auth Required** | ❌ لا                   |

#### Success Response (200)

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

---

### 2️⃣ مدن الدولة

| العنصر            | التفاصيل                                    |
| ----------------- | ------------------------------------------- |
| **Endpoint**      | `GET /api/v1/countries/{country_id}/cities` |
| **Auth Required** | ❌ لا                                       |
| **URL Param**     | `country_id` - معرف الدولة                  |

#### Success Response (200)

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

---

### 3️⃣ قائمة الفروع

| العنصر            | التفاصيل               |
| ----------------- | ---------------------- |
| **Endpoint**      | `GET /api/v1/branches` |
| **Auth Required** | ❌ لا                  |

#### Success Response (200)

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

---

### 4️⃣ فروع المدينة

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/branches/{cityId}` |
| **Auth Required** | ❌ لا                           |
| **URL Param**     | `cityId` - معرف المدينة         |

---

### 5️⃣ تفاصيل فرع

| العنصر            | التفاصيل                           |
| ----------------- | ---------------------------------- |
| **Endpoint**      | `GET /api/v1/branches/{id}/detail` |
| **Auth Required** | ❌ لا                              |
| **URL Param**     | `id` - معرف الفرع                  |

#### Success Response (200)

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

---

### 6️⃣ المسارات النشطة

| العنصر            | التفاصيل             |
| ----------------- | -------------------- |
| **Endpoint**      | `GET /api/v1/routes` |
| **Auth Required** | ❌ لا                |

#### Success Response (200)

```json
{
    "status": true,
    "message": "all routes get successfully",
    "data": {
        "routes": [
            {
                "from_branch_id": 1,
                "to_branch_id": 2,
                "day": "monday",
                "estimated_departur_time": "08:00:00",
                "estimated_arrival_time": "14:00:00",
                "distance_per_kilo": 350
            }
        ]
    }
}
```

---

### 7️⃣ مسارات اليوم

| العنصر            | التفاصيل                                  |
| ----------------- | ----------------------------------------- |
| **Endpoint**      | `GET /api/v1/routes/{day}`                |
| **Auth Required** | ❌ لا                                     |
| **URL Param**     | `day` - اسم اليوم (monday, tuesday, etc.) |

---

### 8️⃣ سياسة التسعير

| العنصر            | التفاصيل                     |
| ----------------- | ---------------------------- |
| **Endpoint**      | `GET /api/v1/pricing-policy` |
| **Auth Required** | ❌ لا                        |

---

## Endpoints التقييم

### 1️⃣ قائمة التقييمات

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `GET /api/v1/rates`             |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |

#### Success Response (200)

```json
{
    "status": true,
    "message": "تم استرجاع التقييمات بنجاح",
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

---

### 2️⃣ إنشاء تقييم

| العنصر            | التفاصيل                        |
| ----------------- | ------------------------------- |
| **Endpoint**      | `POST /api/v1/rates`            |
| **Auth Required** | ✅ نعم                          |
| **Headers**       | `Authorization: Bearer {token}` |
| **Content-Type**  | `application/json`              |

#### Request Body

```json
{
    "rateable_id": "integer | اختياري",
    "rateable_type": "string | اختياري | required with rateable_id",
    "rating": "integer | مطلوب | min:0 | max:5",
    "comment": "string | اختياري | nullable | max:400"
}
```

#### Example Request

```json
{
    "rateable_id": 1,
    "rateable_type": "App\\Models\\Branch",
    "rating": 5,
    "comment": "خدمة ممتازة وسريعة"
}
```

#### Success Response (201)

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

### 3️⃣ تفاصيل تقييم

| العنصر            | التفاصيل                 |
| ----------------- | ------------------------ |
| **Endpoint**      | `GET /api/v1/rates/{id}` |
| **Auth Required** | ✅ نعم                   |

---

### 4️⃣ تحديث تقييم

| العنصر            | التفاصيل                 |
| ----------------- | ------------------------ |
| **Endpoint**      | `PUT /api/v1/rates/{id}` |
| **Auth Required** | ✅ نعم                   |

---

### 5️⃣ حذف تقييم

| العنصر            | التفاصيل                    |
| ----------------- | --------------------------- |
| **Endpoint**      | `DELETE /api/v1/rates/{id}` |
| **Auth Required** | ✅ نعم                      |

---

## Endpoints Telegram OTP

### 1️⃣ إرسال OTP عبر Telegram

| العنصر            | التفاصيل                         |
| ----------------- | -------------------------------- |
| **Endpoint**      | `POST /api/v1/telegram/otp/send` |
| **Auth Required** | ❌ لا                            |
| **Content-Type**  | `application/json`               |

#### Request Body

```json
{
    "chat_id": "string | مطلوب"
}
```

#### Example Request

```json
{
    "chat_id": "123456789"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "otp Send!",
    "data": []
}
```

---

### 2️⃣ التحقق من OTP

| العنصر            | التفاصيل                           |
| ----------------- | ---------------------------------- |
| **Endpoint**      | `POST /api/v1/telegram/otp/verify` |
| **Auth Required** | ❌ لا                              |
| **Content-Type**  | `application/json`                 |

#### Request Body

```json
{
    "chat_id": "string | مطلوب",
    "otp": "string | مطلوب"
}
```

#### Example Request

```json
{
    "chat_id": "123456789",
    "otp": "123456"
}
```

#### Success Response (200)

```json
{
    "status": true,
    "message": "Otp verfied!.",
    "data": []
}
```

#### Error Response (422)

```json
{
    "status": false,
    "message": "invalid or Expire Otp"
}
```

---

## أمثلة Dart/Flutter

### 1️⃣ إعداد HTTP Client

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiClient {
  static const String baseUrl = 'https://your-domain.com/api/v1';
  String? _token;

  // Headers بدون Token
  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  // Headers مع Token
  Map<String, String> get _authHeaders => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': 'Bearer $_token',
  };

  void setToken(String token) {
    _token = token;
  }

  void clearToken() {
    _token = null;
  }
}
```

### 2️⃣ تسجيل الدخول

```dart
Future<LoginResult> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('$baseUrl/login'),
    headers: _headers,
    body: jsonEncode({
      'email': email,
      'password': password,
    }),
  );

  final data = jsonDecode(response.body);

  if (response.statusCode == 200 && data['status'] == true) {
    // حفظ Token
    final token = data['data']['token'];
    setToken(token);

    // حفظ في Secure Storage
    await secureStorage.write(key: 'auth_token', value: token);

    return LoginResult.success(
      user: User.fromJson(data['data']['user']),
      token: token,
    );
  } else if (response.statusCode == 403) {
    // الحساب محظور أو مجمد
    return LoginResult.banned(
      reason: data['errors']['reason'],
      endsAt: data['errors']['ends_at'],
    );
  } else {
    return LoginResult.error(message: data['message']);
  }
}
```

### 3️⃣ إنشاء طرد

```dart
Future<Parcel?> createParcel({
  required int routeId,
  required String receiverName,
  required String receiverAddress,
  required String receiverPhone,
  required double weight,
  required bool isPaid,
}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/parcel'),
    headers: _authHeaders,
    body: jsonEncode({
      'route_id': routeId,
      'reciver_name': receiverName,
      'reciver_address': receiverAddress,
      'reciver_phone': receiverPhone,
      'weight': weight,
      'is_paid': isPaid,
    }),
  );

  final data = jsonDecode(response.body);

  if (response.statusCode == 201 && data['status'] == true) {
    return Parcel.fromJson(data['data']['parcel']);
  }

  throw ApiException(
    message: data['message'],
    errors: data['errors'],
  );
}
```

### 4️⃣ معالجة الأخطاء العامة

```dart
class ApiException implements Exception {
  final String message;
  final Map<String, dynamic>? errors;
  final int? statusCode;

  ApiException({
    required this.message,
    this.errors,
    this.statusCode,
  });
}

Future<T> handleApiCall<T>(
  Future<http.Response> call,
  T Function(Map<String, dynamic>) parser
) async {
  try {
    final response = await call;
    final data = jsonDecode(response.body);

    switch (response.statusCode) {
      case 200:
      case 201:
        if (data['status'] == true) {
          return parser(data);
        }
        throw ApiException(message: data['message']);

      case 401:
        throw AuthException();

      case 422:
        throw ValidationException(
          message: data['message'],
          errors: data['errors'],
        );

      case 429:
        throw RateLimitException();

      default:
        throw ApiException(message: data['message'] ?? 'خطأ غير متوقع');
    }
  } catch (e) {
    if (e is ApiException) rethrow;
    throw ApiException(message: 'خطأ في الاتصال');
  }
}
```

### 5️⃣ نموذج User

```dart
class User {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final String userName;
  final String phone;
  final int cityId;
  final DateTime? emailVerifiedAt;
  final DateTime createdAt;
  final DateTime updatedAt;

  User({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.userName,
    required this.phone,
    required this.cityId,
    this.emailVerifiedAt,
    required this.createdAt,
    required this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      firstName: json['first_name'],
      lastName: json['last_name'],
      email: json['email'],
      userName: json['user_name'],
      phone: json['phone'],
      cityId: json['city_id'],
      emailVerifiedAt: json['email_verified_at'] != null
          ? DateTime.parse(json['email_verified_at'])
          : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}
```

### 6️⃣ نموذج Parcel

```dart
class Parcel {
  final int id;
  final int senderId;
  final String senderType;
  final int routeId;
  final String receiverName;
  final String receiverAddress;
  final String receiverPhone;
  final double weight;
  final double cost;
  final bool isPaid;
  final String parcelStatus;
  final String trackingNumber;
  final DateTime createdAt;
  final DateTime updatedAt;

  Parcel({
    required this.id,
    required this.senderId,
    required this.senderType,
    required this.routeId,
    required this.receiverName,
    required this.receiverAddress,
    required this.receiverPhone,
    required this.weight,
    required this.cost,
    required this.isPaid,
    required this.parcelStatus,
    required this.trackingNumber,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Parcel.fromJson(Map<String, dynamic> json) {
    return Parcel(
      id: json['id'],
      senderId: json['sender_id'],
      senderType: json['sender_type'],
      routeId: json['route_id'],
      receiverName: json['reciver_name'],
      receiverAddress: json['reciver_address'],
      receiverPhone: json['reciver_phone'],
      weight: double.parse(json['weight'].toString()),
      cost: double.parse(json['cost'].toString()),
      isPaid: json['is_paid'] == 1,
      parcelStatus: json['parcel_status'],
      trackingNumber: json['tracking_number'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}
```

---

## ملاحظات مهمة للمطور

### 1. Rate Limiting

```dart
// عند استلام 429، انتظر ثم أعد المحاولة
if (response.statusCode == 429) {
  await Future.delayed(Duration(minutes: 1));
  return retry();
}
```

### 2. Token Expiration

```dart
// عند استلام 401، أعد توجيه المستخدم لتسجيل الدخول
if (response.statusCode == 401) {
  await secureStorage.delete(key: 'auth_token');
  Navigator.pushReplacementNamed(context, '/login');
}
```

### 3. Validation Errors

```dart
// عرض أخطاء التحقق للمستخدم
if (data['errors'] != null) {
  data['errors'].forEach((field, messages) {
    showError('$field: ${messages.first}');
  });
}
```

---

**تم إنشاء هذا التوثيق في**: 2024-12-26

**الإصدار**: 1.0.0
