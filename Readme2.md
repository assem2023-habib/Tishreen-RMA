🚀 نظام الإشعارات المتكامل - RMA Graduation Project
🗒️ نظرة عامة
نظام إشعارات متكامل ومتقدم يدعم الإشعارات الفورية (Real-time) مع تكامل كامل مع Flutter، صُمم خصيصًا لمشروع التخرج RMA.
✨ الميزات الرئيسية
🔔 إشعارات فورية (Real-time) عبر WebSocket
📱 دعم تطبيق Flutter بواجهة حديثة
🎨 أشكال وألوان وأيقونات متنوعة لكل نوع وأولوية إشعار
👥 إشعارات فردية وجماعية
📊 إحصائيات متقدمة للإشعارات
🔒 نظام مصادقة متقدم (Laravel Passport)
⚡ أداء عالي عبر Queue System
🌐 دعم كامل للاتصالات المباشرة (WebSocket)
🏗️ البنية التقنية
Backend (Laravel 11)
Framework: Laravel 11
Database: MySQL
Queue System: Database Queue
Broadcasting: Laravel Reverb
Authentication: Laravel Passport
API: RESTful API
Frontend (Flutter)
Framework: Flutter 3.x
State Management: Provider
HTTP Client: http + dio
WebSocket: web_socket_channel
Local Storage: shared_preferences
📁 هيكل المشروع (مختصر)
🚀 التثبيت والإعداد
1. متطلبات النظام
PHP 8.2+
Composer
MySQL 8.0+
Node.js 18+
Flutter 3.x
2. تثبيت Backend
3. إعداد متغيرات البيئة
تأكد من ضبط إعدادات قاعدة البيانات وReverb وQueue في ملف .env.
4. تشغيل الخدمات
5. تثبيت Flutter
📱 استخدام الـAPI
Authentication
إنشاء إشعار
الحصول على الإشعارات
تعليم كمقروء
نقاط إضافية:
جميع الـEndpoints موثقة في ملف NOTIFICATIONS_API.postman_collection.json
يوجد دليل مفصل في NOTIFICATIONS_README.md
🧩 أنواع وأولويات الإشعارات
النوع	اللون	الأيقونة	الوصف
info	أزرق	معلومات	معلومات عامة
success	أخضر	نجاح	نجاح العملية
warning	أصفر	تحذير	تحذيرات
danger	أحمر	خطر	أخطاء
reminder	أزرق	تذكير	تذكيرات
update	أخضر	تحديث	تحديثات
announcement	أصفر	إعلان	إعلانات
الأولوية	اللون	الوصف
Important	أحمر	إشعارات مهمة
Reminder	أصفر	تذكيرات
Loyalty	أخضر	إشعارات الولاء
🧪 اختبار النظام
اختبار الأوامر:
اختبار Postman: استخدم ملف NOTIFICATIONS_API.postman_collection.json
اختبار Flutter:
🛠️ استكشاف الأخطاء
تأكد من تشغيل Reverb وQueue Worker
راجع إعدادات البيئة وملفات الـlogs
📄 التراخيص
هذا المشروع مرخص تحت رخصة MIT.
