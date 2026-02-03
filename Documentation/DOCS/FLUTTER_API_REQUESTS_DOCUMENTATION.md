# 📋 توثيق Form Requests الكامل - نظام إدارة الطرود

دليل شامل لجميع Form Requests وقواعد التحقق (Validation) لبناء تطبيق Flutter.

---

## 📚 فهرس المحتويات

1. [المصادقة (Auth)](#1-المصادقة-auth)
2. [الطرود (Parcel)](#2-الطرود-parcel)
3. [التخويلات (Authorization)](#3-التخويلات-authorization)
4. [التقييمات (Rate)](#4-التقييمات-rate)
5. [Telegram OTP](#5-telegram-otp)
6. [Enums (القيم الثابتة)](#6--enums-القيم-الثابتة)

---

## 1. المصادقة (Auth)

### 1.1 RegisterRequest - تسجيل مستخدم جديد

**Endpoint:** `POST /api/v1/register`

| الحقل                   | النوع   | القواعد                 | مطلوب | الوصف             |
| ----------------------- | ------- | ----------------------- | ----- | ----------------- |
| `first_name`            | string  | max:255                 | ✅    | الاسم الأول       |
| `last_name`             | string  | max:255                 | ✅    | الاسم الأخير      |
| `email`                 | string  | email, unique:users     | ✅    | البريد الإلكتروني |
| `password`              | string  | min:8, confirmed        | ✅    | كلمة المرور       |
| `password_confirmation` | string  | -                       | ✅    | تأكيد كلمة المرور |
| `phone`                 | string  | unique:users            | ✅    | رقم الهاتف        |
| `birthday`              | date    | -                       | ✅    | تاريخ الميلاد     |
| `city_id`               | integer | exists:cities,id        | ✅    | معرف المدينة      |
| `national_number`       | string  | digits:11, unique:users | ✅    | الرقم الوطني      |

**مثال الطلب:**

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

**Flutter Validation:**

```dart
class RegisterValidator {
  static String? validateFirstName(String? value) {
    if (value == null || value.isEmpty) return 'الاسم الأول مطلوب';
    if (value.length > 255) return 'الاسم طويل جداً';
    return null;
  }

  static String? validateEmail(String? value) {
    if (value == null || value.isEmpty) return 'البريد الإلكتروني مطلوب';
    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
      return 'بريد إلكتروني غير صحيح';
    }
    return null;
  }

  static String? validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'كلمة المرور مطلوبة';
    if (value.length < 8) return 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    return null;
  }

  static String? validatePhone(String? value) {
    if (value == null || value.isEmpty) return 'رقم الهاتف مطلوب';
    return null;
  }

  static String? validateNationalNumber(String? value) {
    if (value == null || value.isEmpty) return 'الرقم الوطني مطلوب';
    if (value.length != 11 || !RegExp(r'^\d{11}$').hasMatch(value)) {
      return 'الرقم الوطني يجب أن يكون 11 رقم';
    }
    return null;
  }
}
```

---

### 1.2 LoginRequest - تسجيل الدخول

**Endpoint:** `POST /api/v1/login`

| الحقل      | النوع  | القواعد                    | مطلوب | الوصف             |
| ---------- | ------ | -------------------------- | ----- | ----------------- |
| `email`    | string | email, min:8, exists:users | ✅    | البريد الإلكتروني |
| `password` | string | min:6                      | ✅    | كلمة المرور       |

**مثال الطلب:**

```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

**Flutter Validation:**

```dart
class LoginValidator {
  static String? validateEmail(String? value) {
    if (value == null || value.isEmpty) return 'البريد الإلكتروني مطلوب';
    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
      return 'بريد إلكتروني غير صحيح';
    }
    if (value.length < 8) return 'البريد قصير جداً';
    return null;
  }

  static String? validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'كلمة المرور مطلوبة';
    if (value.length < 6) return 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    return null;
  }
}
```

---

### 1.3 ForgetPasswordRequest - نسيان كلمة المرور

**Endpoint:** `POST /api/v1/forgot-password`

| الحقل   | النوع  | القواعد             | مطلوب | الوصف                    |
| ------- | ------ | ------------------- | ----- | ------------------------ |
| `email` | string | email, exists:users | ✅    | البريد الإلكتروني المسجل |

**مثال الطلب:**

```json
{
    "email": "ahmed@example.com"
}
```

---

### 1.4 VerifyOtpAndReset - تعيين كلمة مرور جديدة

**Endpoint:** `POST /api/v1/new-password`

| الحقل          | النوع  | القواعد             | مطلوب | الوصف                |
| -------------- | ------ | ------------------- | ----- | -------------------- |
| `email`        | string | email, exists:users | ✅    | البريد الإلكتروني    |
| `otp_code`     | string | digits:6            | ✅    | رمز التحقق (6 أرقام) |
| `new_password` | string | min:8, regex        | ✅    | كلمة المرور الجديدة  |

**قاعدة الـ Regex لكلمة المرور:** `/^[A-Za-z0-9@#$%^&*!]+$/`

**مثال الطلب:**

```json
{
    "email": "ahmed@example.com",
    "otp_code": "123456",
    "new_password": "NewPassword123!"
}
```

**Flutter Validation:**

```dart
static String? validateOtp(String? value) {
  if (value == null || value.isEmpty) return 'رمز التحقق مطلوب';
  if (!RegExp(r'^\d{6}$').hasMatch(value)) return 'رمز التحقق يجب أن يكون 6 أرقام';
  return null;
}

static String? validateNewPassword(String? value) {
  if (value == null || value.isEmpty) return 'كلمة المرور مطلوبة';
  if (value.length < 8) return 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
  if (!RegExp(r'^[A-Za-z0-9@#$%^&*!]+$').hasMatch(value)) {
    return 'كلمة المرور تحتوي على رموز غير مسموحة';
  }
  return null;
}
```

---

### 1.5 VerifyEmailCodeRequest - التحقق من البريد

**Endpoint:** `POST /api/v1/verify-email`

| الحقل      | النوع  | القواعد             | مطلوب | الوصف             |
| ---------- | ------ | ------------------- | ----- | ----------------- |
| `email`    | string | email, exists:users | ✅    | البريد الإلكتروني |
| `password` | string | -                   | ✅    | كلمة المرور       |

**مثال الطلب:**

```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

---

### 1.6 ConfirmEmailOtpAndVerifyEmailRequest - تأكيد OTP البريد

**Endpoint:** `POST /api/v1/confirm-email-otp`

| الحقل      | النوع  | القواعد             | مطلوب | الوصف             |
| ---------- | ------ | ------------------- | ----- | ----------------- |
| `email`    | string | email, exists:users | ✅    | البريد الإلكتروني |
| `otp_code` | string | digits:6            | ✅    | رمز التحقق        |

**مثال الطلب:**

```json
{
    "email": "ahmed@example.com",
    "otp_code": "123456"
}
```

---

### 1.7 ResetPasswordRequest - إعادة تعيين كلمة المرور (مع Token)

**Endpoint:** `POST /api/v1/reset-password` (يتطلب مصادقة)

| الحقل      | النوع  | القواعد             | مطلوب | الوصف               |
| ---------- | ------ | ------------------- | ----- | ------------------- |
| `email`    | string | email, exists:users | ✅    | البريد الإلكتروني   |
| `token`    | string | -                   | ✅    | توكن إعادة التعيين  |
| `password` | string | min:8, regex        | ✅    | كلمة المرور الجديدة |

---

## 2. الطرود (Parcel)

### 2.1 StoreParcelRequest - إنشاء طرد جديد

**Endpoint:** `POST /api/v1/parcel` (يتطلب مصادقة)

| الحقل             | النوع   | القواعد                 | مطلوب | الوصف            |
| ----------------- | ------- | ----------------------- | ----- | ---------------- |
| `route_id`        | integer | exists:branch_routes,id | ✅    | معرف المسار      |
| `reciver_name`    | string  | min:2, max:250          | ✅    | اسم المستلم      |
| `reciver_address` | string  | max:500                 | ✅    | عنوان المستلم    |
| `reciver_phone`   | string  | min:6, max:20, regex    | ✅    | رقم هاتف المستلم |
| `weight`          | decimal | min:0.1                 | ✅    | الوزن (كجم)      |
| `is_paid`         | boolean | -                       | ✅    | هل تم الدفع؟     |

**قاعدة الـ Regex للهاتف:** `/^\+?\d+$/`

**مثال الطلب:**

```json
{
    "route_id": 1,
    "reciver_name": "محمد علي",
    "reciver_address": "دمشق، شارع الثورة",
    "reciver_phone": "+963912345678",
    "weight": 2.5,
    "is_paid": false
}
```

**Flutter Model & Validation:**

```dart
class CreateParcelRequest {
  final int routeId;
  final String reciverName;
  final String reciverAddress;
  final String reciverPhone;
  final double weight;
  final bool isPaid;

  CreateParcelRequest({
    required this.routeId,
    required this.reciverName,
    required this.reciverAddress,
    required this.reciverPhone,
    required this.weight,
    required this.isPaid,
  });

  Map<String, dynamic> toJson() => {
    'route_id': routeId,
    'reciver_name': reciverName,
    'reciver_address': reciverAddress,
    'reciver_phone': reciverPhone,
    'weight': weight,
    'is_paid': isPaid,
  };

  // Validation
  static String? validateReciverName(String? value) {
    if (value == null || value.isEmpty) return 'اسم المستلم مطلوب';
    if (value.length < 2) return 'الاسم قصير جداً (حرفين على الأقل)';
    if (value.length > 250) return 'الاسم طويل جداً';
    return null;
  }

  static String? validateReciverAddress(String? value) {
    if (value == null || value.isEmpty) return 'العنوان مطلوب';
    if (value.length > 500) return 'العنوان طويل جداً';
    return null;
  }

  static String? validateReciverPhone(String? value) {
    if (value == null || value.isEmpty) return 'رقم الهاتف مطلوب';
    if (value.length < 6) return 'رقم الهاتف قصير جداً';
    if (value.length > 20) return 'رقم الهاتف طويل جداً';
    if (!RegExp(r'^\+?\d+$').hasMatch(value)) return 'رقم هاتف غير صحيح';
    return null;
  }

  static String? validateWeight(String? value) {
    if (value == null || value.isEmpty) return 'الوزن مطلوب';
    final weight = double.tryParse(value);
    if (weight == null) return 'الوزن يجب أن يكون رقماً';
    if (weight < 0.1) return 'الوزن يجب أن يكون 0.1 كجم على الأقل';
    return null;
  }
}
```

---

### 2.2 UpdateParcelRequest - تحديث طرد

**Endpoint:** `PUT /api/v1/parcel/{id}` (يتطلب مصادقة)

| الحقل             | النوع   | القواعد                    | مطلوب | الوصف              |
| ----------------- | ------- | -------------------------- | ----- | ------------------ |
| `sender_id`       | integer | exists:users,id            | ❌    | معرف المرسل        |
| `route_id`        | integer | exists:branch_routes,id    | ❌    | معرف المسار        |
| `reciver_name`    | string  | min:2, max:255             | ❌    | اسم المستلم        |
| `reciver_address` | string  | min:2, max:255             | ❌    | عنوان المستلم      |
| `reciver_phone`   | string  | min:6, max:20, regex       | ❌    | رقم هاتف المستلم   |
| `weight`          | decimal | min:0.1                    | ❌    | الوزن (كجم)        |
| `price_policy_id` | integer | exists:pricing_policies,id | ❌    | معرف سياسة التسعير |

> ⚠️ **ملاحظة:** جميع الحقول اختيارية (sometimes) - يتم تحديث الحقول المُرسلة فقط.

**مثال الطلب:**

```json
{
    "reciver_name": "محمد علي أحمد",
    "weight": 3.0
}
```

---

### 2.3 DeleteParcelRequest - حذف طرد

**Endpoint:** `DELETE /api/v1/parcel/{id}` (يتطلب مصادقة)

| الحقل | النوع   | القواعد           | مطلوب | الوصف               |
| ----- | ------- | ----------------- | ----- | ------------------- |
| `id`  | integer | exists:parcels,id | ✅    | معرف الطرد (من URL) |

> ℹ️ **ملاحظة:** المعرف يُستخرج تلقائياً من الـ URL parameter.

---

## 3. التخويلات (Authorization)

### 3.1 StoreAuthorizationRequest - إنشاء تخويل

**Endpoint:** `POST /api/v1/authorization` (يتطلب مصادقة)

#### الخيار 1: تخويل مستخدم مسجل

| الحقل                | النوع   | القواعد                            | مطلوب | الوصف                  |
| -------------------- | ------- | ---------------------------------- | ----- | ---------------------- |
| `parcel_id`          | integer | exists:parcels,id                  | ✅    | معرف الطرد             |
| `authorized_user_id` | integer | exists:users,id, different:user_id | ❌    | معرف المستخدم المُخوّل |

**مثال:**

```json
{
    "parcel_id": 1,
    "authorized_user_id": 5
}
```

#### الخيار 2: تخويل ضيف (Guest)

| الحقل                                | النوع   | القواعد              | مطلوب | الوصف               |
| ------------------------------------ | ------- | -------------------- | ----- | ------------------- |
| `parcel_id`                          | integer | exists:parcels,id    | ✅    | معرف الطرد          |
| `authorized_guest`                   | array   | -                    | ❌    | مصفوفة بيانات الضيف |
| `authorized_guest.*.first_name`      | string  | max:50               | ✅    | الاسم الأول         |
| `authorized_guest.*.last_name`       | string  | max:50               | ❌    | الاسم الأخير        |
| `authorized_guest.*.phone`           | string  | min:6, max:20, regex | ✅    | رقم الهاتف          |
| `authorized_guest.*.address`         | string  | max:255              | ❌    | العنوان             |
| `authorized_guest.*.national_number` | string  | max:20               | ❌    | الرقم الوطني        |
| `authorized_guest.*.city_id`         | integer | exists:cities,id     | ❌    | معرف المدينة        |
| `authorized_guest.*.birthday`        | date    | before:today         | ❌    | تاريخ الميلاد       |

**مثال:**

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
            "birthday": "1995-05-15"
        }
    ]
}
```

**Flutter Model:**

```dart
class CreateAuthorizationRequest {
  final int parcelId;
  final int? authorizedUserId;
  final List<AuthorizedGuest>? authorizedGuest;

  CreateAuthorizationRequest({
    required this.parcelId,
    this.authorizedUserId,
    this.authorizedGuest,
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{'parcel_id': parcelId};
    if (authorizedUserId != null) {
      map['authorized_user_id'] = authorizedUserId;
    }
    if (authorizedGuest != null) {
      map['authorized_guest'] = authorizedGuest!.map((g) => g.toJson()).toList();
    }
    return map;
  }
}

class AuthorizedGuest {
  final String firstName;
  final String? lastName;
  final String phone;
  final String? address;
  final String? nationalNumber;
  final int? cityId;
  final String? birthday;

  AuthorizedGuest({
    required this.firstName,
    this.lastName,
    required this.phone,
    this.address,
    this.nationalNumber,
    this.cityId,
    this.birthday,
  });

  Map<String, dynamic> toJson() => {
    'first_name': firstName,
    if (lastName != null) 'last_name': lastName,
    'phone': phone,
    if (address != null) 'address': address,
    if (nationalNumber != null) 'national_number': nationalNumber,
    if (cityId != null) 'city_id': cityId,
    if (birthday != null) 'birthday': birthday,
  };
}
```

---

### 3.2 UpdateAuthorizationRequest - تحديث تخويل

**Endpoint:** `PUT /api/v1/authorization/{id}` (يتطلب مصادقة)

| الحقل                 | النوع    | القواعد                                       | مطلوب | الوصف                  |
| --------------------- | -------- | --------------------------------------------- | ----- | ---------------------- |
| `user_id`             | integer  | exists:users,id, different:authorized_user_id | ❌    | معرف المستخدم          |
| `parcel_id`           | integer  | exists:parcels,id                             | ❌    | معرف الطرد             |
| `authorized_user_id`  | integer  | exists:users,id, different:user_id            | ❌    | معرف المستخدم المُخوّل |
| `authorized_guest`    | array    | -                                             | ❌    | بيانات الضيف المُخوّل  |
| `used_at`             | datetime | nullable                                      | ❌    | تاريخ الاستخدام        |
| `cancellation_reason` | string   | nullable                                      | ❌    | سبب الإلغاء            |

---

## 4. التقييمات (Rate)

### 4.1 StoreRateRequest - إنشاء تقييم

**Endpoint:** `POST /api/v1/rates` (يتطلب مصادقة)

| الحقل           | النوع   | القواعد                   | مطلوب | الوصف                |
| --------------- | ------- | ------------------------- | ----- | -------------------- |
| `rateable_id`   | integer | -                         | ❌    | معرف العنصر المُقيّم |
| `rateable_type` | string  | required_with:rateable_id | ❌    | نوع العنصر المُقيّم  |
| `rating`        | integer | min:0, max:5              | ✅    | التقييم (0-5)        |
| `comment`       | string  | nullable, max:400         | ❌    | التعليق              |

**مثال الطلب:**

```json
{
    "rateable_id": 1,
    "rateable_type": "Parcel",
    "rating": 5,
    "comment": "خدمة ممتازة وسريعة"
}
```

**Flutter Model & Validation:**

```dart
class CreateRateRequest {
  final int? rateableId;
  final String? rateableType;
  final int rating;
  final String? comment;

  CreateRateRequest({
    this.rateableId,
    this.rateableType,
    required this.rating,
    this.comment,
  });

  Map<String, dynamic> toJson() => {
    if (rateableId != null) 'rateable_id': rateableId,
    if (rateableType != null) 'rateable_type': rateableType,
    'rating': rating,
    if (comment != null) 'comment': comment,
  };

  static String? validateRating(int? value) {
    if (value == null) return 'التقييم مطلوب';
    if (value < 0 || value > 5) return 'التقييم يجب أن يكون بين 0 و 5';
    return null;
  }

  static String? validateComment(String? value) {
    if (value != null && value.length > 400) {
      return 'التعليق طويل جداً (400 حرف كحد أقصى)';
    }
    return null;
  }
}
```

---

### 4.2 UpdateRateRequest - تحديث تقييم

**Endpoint:** `PUT /api/v1/rates/{id}` (يتطلب مصادقة)

| الحقل           | النوع   | القواعد                   | مطلوب | الوصف       |
| --------------- | ------- | ------------------------- | ----- | ----------- |
| `rateable_id`   | integer | -                         | ❌    | معرف العنصر |
| `rateable_type` | string  | required_with:rateable_id | ❌    | نوع العنصر  |
| `rating`        | integer | min:0, max:5              | ❌    | التقييم     |
| `comment`       | string  | nullable, max:400         | ❌    | التعليق     |

---

## 5. Telegram OTP

### 5.1 SendTelegramOtpRequest - إرسال OTP عبر تيليجرام

**Endpoint:** `POST /api/v1/telegram/otp/send`

| الحقل     | النوع   | القواعد | مطلوب | الوصف                     |
| --------- | ------- | ------- | ----- | ------------------------- |
| `chat_id` | integer | -       | ✅    | معرف المحادثة في تيليجرام |

**مثال الطلب:**

```json
{
    "chat_id": 123456789
}
```

---

### 5.2 VerifyTelegramOtpRequest - التحقق من OTP تيليجرام

**Endpoint:** `POST /api/v1/telegram/otp/verify`

| الحقل     | النوع   | القواعد  | مطلوب | الوصف         |
| --------- | ------- | -------- | ----- | ------------- |
| `chat_id` | integer | -        | ✅    | معرف المحادثة |
| `otp`     | string  | digits:6 | ✅    | رمز التحقق    |

**مثال الطلب:**

```json
{
    "chat_id": 123456789,
    "otp": "123456"
}
```

---

## 📊 ملخص سريع

### قواعد التحقق الشائعة

| القاعدة            | الوصف                   | مثال Flutter                        |
| ------------------ | ----------------------- | ----------------------------------- |
| `required`         | حقل مطلوب               | `if (value.isEmpty) return 'مطلوب'` |
| `email`            | بريد إلكتروني           | `RegExp(r'^[\w-\.]+@...')`          |
| `min:N`            | حد أدنى للطول           | `if (value.length < N)`             |
| `max:N`            | حد أقصى للطول           | `if (value.length > N)`             |
| `digits:N`         | عدد أرقام محدد          | `RegExp(r'^\d{N}$')`                |
| `exists:table,col` | موجود في الجدول         | تحقق من الـ API                     |
| `unique:table,col` | فريد                    | تحقق من الـ API                     |
| `regex:/pattern/`  | نمط معين                | `RegExp(r'pattern')`                |
| `sometimes`        | اختياري                 | `if (value != null)`                |
| `confirmed`        | تأكيد (مثل كلمة المرور) | مقارنة حقلين                        |

### الاستجابة عند فشل التحقق

```json
{
    "status": false,
    "message": "Validation failed",
    "errors": {
        "field_name": ["رسالة الخطأ"]
    }
}
```

### معالجة الأخطاء في Flutter

```dart
void handleValidationErrors(Map<String, dynamic> errors) {
  errors.forEach((field, messages) {
    if (messages is List) {
      for (var message in messages) {
        showError('$field: $message');
      }
    }
  });
}
```

---

## 6. 🔢 Enums (القيم الثابتة)

هذا القسم يوضح جميع الـ Enums المستخدمة في الـ API والقيم المتاحة لكل منها.

---

### 6.1 ParcelStatus - حالة الطرد

| القيمة             | الوصف بالعربية | الاستخدام                     |
| ------------------ | -------------- | ----------------------------- |
| `Pending`          | قيد الانتظار   | الطرد مسجل ولم يتم تأكيده بعد |
| `Confirmed`        | مؤكد           | تم تأكيد الطرد                |
| `In_transit`       | قيد النقل      | الطرد في الطريق               |
| `Out_For_Delivery` | خارج للتوصيل   | الطرد مع عامل التوصيل         |
| `Ready_For_Pickup` | جاهز للاستلام  | الطرد جاهز في الفرع           |
| `Delivered`        | تم التسليم     | تم تسليم الطرد بنجاح          |
| `Failed`           | فشل            | فشل التوصيل                   |
| `Returned`         | مُعاد          | تم إرجاع الطرد                |
| `Canceled`         | ملغى           | تم إلغاء الطرد                |

**Flutter Enum:**

```dart
enum ParcelStatus {
  pending('Pending', 'قيد الانتظار'),
  confirmed('Confirmed', 'مؤكد'),
  inTransit('In_transit', 'قيد النقل'),
  outForDelivery('Out_For_Delivery', 'خارج للتوصيل'),
  readyForPickup('Ready_For_Pickup', 'جاهز للاستلام'),
  delivered('Delivered', 'تم التسليم'),
  failed('Failed', 'فشل'),
  returned('Returned', 'مُعاد'),
  canceled('Canceled', 'ملغى');

  final String value;
  final String label;
  const ParcelStatus(this.value, this.label);

  static ParcelStatus fromString(String value) {
    return ParcelStatus.values.firstWhere((e) => e.value == value);
  }
}
```

---

### 6.2 AuthorizationStatus - حالة التخويل

| القيمة      | الوصف بالعربية | الاستخدام                    |
| ----------- | -------------- | ---------------------------- |
| `Pending`   | قيد الانتظار   | التخويل لم يُفعّل بعد        |
| `Active`    | نشط            | التخويل فعّال ويمكن استخدامه |
| `Expired`   | منتهي الصلاحية | انتهت صلاحية التخويل         |
| `Used`      | مُستخدم        | تم استخدام التخويل           |
| `Cancelled` | ملغى           | تم إلغاء التخويل             |

**Flutter Enum:**

```dart
enum AuthorizationStatus {
  pending('Pending', 'قيد الانتظار'),
  active('Active', 'نشط'),
  expired('Expired', 'منتهي الصلاحية'),
  used('Used', 'مُستخدم'),
  cancelled('Cancelled', 'ملغى');

  final String value;
  final String label;
  const AuthorizationStatus(this.value, this.label);
}
```

---

### 6.3 SenderType - نوع المرسل

| القيمة      | الوصف بالعربية |
| ----------- | -------------- |
| `User`      | مستخدم مسجل    |
| `GuestUser` | مستخدم ضيف     |

**Flutter Enum:**

```dart
enum SenderType {
  user('User', 'مستخدم مسجل'),
  guestUser('GuestUser', 'ضيف');

  final String value;
  final String label;
  const SenderType(this.value, this.label);
}
```

---

### 6.4 RatingForType - نوع التقييم (rateable_type)

استخدم هذه القيم في حقل `rateable_type` عند إنشاء تقييم:

| القيمة        | الوصف بالعربية      |
| ------------- | ------------------- |
| `Service`     | تقييم الخدمة        |
| `Branch`      | تقييم الفرع         |
| `Employee`    | تقييم الموظف        |
| `Parcel`      | تقييم الطرد         |
| `Delivery`    | تقييم التوصيل       |
| `Application` | تقييم التطبيق       |
| `ChatSession` | تقييم جلسة المحادثة |

**Flutter Enum:**

```dart
enum RatingForType {
  service('Service', 'الخدمة'),
  branch('Branch', 'الفرع'),
  employee('Employee', 'الموظف'),
  parcel('Parcel', 'الطرد'),
  delivery('Delivery', 'التوصيل'),
  application('Application', 'التطبيق'),
  chatSession('ChatSession', 'المحادثة');

  final String value;
  final String label;
  const RatingForType(this.value, this.label);
}
```

---

### 6.5 GuestType - نوع الضيف

| القيمة       | الوصف بالعربية |
| ------------ | -------------- |
| `Sender`     | مرسل ضيف       |
| `Authorized` | مخوّل ضيف      |

---

### 6.6 DaysOfWeek - أيام الأسبوع

تُستخدم في المسارات والمواعيد:

| القيمة      | الوصف    |
| ----------- | -------- |
| `Sunday`    | الأحد    |
| `Monday`    | الاثنين  |
| `Tuesday`   | الثلاثاء |
| `Wednesday` | الأربعاء |
| `Thursday`  | الخميس   |
| `Friday`    | الجمعة   |
| `Saturday`  | السبت    |

---

### 6.7 PolicyTypes - أنواع سياسات التسعير

| القيمة     | الوصف بالعربية      |
| ---------- | ------------------- |
| `Weight`   | حسب الوزن           |
| `Distance` | حسب المسافة         |
| `Volume`   | حسب الحجم (الأبعاد) |
| `Flate`    | سعر ثابت            |

---

### 6.8 CurrencyType - أنواع العملات

| القيمة   | العملة           | الرمز     |
| -------- | ---------------- | --------- |
| `Syria`  | الليرة السورية   | ل.س (SYP) |
| `USA`    | الدولار الأمريكي | $ (USD)   |
| `Europe` | اليورو           | € (EUR)   |
| `Russia` | الروبل الروسي    | ₽ (RUB)   |

---

### 6.9 UserAccountStatus - حالة حساب المستخدم

| القيمة   | الوصف بالعربية |
| -------- | -------------- |
| `Frozen` | مجمّد          |
| `Banned` | محظور          |

---

### 📱 مثال استخدام Enums في Flutter

```dart
// عند إنشاء تقييم
final rateRequest = CreateRateRequest(
  rateableId: parcel.id,
  rateableType: RatingForType.parcel.value, // "Parcel"
  rating: 5,
  comment: 'خدمة ممتازة',
);

// عند عرض حالة الطرد
Widget buildStatusBadge(String status) {
  final parcelStatus = ParcelStatus.fromString(status);
  return Chip(
    label: Text(parcelStatus.label),
    backgroundColor: _getStatusColor(parcelStatus),
  );
}

// ألوان حسب الحالة
Color _getStatusColor(ParcelStatus status) {
  switch (status) {
    case ParcelStatus.pending: return Colors.orange;
    case ParcelStatus.confirmed: return Colors.blue;
    case ParcelStatus.inTransit: return Colors.purple;
    case ParcelStatus.delivered: return Colors.green;
    case ParcelStatus.failed: return Colors.red;
    case ParcelStatus.canceled: return Colors.grey;
    default: return Colors.grey;
  }
}
```

---

## ✅ قائمة فحص التكامل

عند بناء تطبيق Flutter، تأكد من:

-   [ ] إضافة التحقق المحلي (Client-side validation)
-   [ ] معالجة أخطاء التحقق من الـ API (Server-side errors)
-   [ ] عرض رسائل الخطأ بشكل واضح للمستخدم
-   [ ] إضافة مؤشرات التحميل أثناء إرسال الطلبات
-   [ ] التعامل مع حالة عدم الاتصال بالإنترنت
