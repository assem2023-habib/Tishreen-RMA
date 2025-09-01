# 🚀 نظام الإشعارات المتكامل - RMA Graduation Project

## 📋 نظرة عامة

تم تطوير نظام إشعارات متكامل ومتقدم يدعم الإشعارات المباشرة (Real-time) مع Flutter، مصمم خصيصاً لمشروع التخرج RMA.

### ✨ الميزات الرئيسية

- 🔔 **إشعارات فورية**: دعم كامل للإشعارات المباشرة
- 📱 **دعم Flutter**: تطبيق Flutter متكامل مع واجهة جميلة
- 🎨 **أشكال مختلفة**: ألوان وأيقونات مختلفة لكل نوع وأولوية
- 👥 **إشعارات متعددة**: دعم الإشعارات الفردية والجماعية
- 📊 **إحصائيات متقدمة**: تتبع شامل للإشعارات
- 🔐 **أمان عالي**: نظام مصادقة متقدم
- ⚡ **أداء عالي**: نظام Queue متطور
- 🌐 **WebSocket**: دعم كامل للاتصالات المباشرة

## 🏗️ البنية التقنية

### Backend (Laravel 11)
- **Framework**: Laravel 11
- **Database**: MySQL
- **Queue System**: Database Queue
- **Broadcasting**: Laravel Reverb
- **Authentication**: Laravel Passport
- **API**: RESTful API

### Frontend (Flutter)
- **Framework**: Flutter 3.x
- **State Management**: Provider
- **HTTP Client**: http + dio
- **WebSocket**: web_socket_channel
- **Local Storage**: shared_preferences

## 📁 هيكل المشروع

```
rma-gradioation-project/
├── app/
│   ├── Console/Commands/TestNotifications.php
│   ├── Events/NotificationSent.php
│   ├── Http/Controllers/
│   │   ├── Api/V1/Notification/NotificationController.php
│   │   └── BroadcastController.php
│   ├── Jobs/ProcessNotificationJob.php
│   ├── Listeners/SendNotificationListener.php
│   ├── Models/
│   │   ├── Notification.php
│   │   └── User.php
│   ├── Providers/
│   │   ├── BroadcastServiceProvider.php
│   │   └── EventServiceProvider.php
│   └── Services/NotificationService.php
├── config/
│   ├── broadcasting.php
│   └── queue.php
├── database/migrations/
├── routes/
│   ├── api.php
│   └── channels.php
├── flutter_notification_example.dart
├── flutter_pubspec.yaml
├── NOTIFICATIONS_API.postman_collection.json
└── NOTIFICATIONS_README.md
```

## 🚀 التثبيت والإعداد

### 1. متطلبات النظام
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+
- Flutter 3.x

### 2. تثبيت Backend

```bash
# Clone المشروع
git clone <repository-url>
cd rma-gradioation-project

# تثبيت Dependencies
composer install

# نسخ ملف البيئة
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate

# إنشاء جداول Queue
php artisan queue:table
php artisan queue:failed-table
php artisan migrate

# تثبيت Laravel Reverb
composer require laravel/reverb
php artisan reverb:install
```

### 3. إعداد متغيرات البيئة

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rma_project
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=your-reverb-app-key
REVERB_APP_SECRET=your-reverb-app-secret
REVERB_APP_ID=your-reverb-app-id
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Queue
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

### 4. تشغيل الخدمات

```bash
# تشغيل Queue Worker
php artisan queue:work --queue=notifications

# تشغيل Reverb Server
php artisan reverb:start

# تشغيل التطبيق
php artisan serve
```

### 5. تثبيت Flutter

```bash
# إنشاء مشروع Flutter جديد
flutter create rma_notifications_app
cd rma_notifications_app

# نسخ pubspec.yaml
cp ../flutter_pubspec.yaml pubspec.yaml

# تثبيت Dependencies
flutter pub get

# تشغيل التطبيق
flutter run
```

## 📱 استخدام API

### Authentication
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

### إنشاء إشعار
```http
POST /api/v1/notifications
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "إشعار جديد",
    "message": "لديك رسالة جديدة",
    "notification_type": "info",
    "notification_priority": "Important",
    "user_ids": [1, 2, 3]
}
```

### الحصول على الإشعارات
```http
GET /api/v1/notifications?limit=20&unread_only=false
Authorization: Bearer {token}
```

### تعليم كمقروء
```http
POST /api/v1/notifications/{id}/read
Authorization: Bearer {token}
```

## 🔌 Flutter Integration

### 1. إعداد NotificationService

```dart
final notificationService = NotificationService();
notificationService.setAuthToken('your_token', userId);

// الحصول على الإشعارات
final notifications = await notificationService.getNotifications();

// إنشاء إشعار جديد
await notificationService.createNotification(
  title: 'عنوان الإشعار',
  message: 'محتوى الإشعار',
  type: 'info',
  priority: 'Important',
  userIds: [1, 2, 3],
);
```

### 2. WebSocket Connection

```dart
notificationService.connectToWebSocket(
  onNotificationReceived: (notification) {
    // معالجة الإشعار الجديد
    print('New notification: ${notification['title']}');
  },
  onError: (error) {
    print('Error: $error');
  },
  onConnected: () {
    print('Connected to WebSocket');
  },
);
```

## 🎯 أنواع الإشعارات

### أنواع الإشعارات
| النوع | اللون | الأيقونة | الوصف |
|-------|-------|----------|-------|
| `info` | أزرق | معلومات | معلومات عامة |
| `success` | أخضر | نجاح | نجاح العملية |
| `warning` | أصفر | تحذير | تحذيرات |
| `danger` | أحمر | خطر | أخطاء |
| `reminder` | أزرق | تذكير | تذكيرات |
| `update` | أخضر | تحديث | تحديثات |
| `announcement` | أصفر | إعلان | إعلانات |

### أولويات الإشعارات
| الأولوية | اللون | الوصف |
|----------|-------|-------|
| `Important` | أحمر | إشعارات مهمة |
| `Reminder` | أصفر | تذكيرات |
| `Loyalty` | أخضر | إشعارات الولاء |

## 🧪 اختبار النظام

### 1. اختبار Command

```bash
# اختبار النظام
php artisan notifications:test

# اختبار مع مستخدم محدد
php artisan notifications:test --user-id=1

# اختبار مع نوع وأولوية محددين
php artisan notifications:test --type=warning --priority=Important
```

### 2. اختبار Postman

استخدم ملف `NOTIFICATIONS_API.postman_collection.json` لاختبار جميع API endpoints.

### 3. اختبار Flutter

```bash
# تشغيل التطبيق
flutter run

# اختبار WebSocket
# افتح التطبيق وانتظر الإشعارات المباشرة
```

## 📊 إحصائيات النظام

### معلومات الإشعارات
```json
{
  "stats": {
    "total": 150,
    "read": 120,
    "unread": 30
  }
}
```

### معلومات المستخدم
```json
{
  "user": {
    "id": 1,
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "unread_count": 5
  }
}
```

## 🔍 استكشاف الأخطاء

### مشاكل شائعة

1. **WebSocket لا يعمل**
   - تأكد من تشغيل Reverb server
   - تحقق من إعدادات Broadcasting
   - تأكد من صحة token

2. **Queue لا يعمل**
   - تأكد من تشغيل queue worker
   - تحقق من إعدادات قاعدة البيانات
   - راجع logs

3. **الإشعارات لا تصل**
   - تحقق من إعدادات القنوات
   - تأكد من صحة user_ids
   - راجع NotificationService

### Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Queue logs
php artisan queue:failed

# Broadcasting logs
# راجع console output للـ Reverb server
```

## 🚀 النشر والإنتاج

### 1. إعدادات الإنتاج

```env
APP_ENV=production
APP_DEBUG=false
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
```

### 2. تشغيل الخدمات

```bash
# تشغيل Queue Worker كخدمة
php artisan queue:work --daemon

# تشغيل Reverb server
php artisan reverb:start --host=0.0.0.0 --port=8080

# استخدام Supervisor للـ Queue
# استخدام PM2 للـ Reverb
```

### 3. Flutter Build

```bash
# Android
flutter build apk --release

# iOS
flutter build ios --release

# Web
flutter build web --release
```

## 📚 الوثائق الإضافية

- [NOTIFICATIONS_README.md](NOTIFICATIONS_README.md) - دليل مفصل للإشعارات
- [flutter_notification_example.dart](flutter_notification_example.dart) - مثال Flutter كامل
- [NOTIFICATIONS_API.postman_collection.json](NOTIFICATIONS_API.postman_collection.json) - Postman Collection

## 🤝 المساهمة

1. Fork المشروع
2. إنشاء branch جديد (`git checkout -b feature/AmazingFeature`)
3. Commit التغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push إلى branch (`git push origin feature/AmazingFeature`)
5. فتح Pull Request

## 📄 الترخيص

هذا المشروع مرخص تحت رخصة MIT - راجع ملف [LICENSE](LICENSE) للتفاصيل.

## 📞 الدعم

إذا واجهت أي مشاكل أو لديك أسئلة:

- 📧 Email: support@rma-project.com
- 💬 Discord: [RMA Project Discord](https://discord.gg/rma-project)
- 📱 Telegram: [@rma_project_support](https://t.me/rma_project_support)

## 🙏 شكر وتقدير

- فريق RMA Graduation Project
- Laravel Community
- Flutter Community
- جميع المساهمين في المشروع

---

**تم التطوير بواسطة فريق RMA Graduation Project** 🎓

**آخر تحديث**: ديسمبر 2024
