# دليل المطور - نظام إدارة الطرود والشحن

## 📚 فهرس المحتويات

1. [نظرة عامة على التطوير](#نظرة-عامة-على-التطوير)
2. [إعداد بيئة التطوير](#إعداد-بيئة-التطوير)
3. [هيكل المشروع](#هيكل-المشروع)
4. [معايير التطوير](#معايير-التطوير)
5. [إضافة ميزات جديدة](#إضافة-ميزات-جديدة)
6. [اختبار الكود](#اختبار-الكود)
7. [إدارة قاعدة البيانات](#إدارة-قاعدة-البيانات)
8. [إدارة الملفات](#إدارة-الملفات)
9. [إدارة التخزين المؤقت](#إدارة-التخزين-المؤقت)
10. [إدارة الطوابير](#إدارة-الطوابير)
11. [إدارة الأحداث](#إدارة-الأحداث)
12. [إدارة الإشعارات](#إدارة-الإشعارات)
13. [إدارة الترجمة](#إدارة-الترجمة)
14. [إدارة الأمان](#إدارة-الأمان)
15. [تحسين الأداء](#تحسين-الأداء)
16. [استكشاف الأخطاء](#استكشاف-الأخطاء)
17. [النشر والإنتاج](#النشر-والإنتاج)

---

## نظرة عامة على التطوير

### التقنيات المستخدمة
- **Laravel 12**: إطار عمل PHP حديث
- **PHP 8.2+**: إصدار PHP المطلوب
- **MySQL 8.0+**: قاعدة البيانات
- **Filament 3.3**: لوحة الإدارة
- **Laravel Passport**: نظام المصادقة
- **Spatie Laravel Permission**: إدارة الصلاحيات

### متطلبات التطوير
- PHP 8.2 أو أحدث
- Composer 2.0 أو أحدث
- Node.js 18+ و NPM
- MySQL 8.0 أو أحدث
- Git

---

## إعداد بيئة التطوير

### 1. استنساخ المشروع
```bash
git clone [repository-url]
cd rma-gradioation-project
```

### 2. تثبيت التبعيات
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

### 3. إعداد البيئة
```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate

# تشغيل البذور
php artisan db:seed

# إنشاء مفتاح Passport
php artisan passport:install
```

### 4. بناء الأصول
```bash
# للتطوير
npm run dev

# للإنتاج
npm run build
```

### 5. تشغيل الخدمات (جديد 🚀)
لقد قمت بتوفير سكربتات لتشغيل جميع الخدمات الضرورية بضغطة واحدة بدلاً من فتح عدة نوافذ Terminal:

**لنظام Windows:**
1. اضغط مرتين على ملف `run-project.bat` الموجود في المجلد الرئيسي للمشروع.
2. سيقوم السكربت بفتح نوافذ منفصلة لكل من:
   - خادم التطوير (Laravel Serve)
   - خادم الإشعارات (Laravel Reverb)
   - مشغل الطوابير (Queue Worker)
   - المجدول (Laravel Scheduler)
   - خادم الأصول (Vite)

**أو باستخدام PowerShell:**
```powershell
.\run-project.ps1
```

---

## هيكل المشروع

```
app/
├── Console/                 # أوامر Artisan
├── Enums/                  # التعدادات
│   ├── AuthorizationStatus.php
│   ├── ParcelStatus.php
│   ├── SenderType.php
│   └── ...
├── Events/                 # الأحداث
├── Exceptions/             # استثناءات مخصصة
├── Filament/              # لوحة الإدارة
│   ├── Forms/             # نماذج Filament
│   ├── Resources/         # موارد Filament
│   ├── Tables/            # جداول Filament
│   └── Widgets/           # أدوات Filament
├── Http/
│   ├── Controllers/       # المتحكمات
│   │   └── Api/
│   │       └── V1/        # API v1
│   ├── Middleware/        # الوسطاء
│   └── Requests/          # طلبات التحقق
├── Models/                # النماذج
├── Notifications/         # الإشعارات
├── Observers/             # المراقبين
├── Policies/              # السياسات
├── Providers/             # مزودي الخدمة
├── Services/              # الخدمات
└── Trait/                 # السمات
```

---

## معايير التطوير

### 1. PSR Standards
- **PSR-1**: Basic Coding Standard
- **PSR-4**: Autoloader
- **PSR-12**: Extended Coding Style

### 2. Laravel Conventions
```php
// النماذج
class User extends Model
{
    protected $fillable = ['name', 'email'];
    protected $hidden = ['password'];
    protected $casts = ['email_verified_at' => 'datetime'];
}

// المتحكمات
class UserController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}

// الخدمات
class UserService
{
    public function createUser(array $data): User
    {
        return User::create($data);
    }
}
```

### 3. Naming Conventions
```php
// Classes: PascalCase
class ParcelController {}

// Methods: camelCase
public function createParcel() {}

// Variables: camelCase
$parcelData = [];

// Constants: UPPER_SNAKE_CASE
const MAX_WEIGHT = 50.0;

// Database: snake_case
$table->string('tracking_number');
```

### 4. Code Documentation
```php
/**
 * Create a new parcel for the authenticated user.
 *
 * @param array $data The parcel data
 * @return Parcel
 * @throws ValidationException
 */
public function createParcel(array $data): Parcel
{
    // Implementation
}
```

---

## إضافة ميزات جديدة

### 1. إنشاء نموذج جديد
```bash
php artisan make:model Notification -m
```

### 2. إنشاء متحكم API
```bash
php artisan make:controller Api/V1/NotificationController --api
```

### 3. إنشاء خدمة
```bash
# إنشاء ملف الخدمة يدوياً
touch app/Services/NotificationService.php
```

### 4. إنشاء طلب تحقق
```bash
php artisan make:request Api/V1/Notification/StoreNotificationRequest
```

### 5. إنشاء مراقب
```bash
php artisan make:observer NotificationObserver --model=Notification
```

### 6. إنشاء سياسة
```bash
php artisan make:policy NotificationPolicy --model=Notification
```

### 7. إنشاء إشعار
```bash
php artisan make:notification SendNotification
```

---

## اختبار الكود

### 1. إعداد الاختبارات
```bash
# إنشاء اختبار Feature
php artisan make:test ParcelTest

# إنشاء اختبار Unit
php artisan make:test ParcelServiceTest --unit
```

### 2. تشغيل الاختبارات
```bash
# جميع الاختبارات
php artisan test

# اختبارات محددة
php artisan test --filter=ParceTest

# مع التغطية
php artisan test --coverage
```

### 3. أمثلة الاختبارات
```php
// Feature Test
class ParcelTest extends TestCase
{
    public function test_can_create_parcel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->postJson('/api/v1/parcel', [
            'route_id' => 1,
            'reciver_name' => 'Test User',
            'reciver_address' => 'Test Address',
            'reciver_phone' => '+963912345678',
            'weight' => 2.5,
            'is_paid' => false
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('parcels', [
            'reciver_name' => 'Test User'
        ]);
    }
}

// Unit Test
class ParcelServiceTest extends TestCase
{
    public function test_can_calculate_cost()
    {
        $service = new ParcelService();
        $cost = $service->calculateCost(2.5, 500);
        
        $this->assertEquals(1250, $cost);
    }
}
```

---

## إدارة قاعدة البيانات

### 1. الهجرات
```bash
# إنشاء هجرة
php artisan make:migration create_notifications_table

# تشغيل الهجرات
php artisan migrate

# إعادة تعيين الهجرات
php artisan migrate:reset

# إعادة تشغيل الهجرات
php artisan migrate:refresh
```

### 2. البذور
```bash
# إنشاء بذرة
php artisan make:seeder NotificationSeeder

# تشغيل البذور
php artisan db:seed

# تشغيل بذرة محددة
php artisan db:seed --class=NotificationSeeder
```

### 3. Factories
```bash
# إنشاء Factory
php artisan make:factory NotificationFactory
```

```php
// Factory Example
class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'notification_type' => 'info',
            'notification_priority' => 'normal',
        ];
    }
}
```

---

## إدارة الملفات

### 1. رفع الملفات
```php
// في المتحكم
if ($request->hasFile('image')) {
    $path = $request->file('image')->store('images', 'public');
    $user->image_profile = $path;
    $user->save();
}
```

### 2. حذف الملفات
```php
// حذف ملف قديم
if ($user->image_profile) {
    Storage::disk('public')->delete($user->image_profile);
}
```

### 3. إعدادات التخزين
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## إدارة التخزين المؤقت

### 1. Cache Tags
```php
// تخزين مؤقت مع tags
Cache::tags(['users', 'profile'])->put('user.1', $user, 3600);

// مسح cache محدد
Cache::tags(['users'])->flush();
```

### 2. Model Caching
```php
// في النموذج
class User extends Model
{
    protected $fillable = ['name', 'email'];
    
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            Cache::forget('users.all');
        });
    }
}
```

### 3. Query Caching
```php
// تخزين مؤقت للاستعلامات
$users = Cache::remember('users.all', 3600, function () {
    return User::all();
});
```

---

## إدارة الطوابير

### 1. إنشاء Job
```bash
php artisan make:job SendEmailNotification
```

### 2. إرسال Job
```php
// إرسال فوري
SendEmailNotification::dispatch($user, $message);

// إرسال مؤجل
SendEmailNotification::dispatch($user, $message)
    ->delay(now()->addMinutes(5));
```

### 3. تشغيل Worker
```bash
# تشغيل worker
php artisan queue:work

# مع إعادة المحاولة
php artisan queue:work --tries=3
```

---

## إدارة الأحداث

### 1. إنشاء Event
```bash
php artisan make:event ParcelStatusChanged
```

### 2. إنشاء Listener
```bash
php artisan make:listener SendParcelNotification --event=ParcelStatusChanged
```

### 3. تسجيل الأحداث
```php
// في EventServiceProvider
protected $listen = [
    ParcelStatusChanged::class => [
        SendParcelNotification::class,
    ],
];
```

---

## إدارة الإشعارات

### 1. إنشاء Notification
```bash
php artisan make:notification ParcelDelivered
```

### 2. إرسال الإشعارات
```php
// إرسال لمستخدم واحد
$user->notify(new ParcelDelivered($parcel));

// إرسال لجميع المستخدمين
User::all()->each(function ($user) {
    $user->notify(new ParcelDelivered($parcel));
});
```

### 3. قنوات الإشعارات
```php
public function via($notifiable)
{
    return ['mail', 'database', 'telegram'];
}
```

---

## إدارة الترجمة

### 1. إضافة ترجمة جديدة
```php
// في lang/ar/parcel.php
return [
    'new_message' => 'رسالة جديدة',
];

// في lang/en/parcel.php
return [
    'new_message' => 'New message',
];
```

### 2. استخدام الترجمة
```php
// في الكود
__('parcel.new_message')

// مع متغيرات
__('parcel.welcome_message', ['name' => $user->name])
```

### 3. تنسيق الترجمة
```php
// في lang/ar/parcel.php
return [
    'welcome_message' => 'مرحباً :name، أهلاً بك في نظامنا!',
];
```

---

## إدارة الأمان

### 1. التحقق من الصلاحيات
```php
// في المتحكم
public function index()
{
    $this->authorize('viewAny', Parcel::class);
    // Logic
}

// في Blade
@can('create', App\Models\Parcel)
    <a href="{{ route('parcels.create') }}">Create Parcel</a>
@endcan
```

### 2. حماية API
```php
// في RouteServiceProvider
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    // Protected routes
});
```

### 3. تشفير البيانات
```php
// تشفير البيانات الحساسة
$encrypted = Crypt::encryptString('sensitive data');
$decrypted = Crypt::decryptString($encrypted);
```

---

## تحسين الأداء

### 1. Eager Loading
```php
// تجنب N+1 queries
$parcels = Parcel::with(['sender', 'route'])->get();
```

### 2. Database Indexing
```php
// في الهجرة
$table->index(['tracking_number']);
$table->index(['parcel_status']);
$table->index(['created_at']);
```

### 3. Query Optimization
```php
// استخدام select محدد
$users = User::select('id', 'name', 'email')->get();

// استخدام where محدد
$parcels = Parcel::where('status', 'pending')
    ->where('created_at', '>', now()->subDays(7))
    ->get();
```

---

## استكشاف الأخطاء

### 1. Logging
```php
// تسجيل الأخطاء
Log::error('Error message', ['context' => $data]);

// تسجيل المعلومات
Log::info('User action', ['user_id' => $user->id]);
```

### 2. Debug Mode
```php
// في .env
APP_DEBUG=true
LOG_LEVEL=debug
```

### 3. Error Handling
```php
try {
    // Risky code
} catch (Exception $e) {
    Log::error($e->getMessage());
    return response()->json(['error' => 'Something went wrong'], 500);
}
```

---

## النشر والإنتاج

### 1. إعداد الإنتاج
```bash
# تحسين التطبيق
php artisan config:cache
php artisan route:cache
php artisan view:cache

# بناء الأصول
npm run build
```

### 2. متطلبات الخادم
- PHP 8.2+
- MySQL 8.0+
- Nginx/Apache
- SSL Certificate
- Domain Name

### 3. متغيرات البيئة
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password

TELEGRAM_BOT_TOKEN=your-bot-token
```

---

## نصائح التطوير

### 1. Best Practices
- استخدم Type Hints
- اكتب اختبارات شاملة
- اتبع معايير PSR
- استخدم Services للمنطق المعقد
- استخدم Observers للأحداث

### 2. Code Review
- راجع الكود قبل الدمج
- تأكد من الأمان
- تحقق من الأداء
- تأكد من التوثيق

### 3. Version Control
- استخدم Git branches
- اكتب commit messages واضحة
- استخدم pull requests
- احتفظ بـ changelog

---

## موارد إضافية

### 1. Laravel Documentation
- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Filament Docs](https://filamentphp.com/docs)
- [Passport Docs](https://laravel.com/docs/12.x/passport)

### 2. PHP Resources
- [PHP Manual](https://www.php.net/manual/)
- [PSR Standards](https://www.php-fig.org/psr/)

### 3. Database Resources
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Laravel Eloquent](https://laravel.com/docs/12.x/eloquent)

---

**هذا الدليل يوفر إرشادات شاملة لتطوير وصيانة النظام.**
