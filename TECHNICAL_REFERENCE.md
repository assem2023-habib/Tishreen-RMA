# المرجع التقني الشامل - نظام إدارة الطرود والشحن

## 📋 فهرس المحتويات

1. [نظرة عامة على النظام](#نظرة-عامة-على-النظام)
2. [هيكل قاعدة البيانات](#هيكل-قاعدة-البيانات)
3. [النماذج والعلاقات](#النماذج-والعلاقات)
4. [المتحكمات و API Endpoints](#المتحكمات-و-api-endpoints)
5. [الخدمات والمنطق التجاري](#الخدمات-والمنطق-التجاري)
6. [نظام المصادقة والأمان](#نظام-المصادقة-والأمان)
7. [المراقبين والأحداث](#المراقبين-والأحداث)
8. [نظام الإشعارات](#نظام-الإشعارات)
9. [لوحة الإدارة Filament](#لوحة-الإدارة-filament)
10. [قواعد التحقق والطلبات](#قواعد-التحقق-والطلبات)
11. [السياسات والأذونات](#السياسات-والأذونات)
12. [الوسطاء والحماية](#الوسطاء-والحماية)
13. [الترجمة واللغات](#الترجمة-واللغات)
14. [التكوين والإعدادات](#التكوين-والإعدادات)
15. [الاختبار والصيانة](#الاختبار-والصيانة)

---

## نظرة عامة على النظام

### التقنيات الأساسية
- **Laravel 12**: إطار عمل PHP حديث
- **PHP 8.2+**: إصدار PHP المطلوب
- **MySQL 8.0+**: قاعدة البيانات الرئيسية
- **Filament 3.3**: لوحة الإدارة
- **Laravel Passport**: نظام المصادقة API
- **Spatie Laravel Permission**: إدارة الأدوار والصلاحيات

### المكونات الرئيسية
- **API RESTful**: واجهة برمجية شاملة
- **Admin Panel**: لوحة إدارة متقدمة
- **Real-time Notifications**: إشعارات فورية
- **Multi-language Support**: دعم متعدد اللغات
- **Telegram Integration**: تكامل مع Telegram

---

## هيكل قاعدة البيانات

### الجداول الرئيسية

#### 1. جدول المستخدمين (users)
```sql
- id (Primary Key)
- first_name (VARCHAR)
- last_name (VARCHAR)
- email (VARCHAR, UNIQUE)
- user_name (VARCHAR, UNIQUE, NULLABLE)
- password (VARCHAR)
- phone (VARCHAR, UNIQUE)
- address (VARCHAR, NULLABLE)
- national_number (VARCHAR, UNIQUE)
- birthday (DATE, NULLABLE)
- email_verified_at (TIMESTAMP, NULLABLE)
- city_id (Foreign Key)
- image_profile (VARCHAR, NULLABLE)
- created_at, updated_at (TIMESTAMPS)
- remember_token (VARCHAR)
```

#### 2. جدول الطرود (parcels)
```sql
- id (Primary Key)
- sender_id (Foreign Key)
- sender_type (ENUM: User, GuestUser)
- route_id (Foreign Key to branch_routes)
- reciver_name (VARCHAR)
- reciver_address (VARCHAR)
- reciver_phone (VARCHAR)
- weight (DECIMAL 5,3)
- cost (DECIMAL 10,3)
- is_paid (TINYINT, Default: 0)
- parcel_status (ENUM: Pending, Confirmed, In_transit, etc.)
- tracking_number (VARCHAR, UNIQUE)
- appointment_id (Foreign Key, NULLABLE)
- created_at, updated_at (TIMESTAMPS)
```

#### 3. جدول الفروع (branches)
```sql
- id (Primary Key)
- branch_name (VARCHAR)
- city_id (Foreign Key)
- address (VARCHAR, NULLABLE)
- phone (VARCHAR, NULLABLE)
- email (VARCHAR, NULLABLE)
- latitude (DECIMAL 11,3, NULLABLE)
- longitude (DECIMAL 11,3, NULLABLE)
- status (TINYINT, Default: 1)
- created_at, updated_at (TIMESTAMPS)
```

#### 4. جدول مسارات الفروع (branch_routes)
```sql
- id (Primary Key)
- from_branch_id (Foreign Key)
- to_branch_id (Foreign Key)
- estimated_departure_time (TIME)
- estimated_arrival_time (TIME)
- created_at, updated_at (TIMESTAMPS)
```

#### 5. جدول المواعيد (appointments)
```sql
- id (Primary Key)
- user_id (Foreign Key, NULLABLE)
- branch_id (Foreign Key)
- date (DATE)
- time (TIME)
- booked (BOOLEAN, Default: false)
- created_at, updated_at (TIMESTAMPS)
```

#### 6. جدول التخويلات (parcel_authorizations)
```sql
- id (Primary Key)
- user_id (Foreign Key)
- parcel_id (Foreign Key)
- authorized_user_id (Foreign Key)
- authorized_user_type (ENUM: User, GuestUser)
- authorized_code (VARCHAR, UNIQUE)
- authorized_status (VARCHAR)
- generated_at (TIMESTAMP)
- expired_at (TIMESTAMP)
- used_at (TIMESTAMP, NULLABLE)
- cancellation_reason (VARCHAR, NULLABLE)
```

#### 7. جدول تاريخ الطرود (parcel_histories)
```sql
- id (Primary Key)
- parcel_id (Foreign Key, NULLABLE)
- user_id (Foreign Key)
- old_data (JSON, NULLABLE)
- new_data (JSON, NULLABLE)
- changes (JSON, NULLABLE)
- operation_type (ENUM: CREATED, UPDATED, DELETED)
- created_at, updated_at (TIMESTAMPS)
```

### الجداول المساعدة

#### جداول OAuth (Laravel Passport)
- `oauth_clients`
- `oauth_access_tokens`
- `oauth_refresh_tokens`
- `oauth_auth_codes`

#### جداول الصلاحيات (Spatie Permission)
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

#### جداول OTP
- `password_otps`
- `email_otps`
- `telegram_otps`

---

## النماذج والعلاقات

### 1. نموذج User
```php
// العلاقات الرئيسية
public function city() // belongsTo City
public function parcels() // morphMany Parcel
public function parcelsAuthorizations() // morphMany ParcelAuthorization
public function employee() // hasOne Employee
public function rates() // morphMany Rate
public function notifications() // belongsToMany Notification
public function appointments() // hasMany Appointment

// السمات المحسوبة
public function getNameAttribute() // user_name
public function getFullNameAttribute() // first_name + last_name
```

### 2. نموذج Parcel
```php
// العلاقات الرئيسية
public function route() // belongsTo BranchRoute
public function appointment() // belongsTo Appointment
public function sender() // morphTo (User/GuestUser)
public function parcelsHistories() // hasMany ParcelHistory
public function parcelAuthorization() // hasMany ParcelAuthorization
public function rates() // morphMany Rate

// السمات المحسوبة
public function getSenderNameAttribute() // اسم المرسل
public function getRouteLabelAttribute() // تسمية المسار
```

### 3. نموذج Branch
```php
// العلاقات الرئيسية
public function city() // belongsTo City
public function country() // through city
public function parcel() // hasMany Parcel
public function employees() // hasMany Employee
public function rates() // morphMany Rate
```

### 4. نموذج ParcelAuthorization
```php
// العلاقات الرئيسية
public function sender() // morphTo
public function authorizedUser() // morphTo
public function user() // belongsTo User
public function parcel() // belongsTo Parcel
```

---

## المتحكمات و API Endpoints

### 1. AuthController
```php
// Endpoints العامة
POST /api/v1/register - تسجيل مستخدم جديد
POST /api/v1/login - تسجيل الدخول
POST /api/v1/forgot-password - طلب إعادة تعيين كلمة المرور
POST /api/v1/new-password - تعيين كلمة مرور جديدة
POST /api/v1/verify-email - طلب تحقق البريد الإلكتروني
POST /api/v1/confirm-email-otp - تأكيد رمز التحقق

// Endpoints محمية
GET /api/v1/logout - تسجيل الخروج
GET /api/v1/me - بيانات المستخدم الحالي
POST /api/v1/reset-password - إعادة تعيين كلمة المرور
```

### 2. ParcelController
```php
// جميع العمليات محمية
GET /api/v1/parcel - قائمة الطرود
POST /api/v1/parcel - إنشاء طرد جديد
GET /api/v1/parcel/{id} - تفاصيل طرد
PUT /api/v1/parcel/{id} - تحديث طرد
DELETE /api/v1/parcel/{id} - حذف طرد
```

### 3. AuthorizationController
```php
// جميع العمليات محمية
GET /api/v1/authorization - قائمة التخويلات
POST /api/v1/authorization - إنشاء تخويل
GET /api/v1/authorization/{id} - تفاصيل تخويل
PUT /api/v1/authorization/{id} - تحديث تخويل
DELETE /api/v1/authorization/{id} - حذف تخويل
POST /api/v1/authorization/use/{id} - استخدام تخويل
```

### 4. AppointmentController
```php
// Endpoints عامة
GET /api/v1/get-getCalender/{tracking_number} - المواعيد المتاحة
POST /api/v1/book-appointment - حجز موعد
```

### 5. TelegramOtpController
```php
// Endpoints عامة
POST /api/v1/telegram/otp/send - إرسال OTP عبر Telegram
POST /api/v1/telegram/otp/verify - التحقق من OTP
POST /api/v1/telegram/webhook - Webhook للبوت
```

---

## الخدمات والمنطق التجاري

### 1. ParcelService
```php
// الوظائف الرئيسية
public function showParcels($userId) // عرض طرود المستخدم
public function createParcel($data) // إنشاء طرد جديد
public function updateParcel($parcelId, $parcel) // تحديث طرد
public function showParcel($parcelId) // عرض طرد محدد
public function deleteParcel($userId, $parcelId) // حذف طرد
```

### 2. AuthService
```php
// الوظائف الرئيسية
public function login(array $credentials) // تسجيل الدخول
public function checkIfEmailVerifited($email) // فحص تحقق البريد
public function createOtp(string $table, $email) // إنشاء OTP
public function verifiyOtp($table, $email, $otp_code) // التحقق من OTP
```

### 3. AuthorizationService
```php
// الوظائف الرئيسية
public function getAllAuthorization($userId) // جميع التخويلات
public function createAuthorization($data) // إنشاء تخويل
public function showAuthorization(string $id) // عرض تخويل
public function updateAuthorization($authorizationId, array $data) // تحديث تخويل
public function deleteAuthorization(string $id) // حذف تخويل
public function useAuthorization(string $authorizationId) // استخدام تخويل
```

### 4. TelegramOtpService
```php
// الوظائف الرئيسية
public function generateOtp($chatId) // توليد OTP
public function sendOtp($chatId) // إرسال OTP
public function verifyOtp($chatId, $otp) // التحقق من OTP
```

### 5. GeocodingService
```php
// الوظائف الرئيسية
public static function reverseGeocode(float $lat, float $lng) // تحويل الإحداثيات
```

---

## نظام المصادقة والأمان

### 1. Laravel Passport
- **OAuth2 Server**: خادم OAuth2 كامل
- **API Tokens**: رموز API آمنة
- **Client Credentials**: مصادقة العميل
- **Personal Access Tokens**: رموز الوصول الشخصية

### 2. نظام الأدوار والصلاحيات
```php
// الأدوار المتاحة
enum RoleName: string {
    case SUPER_ADMIN = "SuperAdmin";
    case EMPLOYEE = "Employee";
}

// الصلاحيات المتاحة
enum PermissionName: string {
    case CAN_ACCESS_PANEL = "can_access_panel";
}
```

### 3. حماية البيانات
- **Password Hashing**: تشفير كلمات المرور
- **CSRF Protection**: حماية من هجمات CSRF
- **Rate Limiting**: تحديد معدل الطلبات
- **Input Validation**: تحقق شامل من المدخلات

---

## المراقبين والأحداث

### 1. ParcelLifecycleObserver
```php
// الأحداث المعالجة
public function creating(Parcel $parcel) // قبل الإنشاء
public function created(Parcel $parcel) // بعد الإنشاء
public function updating(Parcel $parcel) // قبل التحديث
public function deleting(Parcel $parcel) // قبل الحذف

// الوظائف المساعدة
private function generateUniqueTrackingNumber() // توليد رقم تتبع فريد
private function calculateCost($weight, $pricePolicy) // حساب التكلفة
private function resolvePriceByWeight($weight) // تحديد السعر حسب الوزن
```

### 2. ParcelAuthorizationObserver
```php
// الأحداث المعالجة
public function creating(ParcelAuthorization $parcelAuthorization) // قبل الإنشاء
public function updated(ParcelAuthorization $parcelAuthorization) // بعد التحديث
public function deleted(ParcelAuthorization $parcelAuthorization) // بعد الحذف

// الوظائف المساعدة
// توليد رمز تخويل فريد
// تحديد تاريخ انتهاء الصلاحية
```

### 3. EmployeeObserver
```php
// الأحداث المعالجة
public function creating(Employee $employee) // قبل الإنشاء
public function created(Employee $employee) // بعد الإنشاء
public function deleting(Employee $employee) // قبل الحذف

// الوظائف المساعدة
// تعيين دور الموظف
// إزالة دور الموظف
```

---

## نظام الإشعارات

### 1. SendEmailVerificationOtpNotification
```php
// قنوات الإرسال
public function via(object $notifiable): array // ['mail']

// محتوى البريد الإلكتروني
public function toMail(object $notifiable): MailMessage
```

### 2. SendPasswordOtpNotification
```php
// قنوات الإرسال
public function via(object $notifiable): array // ['mail']

// محتوى البريد الإلكتروني
public function toMail(object $notifiable): MailMessage
```

### 3. SendNotification
```php
// إشعارات عامة
// دعم قنوات متعددة
// تخصيص المحتوى
```

---

## لوحة الإدارة Filament

### 1. الموارد الرئيسية
- **ParcelResource**: إدارة الطرود
- **UserResource**: إدارة المستخدمين
- **BranchResource**: إدارة الفروع
- **EmployeeResource**: إدارة الموظفين
- **AppointmentResource**: إدارة المواعيد
- **AuthorizationResource**: إدارة التخويلات

### 2. النماذج المخصصة
- **LocationSelect**: اختيار الموقع الجغرافي
- **PhoneNumber**: إدخال أرقام الهواتف
- **NationalNumber**: إدخال الرقم الوطني
- **ActiveToggle**: تبديل الحالة

### 3. الجداول المخصصة
- **Timestamps**: أعمدة الطوابع الزمنية
- **ActiveToggleColumn**: عمود تبديل الحالة
- **Custom Actions**: إجراءات مخصصة

---

## قواعد التحقق والطلبات

### 1. StoreParcelRequest
```php
// قواعد التحقق
'route_id' => 'required|numeric|exists:branch_routes,id'
'reciver_name' => 'required|string|max:250|min:2'
'reciver_address' => 'required|string|max:500'
'reciver_phone' => 'required|string|min:6|max:20|regex:/^\+?\d+$/'
'weight' => 'required|numeric|min:0.1'
'is_paid' => 'required|boolean'
```

### 2. RegisterRequest
```php
// قواعد التحقق
'first_name' => 'required|string|max:255'
'last_name' => 'required|string|max:255'
'email' => 'required|email|unique:users,email'
'password' => 'required|string|min:8|confirmed'
'phone' => 'required|unique:users'
'birthday' => 'required'
'city_id' => 'required|exists:cities,id'
'national_number' => 'required|digits:11|unique:users,national_number'
```

### 3. StoreAuthorizationRequest
```php
// قواعد التحقق المعقدة
// دعم المستخدمين المسجلين والضيوف
// التحقق من عدم وجود تخويل مسبق
```

---

## السياسات والأذونات

### 1. BranchPolicy
```php
// الصلاحيات
public function viewAny(User $user): bool // true
public function view(User $user, Branch $branch): bool // true
public function create(User $user): bool // SUPER_ADMIN only
public function update(User $user, Branch $branch): bool // SUPER_ADMIN only
public function delete(User $user, Branch $branch): bool // SUPER_ADMIN only
```

### 2. EmployeePolicy
```php
// الصلاحيات
public function viewAny(User $user): bool // SUPER_ADMIN only
public function view(User $user, Employee $employee): bool // true
public function create(User $user): bool // true
public function update(User $user, Employee $employee): bool // true
public function delete(User $user, Employee $employee): bool // true
```

---

## الوسطاء والحماية

### 1. ApiAuthenticate
```php
// معالجة استثناءات المصادقة
// إرجاع استجابة JSON مناسبة
// دعم Guards متعددة
```

### 2. Rate Limiting
```php
// تحديد معدل الطلبات: 6 طلبات في الدقيقة
// تطبيق على جميع API endpoints
// حماية من هجمات DDoS
```

---

## الترجمة واللغات

### 1. الملفات المدعومة
- **العربية**: `lang/ar/`
- **الإنجليزية**: `lang/en/`

### 2. الملفات المترجمة
- `auth.php` - رسائل المصادقة
- `parcel.php` - رسائل الطرود
- `authorization.php` - رسائل التخويل
- `notifications.php` - رسائل الإشعارات
- `validation.php` - رسائل التحقق

### 3. استخدام الترجمة
```php
// في الكود
__('auth.login_success')
__('parcel.parcel_created')
__('authorization.authorization_used')
```

---

## التكوين والإعدادات

### 1. إعدادات التطبيق
```php
// config/app.php
'name' => env('APP_NAME', 'Laravel')
'env' => env('APP_ENV', 'production')
'debug' => (bool) env('APP_DEBUG', false)
'url' => env('APP_URL', 'http://localhost')
'timezone' => 'UTC'
'locale' => 'en'
```

### 2. إعدادات قاعدة البيانات
```php
// config/database.php
'default' => env('DB_CONNECTION', 'mysql')
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
]
```

### 3. إعدادات الخدمات الخارجية
```php
// config/services.php
'telegram' => [
    'botToken' => env('TELEGRAM_BOT_TOKEN'),
]
```

---

## الاختبار والصيانة

### 1. الاختبارات
```bash
# تشغيل جميع الاختبارات
php artisan test

# اختبارات الوحدة
php artisan test --testsuite=Unit

# اختبارات الميزات
php artisan test --testsuite=Feature
```

### 2. الصيانة
```bash
# تنظيف التخزين المؤقت
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# إعادة بناء التخزين المؤقت
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. المراقبة
- **Logs**: سجلات مفصلة في `storage/logs/`
- **Performance**: مراقبة الأداء
- **Errors**: تتبع الأخطاء
- **Security**: مراقبة الأمان

---

## التطوير والتوسع

### 1. إضافة ميزات جديدة
- إنشاء نماذج جديدة
- إضافة متحكمات API
- تطوير خدمات جديدة
- إنشاء موارد Filament

### 2. التكامل مع خدمات خارجية
- **Payment Gateways**: بوابات الدفع
- **SMS Services**: خدمات الرسائل النصية
- **Email Services**: خدمات البريد الإلكتروني
- **Maps APIs**: خرائط وخدمات الموقع

### 3. تحسين الأداء
- **Database Optimization**: تحسين قاعدة البيانات
- **Caching**: التخزين المؤقت
- **Queue Jobs**: مهام الطابور
- **API Optimization**: تحسين API

---

## الأمان والحماية

### 1. حماية البيانات
- **Encryption**: تشفير البيانات الحساسة
- **Hashing**: تشفير كلمات المرور
- **Sanitization**: تنظيف المدخلات
- **Validation**: التحقق من البيانات

### 2. حماية API
- **Rate Limiting**: تحديد معدل الطلبات
- **CORS**: إعدادات CORS
- **Authentication**: المصادقة
- **Authorization**: التفويض

### 3. مراقبة الأمان
- **Logging**: تسجيل الأحداث
- **Monitoring**: مراقبة النظام
- **Alerts**: تنبيهات الأمان
- **Audit Trail**: مسار التدقيق

---

## الدعم والمساعدة

### 1. الوثائق
- **API Documentation**: وثائق API
- **User Guide**: دليل المستخدم
- **Developer Guide**: دليل المطور
- **Troubleshooting**: استكشاف الأخطاء

### 2. المجتمع
- **GitHub Issues**: تقارير المشاكل
- **Discussions**: مناقشات المجتمع
- **Contributions**: المساهمات
- **Feedback**: التعليقات

---

**هذا المرجع التقني يغطي جميع جوانب النظام بالتفصيل ويوفر مرجعاً شاملاً للمطورين والمستخدمين.**
