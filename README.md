# نظام إدارة الطرود والشحن - RMA Graduation Project

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel"/>
  <img src="https://img.shields.io/badge/Filament-3.3-FFFFFF?style=for-the-badge&logo=filament" alt="Filament"/>
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql" alt="MySQL"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"/>
</p>

---

## نظرة عامة

**نظام إدارة الطرود والشحن (RMA)** هو تطبيق ويب متكامل مبني على **Laravel 12** مع **Filament Admin Panel**، يوفر حلولاً شاملة لإدارة عمليات الشحن والطرود. يدعم النظام المستخدمين المسجلين والضيوف مع إمكانيات تتبع متقدمة ونظام إدارة شامل.

### المشكلة التي يحلها

قبل هذا النظام، كانت إدارة عمليات الشحن والطرود تتطلب:
- 📉 جهد يدوي كبير في تتبع حالة كل طرد
- ⚠️ صعوبة في التنسيق بين الفروع المتعددة
- 🕐 عدم وجود تواصل فوري بين العملاء والموظفين
- 📍 مشكلة في تتبع موقع الطرد الحالي

**الحل:** نظام مركزي مؤتمت يربط بين تطبيق العميل، لوحة تحكم الموظفين، ولوحة تحكم المدير مع دعم WebSocket للتواصل الفوري.

---

## المزايا الرئيسية

### 📦 إدارة الطرود الذكية

| الميزة | الوصف |
|--------|-------|
| **إنشاء الطرود** | دعم للمستخدمين المسجلين والضيوف |
| **تتبع الطرود** | نظام تتبع شامل مع أرقام تتبع فريدة |
| **حالات متعددة** | Pending → Confirmed → In Transit → Ready for Pickup → Delivered → Failed → Returned → Canceled |
| **تسعير ذكي** | نظام تسعير تلقائي بناءً على الوزن والسياسات |
| **تاريخ الطرود** | سجل كامل لتغييرات حالة كل طرد |

### 📊 دورة حياة الطرد (Parcel Lifecycle)

```
┌─────────────┐     ┌──────────────┐     ┌────────────┐     ┌─────────────────┐
│   Pending   │ ──► │  Confirmed   │ ──► │ In Transit │ ──► │ Ready for Pickup│
└─────────────┘     └──────────────┘     └────────────┘     └─────────────────┘
                                                                          │
                           ┌─────────────────────────────────────────────┘
                           ▼
                    ┌──────────────┐     ┌───────────┐     ┌───────────┐     ┌──────────┐
                    │   Failed     │ ◄── │ Delivered│ ◄────│ In Transit│ ◄───┤  Return │
                    └──────────────┘     └───────────┘     └───────────┘     └──────────┘
```

### 👥 إدا��ة المستخدمين والأدوار

| الدور | الوصف |
|--------|-------|
| **Guest (ضيف)** | يمكنه إنشاء طرود بدون تسجيل |
| **User (مستخدم)** | إنشاء وتتبع طروده الخاصة |
| **Employee (موظف)** | إدارة الطرود في الفرع المحدد |
| **Super Admin** | تحكم كامل في جميع الفروع والأنظمة |

### 🏢 إدارة الفروع والمسارات

- **إدارة الفروع**: إضافة وتعديل الفروع مع الموقع الجغرافي
- **مسارات الشحن**: تحديد مسارات بين الفروع (Branch Routes)
- **أيام العمل**: جدولة أيام عمل كل فرع
- **المواقع الجغرافية**: دعم الإحداثيات (Latitude/Longitude) واختيار الخريطة

### 📅 نظام المواعيد

| الميزة | الوصف |
|--------|-------|
| **حجز المواعيد** | نظام حجز مواعيد للاستلام والتسليم |
| **تقويم متاح** | عرض المواعيد المتاحة حسب المسار |
| **إدارة المواعيد** | إدارة شاملة من لوحة الإدارة |
| **التذكير التلقائي** | تذكير تلقائي للعملاء والموظفين |

### 🔐 نظام التخويل (Authorization)

```
┌─────────────────┐     ┌────────────────┐     ┌─────────────────┐
│  إنشاء تخويل   │ ──► │   إرسال رمز   │ ──► │   استلام الطرد │
│                 │     │   للمفوض      │     │   باستخدام رمز │
└─────────────────┘     └────────────────┘     └─────────────────┘
```

### 📱 نظام المحادثات والدعم (Chat System)

- **محادثات فورية**: دعم المحادثات بين العميل والموظفين
- **WebSocket**: تواصل لحظي عبر Laravel Reverb
- **نظام طابور الدع**: توزيع المحادثات على الموظفين
- **إشعارات فورية**: تنبيهات عند وصول رسائل جديدة

### 🚛 إدارة الشحن والتوصيل

- **إدارة الشاحنات**: تتبع الشاحنات والسائقين
- **تعيين الطرود**: ربط الطرود بالشحنات
- **تتبع التسليم**: مراقبة عملية التسليم والاستلام
- **تحديث المواقع**: تحديث موقع الشاحنة الحالي

### 📊 لوحة الإدارة (Filament Admin Panel)

#### نظرة عامة على Filament

**Filament** هو إطار عمل مفتوح المصدر لبناء لوحات الإدارة الاحترافية في Laravel. يوفر واجهة متجاوبة وعالية الأداء مع دعم كامل لـ Livewire.

#### الموارد (Resources) المتاحة في المشروع

| Resource | الوصف | الصفحات |
|----------|-------|----------|
| **ParcelResource** | إدارة الطرود | List, Create, Edit |
| **ConversationResource** | المحادثات | List, View, Edit |
| **NotificationResource** | الإشعارات | List, Create, Edit |
| **ParcelAuthorizationResource** | التخويلات | List, Create, Edit |
| **ParcelHistoryResource** | تاريخ الطرود | List, Create, Edit |
| **ParcelShipmentAssignmentResource** | تعيينات الشحن | List, Create, Edit |
| **ShipmentResource** | الشحنات | List, Create, Edit |
| **TruckResource** | الشاحنات | List, Create, Edit |
| **BranchResource** | الفروع | List, Create, Edit |
| **BranchRouteResource** | مسارات الشحن | List, Create, Edit |
| **BranchRouteDaysResource** | أيام العمل | List, Create, Edit |
| **CityResource** | المدن | List, Create, Edit |
| **CountryResource** | الدول | List, Create, Edit |
| **EmployeeResource** | الموظفين | List, Create, Edit |
| **UserResource** | المستخدمين | List, Create, Edit |
| **GuestUserResource** | المستخدمين الضيوف | List, Create, Edit |
| **UserRestrictionResource** | قيود المستخدمين | List, Create, Edit |
| **AppointmentResource** | المواعيد | Manage |
| **PricingPolicyResource** | سياسات التسعير | List, Create, Edit |
| **RateResource** | الأسعار | List |
| **UsagePoliciesResource** | سياسات الاستخدام | List, Create, Edit |
| **FrequentlyAskedQuestionsResource** | الأسئلة الشائعة | List, Create, Edit |

#### المكونات المخصصة (Custom Components)

##### 1. LocationSelect
```php
// اختيار المدينة والبلد مع التحديث الحي
LocationSelect::make('city_id', 'country_id', 'City', 'Country')
```
- اختيار الدولة أولاً
- جلب المدن المتاحة عند اختيار الدولة
- تحديث حي (Live)

##### 2. PhoneNumber
```php
// إدخال رقم الهاتف المحسن
PhoneNumber::make('phone', 'Phone')
```
- دعم لجميع الدول
- التحقق التلقائي

##### 3. NationalNumber
```php
// رقم الوطني
NationalNumber::make('national_number', 'National Number')
```
- التحقق من صحة الرقم

##### 4. ActiveToggle
```php
// مفتاح التفعيل
ActiveToggle::make('is_active', 'Active')
```
- تفعيل/تعطيل للسجلات

##### 5. City Component
```php
// اختيار المدينة
City::make('city_id', 'City')
```
- قائمة المدن مع البحث

#### نظام Wizard (الخطوات المتعددة)

يدعم النظام إدخال البيانات على خطوات متعددة:

```
Step 1: Choose Sender Type    ←─── نوع المرسل (مستخدم/ضيف)
Step 2: Sender Details     ←─── بيانات المرسل
Step 3: Parcel Details   ←─── تفاصيل الطرد
Step 4: Receiver Details ←─── بيانات المستقبل
Step 5: Pricing         ←─── التسعير
```

#### أدوات الصفحة (Widgets)

| Widget | الوصف |
|-------|-------|
| **StatsOverview** | إحصائيات عامة (عدد الطرود، المستخدمين...) |
| **ParcelStatusChart** | رسم بياني لحالات الطرود |
| **ParcelChart** | رسم بياني للطرود |
| **RatesChart** | رسم بياني للأسعار |

#### ميزات Filament المستخدمة

| الميزة | الوصف |
|--------|-------|
| **Livewire** | التفاعل بدون JavaScript |
| **Tailwind CSS** | تصميم متجاوب |
| **Dark Mode** | الوضع الداكن |
| **RTL Support** | دعم العربية |
| **Tables** | جداول مع ACTIONS |
| **Forms** | نماذج ذكية |
| **Relation Managers** | إدارة العلاقات |
| **Notifications** | إشعارات داخل التطبيق |

#### صفحات المصادقة (Auth)

```
/login      ←─── تسجيل الدخول
/register   ←─── تسجيل جديد
/forgot-password ←─── نسيت كلمة المرور
/validate-account ←─── تفعيل الحساب
```

#### هيكل Filament في المشروع

```
app/Filament/
├── Forms/
│   └── Components/           ←─── مكونات مخصصة
│       ├── LocationSelect.php
│       ├── PhoneNumber.php
│       ├── NationalNumber.php
│       ├── ActiveToggle.php
│       └── City.php
│
├── Helpers/
│   └── TableActions.php       ←─── إجراءات الجدول
│
├── Pages/
│   ├── Auth/                ←─── المصادقة
│   │   ├── Login.php
│   │   ├── Register.php
│   │   ├── ForgetPassword.php
│   │   └── ValidateAccount.php
│   └── Dashboard.php         ←─── لوحة التحكم
│
├── Resources/              ←─── الموارد
│   ├── ParcelResource.php
│   ├── ConversationResource.php
│   └── ... (20+ resource)
│
└── Widgets/                ←─── الأدوات
    ├── StatsOverview.php
    ├── ParcelStatusChart.php
    ├── ParcelChart.php
    └── RatesChart.php
```

#### مثال: نموذج إنشاء طرد (Parcel Form)

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Wizard::make([
                Step::make('Sender Type')
                    ->schema([
                        Select::make('sender_type')
                            ->options(SenderType::class)
                            ->reactive()
                    ]),
                
                Step::make('Sender Details')
                    ->schema([
                        TextInput::make('guest_first_name'),
                        PhoneNumber::make('guest_phone'),
                        LocationSelect::make('city_id', 'country_id'),
                    ]),
                
                Step::make('Parcel Details')
                    ->schema([
                        TextInput::make('description'),
                        TextInput::make('weight'),
                        Select::make('branch_route_id'),
                    ]),
            ])
        ]);
}
```

#### الوصول إلى لوحة التحكم

- **الرابط**: `/admin`
- **المستخدمون**: Super Admin والموظفين فقط

### 🌐 دعم متعدد اللغات

- **العربية والإنجليزية**: دعم كامل للغتين
- **واجهة محلية**: تجربة مستخدم مخصصة

---

## البنية التقنية (Tech Stack)

### التقنيات الأساسية

| التقنية | الإصدار | الاستخدام |
|---------|---------|-----------|
| **Laravel** | 12.x | إطار عمل PHP |
| **PHP** | 8.2+ | لغة البرمجة |
| **MySQL** | 8.0+ | قاعدة البيانات |
| **Filament** | 3.3 | لوحة الإدارة |
| **Tailwind CSS** | 4.0 | التصميم |

### الحزم الأساسية

| الحزمة | الاستخدام |
|--------|----------|
| **Laravel Passport** | نظام المصادقة API |
| **Laravel Reverb** | WebSockets |
| **Spatie Permission** | إدارة الأدوار والصلاحيات |
| **dotswan/filament-map-picker** | اختيار الخريطة |
| **ysfkaya/filament-phone-input** | إدخال الهواتف |
| **Firebase/ Telegram** | الإشعارات |

---

## هيكل المشروع (Project Structure)

```
app/
├── Enums/                          # الثوابت والتعدادات
│   ├── ParcelStatus.php           # حالات الطرد
│   ├── AppointmentStatus.php     # حالات الموعد
│   ├── RoleName.php              # أسماء الأدوار
│   ├── AuthorizationStatus.php   # حالات التخويل
│   └── ...
│
├── Events/                         # أحداث WebSocket
│   ├── ConversationUpdatedEvent.php
│   └── NewMessageEvent.php
│
├── Filament/                      # لوحة الإدارة
│   ├── Forms/                    # النماذج
│   │   ├── PhoneNumber.php
│   │   ├── LocationSelect.php
│   │   └── City.php
│   ├── Helpers/                  # المساعدين
│   ├── Pages/                   # الصفحات
│   │   ├── Auth/                # تسجيل الدخول
│   │   └── Dashboard.php
│   └── Resources/              # الموارد
│       ├── ParcelResource.php
│       ├── ConversationResource.php
│       ├── EmployeeResource.php
│       └── ...
│
├── Http/
│   ├── Controllers/
│   │   └── API/               # المتحكمات API
│   │       ├── V1/
│   │       │   ├── Admin/
│   │       │   ├── Appointment/
│   │       │   ├── Auth/
│   │       │   ├── Authorization/
│   │       │   ├── Branche/
│   │       │   ├── Chat/
│   │       │   └── ...
│   ├── Middleware/
│   └── Requests/
│
├── Models/                       # نماذج قاعدة البيانات
│   ├── User.php               # المستخدم
│   ├── Employee.php           # الموظف
│   ├── Parcel.php            # الطرد
│   ├── ParcelHistory.php     # تاريخ الطرد
│   ├── ParcelAuthorization.php
│   ├── Shipment.php         # الشحنة
│   ├── Truck.php            # الشاحنة
│   ├── Appointment.php    # الموعد
│   ├── Branch.php         # الفرع
│   ├── BranchRoute.php   # مسار الشحن
│   ├── Conversation.php # المحادثة
│   ├── Message.php       # الرسالة
│   └── ...
│
├── Notifications/            # الإشعارات
├── Observers/               # المراقبون
├── Policies/               # السياسات
├── Services/              # الخدمات
└── Traits/               # السمات
```

---

## قاعدة البيانات (Database Schema)

### الجداول الرئيسية

#### 1. جداول المستخدمين
| الجدول | الوصف |
|--------|-------|
| `users` | حسابات المستخدمين المسجلين |
| `employees` | بيانات الموظفين |
| `guest_users` | المستخدمون الضيوف |
| `user_restrictions` | قيود الحسابات |

#### 2. جداول الطرود
| الجدول | الوصف |
|--------|-------|
| `parcels` | الطرود |
| `parcel_histories` | تاريخ تغييرات الطرود |
| `parcel_authorizations` | تخويلات الاستلام |
| `parcel_shipment_assignments` | تعيينات الشحن |

#### 3. جداول الشحن
| الجدول | الوصف |
|--------|-------|
| `shipments` | الشحنات |
| `trucks` | الشاحنات |

#### 4. جداول المواعيد
| الجدول | الوصف |
|--------|-------|
| `appointments` | المواعيد |

#### 5. جداول الفروع
| الجدول | ا��وصف |
|--------|-------|
| `countries` | الدول |
| `cities` | المدن |
| `branches` | الفروع |
| `branch_routes` | مسارات الشحن |
| `branch_route_days` | أيام العمل |

#### 6. جدولات الدعم
| الجدول | الوصف |
|--------|-------|
| `conversations` | المحادثات |
| `messages` | الرسائل |
| `notifications` | الإشعارات |
| `pricing_policies` | سياسات التسعير |

---

## نظام API (REST API)

### الإصدارات

- **الإصدار الحالي**: `/api/v1/`

### المصادقة (Authentication)

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/register` | POST | تسجيل مستخدم جديد |
| `/api/v1/login` | POST | تسجيل الدخول |
| `/api/v1/logout` | GET | تسجيل الخروج |
| `/api/v1/me` | GET | بيانات المستخدم الحالي |
| `/api/v1/forgot-password` | POST | استعادة كلمة المرور |
| `/api/v1/verify-email` | POST | التحقق من البريد |
| `/api/v1/confirm-email-otp` | POST | تأكيد رمز التحقق |

### إدارة الطرود

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/parcel` | GET | قائمة الطرود |
| `/api/v1/parcel` | POST | إنشاء طرد جديد |
| `/api/v1/parcel/{id}` | GET | تفاصيل طرد |
| `/api/v1/parcel/{id}` | PUT | تحديث طرد |
| `/api/v1/parcel/{id}` | DELETE | حذف طرد |
| `/api/v1/parcel/returned` | GET | الطرود المرفودة |
| `/api/v1/parcel/{tracking_number}/location` | GET | موقع الطرد |

### نظام التخويل

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/authorization` | GET | قائمة التخويلات |
| `/api/v1/authorization` | POST | إنشاء تخويل |
| `/api/v1/authorization/{id}` | PUT | تحديث تخويل |
| `/api/v1/authorization/{id}` | DELETE | حذف تخويل |
| `/api/v1/authorization/use/{id}` | POST | استخدام التخويل |

### المواعيد

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/get-getCalender/{tracking_number}` | GET | المواعيد المتاحة |
| `/api/v1/book-appointment` | POST | حجز موعد |
| `/api/v1/cancel-appointment` | POST | إلغاء موعد |
| `/api/v1/update-appointment` | POST | تحديث موعد |

### المحادثات (Chat)

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/chat/conversations` | GET | قائمة المحادثات |
| `/api/v1/chat/conversations` | POST | إنشاء محادثة |
| `/api/v1/chat/conversations/{id}` | GET | تفاصيل محادثة |
| `/api/v1/chat/conversations/{id}/close` | POST | إغلاق محادثة |
| `/api/v1/chat/conversations/{id}/messages` | GET | رسائل المحادثة |
| `/api/v1/chat/conversations/{id}/messages` | POST | إرسال رسالة |

### الفروع والمواقع

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/countries` | GET | قائمة الدول |
| `/api/v1/countries/{id}/cities` | GET | مدن الدولة |
| `/api/v1/branches` | GET | قائمة الفروع |
| `/api/v1/branches/{cityId}` | GET | فروع المدينة |
| `/api/v1/routes` | GET | مسارات الشحن |
| `/api/v1/routes/{day}` | GET | مسارات يوم معين |

### الإشعارات

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/notifications` | GET | قائمة الإشعارات |
| `/api/v1/notifications/unread-count` | GET | عدد غير المقروءة |
| `/api/v1/notifications/{id}/read` | POST | تحديد كمقروء |
| `/api/v1/notifications/read-all` | POST | تحديد الكل كمقروء |

### نقاط管理员 (Admin)

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/admin/my-branch` | GET | الفرع الحالي |
| `/api/v1/admin/parcels` | GET | جميع الطرود |
| `/api/v1/admin/parcels/{id}/history` | GET | تاريخ طرد |
| `/api/v1/admin/parcels/{id}/confirm-reception` | POST | تأكيد الاستلام |
| `/api/v1/admin/parcels/{id}/update-status` | POST | تحديث الحالة |
| `/api/v1/admin/appointments` | GET | المواعيد |
| `/api/v1/admin/shipments` | GET | الشحنات |
| `/api/v1/admin/trucks` | GET | الشاحنات |

### نقاط المدير العام (Super Admin)

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `/api/v1/super-admin/stats` | GET | الإحصائيات |
| `/api/v1/super-admin/branches` | GET | الفروع |
| `/api/v1/super-admin/branches` | POST | إنشاء فرع |
| `/api/v1/super-admin/employees` | GET | الموظفين |
| `/api/v1/super-admin/assign-employee` | POST | تعيين موظف |
| `/api/v1/super-admin/all-parcels` | GET | جميع الطرود |

---

## نظام WebSocket (Laravel Reverb)

### القنوات المتاحة

| القناة | الوصف |
|--------|-------|
| `user.{id}` | إشعارات مستخدم محدد |
| `notifications` | الإشعارات العامة |
| `conversation.{id}` | محادثة محددة |
| `employee.{id}` | تحديثات موظف |
| `support.queue` | طابور الدعم |

### Beispiel: الاشتراك بقناة

```json
{
  "event": "pusher:subscribe",
  "data": {
    "channel": "notifications"
  }
}
```

### Beispiel: استلام حدث

```json
{
  "event": "conversation.created",
  "data": {
    "id": 1,
    "customer_id": 5,
    "subject": "استشارة",
    "status": "open",
    "action": "created"
  }
}
```

---

## الأمان (Security)

### نظام الصلاحيات

| الميزة | الوصف |
|--------|-------|
| **Laravel Passport** | مصادقة OAuth2 |
| **Spatie Permission** | إدارة الأدوار |
| **Middleware** | حماية المسارات |
| **CSRF Protection** | حماية من الهجمات |
| **Rate Limiting** | تحديد المعدل |

### الأدوار والصلاحيات

```
Super Admin
    │
    ├── Employee (Branch)
    │   ├── عرض الطرود
    │   ├── تحديث الحالة
    │   └── إدارة المواعيد
    │
    └── User/Guest
        ├── إنشاء الطرد
        ├── تتبع الطرد
        ├── حجز موعد
        └── المحادثة
```

---

## التشغيل (Installation)

### المتطلبات

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 8.0+
- Git

### خطوات التثبيت

```bash
# 1. استنساخ المشروع
git clone [repository-url]
cd rma-gradioation-project

# 2. تثبيت التبعيات
composer install
npm install

# 3. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 4. إعداد قاعدة البيانات
# تعديل ملف .env بالإعدادات الصحيحة

# 5. تشغيل الهجرات
php artisan migrate

# 6. تشغيل البذور
php artisan db:seed

# 7. إنشاء روابط التخزين
php artisan storage:link

# 8. تشغيل الخادم
php artisan serve --host=0.0.0.0 --port=8000
```

### تشغيل WebSocket

```bash
php artisan reverb:start --host=0.0.0.0 --port=6001 --debug
```

### تشغيل جميع الخدمات

```bash
# ملف التشغيل التلقائي
run-project.bat
```

---

## الإعدادات (Configuration)

### متغيرات البيئة

```env
# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=r_m_a
DB_USERNAME=root
DB_PASSWORD=

# Laravel
APP_NAME=RMA
APP_URL=http://localhost:8000

# المصادقة
BROADCAST_CONNECTION=reverb

# Reverb
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=0.0.0.0
REVERB_PORT=6001

# Telegram (اختياري)
TELEGRAM_BOT_TOKEN=your_bot_token
```

---

## الاختبارات

```bash
# تشغيل جميع الاختبارات
php artisan test

# اختبارات الوحدة
php artisan test --testsuite=Unit

# اختبارات الميزات
php artisan test --testsuite=Feature
```

---

## المساهمة

نرحب بالمساهمات! يرجى:

1. Fork المشروع
2. إنشاء فرع جديد
3.Commit التغييرات
4. Push إلى الفرع
5. فتح Pull Request

---

## الترخيص

هذا المشروع مرخص تحت رخصة MIT.

---

<div align="center">

**تم التطوير بعناية لضمان أفضل تجربة مستخدم وأعلى معايير الأمان والأداء.**

</div>