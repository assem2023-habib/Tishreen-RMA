# دليل النظام الفوري (Real-time System Guide) - Frontend

هذا المستند يشرح بالتفصيل كيفية الربط مع نظام الإشعارات الفورية ونظام المحادثات المباشرة (Live Chat) في تطبيق Flutter.

---

## 1. الإعداد الأولي (Connection Setup)

نستخدم **Laravel Reverb** كخادم WebSocket. يجب إعداد مكتبة `pusher_client` (أو أي مكتبة Laravel Echo متوافقة) في Flutter بالإعدادات التالية:

### إعدادات الاتصال (Client Configuration)

```dart
// إعدادات Reverb (متوافقة مع Pusher)
const String appId = '125362'; // REVERB_APP_ID
const String key = 'z8gmvgvmclvhoezjsfil';  // REVERB_APP_KEY
const String host = '10.43.226.236'; // REVERB_HOST
const int port = 6001; // REVERB_PORT
const String scheme = 'http'; // REVERB_SCHEME

// خيارات Pusher
PusherOptions options = PusherOptions(
  host: host,
  port: port,
  encrypted: false, // false because scheme is http
  cluster: 'mt1',
);
```

> **ملاحظة:** تأكد من إضافة الـ Authorizer الصحيح للقنوات الخاصة (Private Channels) مع إرسال الـ Bearer Token الخاص بالمستخدم.

---

## 2. نظام الإشعارات (Notifications System)

هناك نوعان من قنوات الإشعارات: عامة وخاصة.

### أ. القناة العامة (Public Notifications)

تستخدم للإعلانات العامة التي تصل لجميع المستخدمين.

-   **اسم القناة:** `notifications`
-   **نوع القناة:** Public

### ب. القناة الخاصة بالمستخدم (Private User Channel)

تستخدم للإشعارات الخاصة بالمستخدم.

-   **اسم القناة:** `private-user.{user_id}`
-   **نوع القناة:** Private (يتطلب Authentication)
-   **اسم الحدث (Event):** `.notification.sent` (أو `notification.sent`)
-   **هيكل البيانات (Payload):**

```json
{
    "id": "uuid-string (اختياري)",
    "title": "عنوان الإشعار",
    "message": "نص الإشعار",
    "notification_type": "info", // values: info, success, warning, danger
    "data": {
        "parcel_id": 123,
        "tracking_number": "RMA-123456"
    },
    "created_at": "timestamp"
}
```

### ج. نقاط الاتصال (API Endpoints) الخاصة بالإشعارات

| الوظيفة            | الطريقة | الرابط                               |
| ------------------ | ------- | ------------------------------------ |
| جلب كل الإشعارات   | `GET`   | `/api/v1/notifications`              |
| عدد غير المقروءة   | `GET`   | `/api/v1/notifications/unread-count` |
| تحديد الكل كمقروء  | `POST`  | `/api/v1/notifications/read-all`     |
| تحديد إشعار كمقروء | `POST`  | `/api/v1/notifications/{id}/read`    |

---

## 3. نظام المحادثات المباشرة (Live Chat System)

نظام محادثة 1-to-1 بين العميل والموظف.

### أ. القنوات والأحداث (Channels & Events)

#### القناة: `private-conversation.{conversation_id}`

يجب الاشتراك في هذه القناة بمجرد دخول العميل لصفحة المحادثة.

**الأحداث المستمع إليها:**

1. **حدث رسالة جديدة (`.message.new`)**

    - ينطلق عندما يرسل الطرف الآخر رسالة.
    - **Payload:**
        ```json
        {
            "id": 150,
            "uuid": "uuid-v4",
            "conversation_id": 10,
            "sender_type": "App\\Models\\Employee", // Employee or User
            "sender_id": 5,
            "sender_name": "اسم الموظف",
            "content": "مرحباً، كيف يمكنني مساعدتك؟",
            "type": "text", // text, file, image
            "attachment_url": "https://...",
            "attachment_name": "filename.pdf",
            "is_from_customer": false,
            "created_at": "2024-01-01T12:00:00.000000Z"
        }
        ```

2. **حدث تحديث المحادثة (`.conversation.updated`)**
    - ينطلق عند تغير حالة المحادثة.
    - **Payload:**
        ```json
        {
            "id": 10,
            "status": "open", // pending, open, closed
            "action": "assigned", // assigned, closed, created
            "employee_id": 5,
            "updated_at": "..."
        }
        ```

### ب. سيناريو الربط (Integration Workflow)

1. **بدء المحادثة:**

    - العميل يرسل `POST /api/v1/chat/conversations` لإنشاء محادثة.
    - **Body:**
        ```json
        {
            "subject": "استفسار عن طرد",
            "related_type": "parcel", // اختياري: parcel, branch, appointment
            "related_id": 123
        }
        ```
    - الباك اند يعيد كائن المحادثة مع الـ `id`.

2. **الاشتراك:**

    - العميل يشترك في قناة `private-conversation.{id}`.

3. **إرسال رسالة:**

    - العميل يرسل `POST /api/v1/chat/conversations/{id}/messages`.
    - **Body:** `content` و/أو `attachment` (ملف).
    - الرسالة تظهر فوراً لدى العميل (Optimistic UI).

4. **استقبال رسالة:**
    - الاستماع لحدث `.message.new` وعرض الرسالة في القائمة.

### ج. نقاط الاتصال (API Endpoints) الخاصة بالمحادثات

| الوظيفة         | الطريقة | الرابط                                     | البيانات المطلوبة                                                              |
| --------------- | ------- | ------------------------------------------ | ------------------------------------------------------------------------------ |
| قائمة المحادثات | `GET`   | `/api/v1/chat/conversations`               | -                                                                              |
| إنشاء محادثة    | `POST`  | `/api/v1/chat/conversations`               | `subject` (مطلوب)<br>`related_type` (parcel/branch)<br>`related_id` (optional) |
| تفاصيل محادثة   | `GET`   | `/api/v1/chat/conversations/{id}`          | -                                                                              |
| جلب الرسائل     | `GET`   | `/api/v1/chat/conversations/{id}/messages` | `page=1`                                                                       |
| إرسال رسالة     | `POST`  | `/api/v1/chat/conversations/{id}/messages` | `content` (أو `attachment`)                                                    |
| إغلاق محادثة    | `POST`  | `/api/v1/chat/conversations/{id}/close`    | -                                                                              |

---

## ملاحظات هامة للمطورين

1. **صيغة القنوات:** تأكد من أن الـ client يضيف بادئة `private-` للقنوات الخاصة عند الاتصال، أو استخدم القناة باسمها الكامل إذا كنت تستخدم مكتبة لا تفعل ذلك تلقائياً (لكن Pusher convention يتطلب البادئة في الـ auth request).
2. **أنواع الملفات:** عند رفع ملف في المحادثة، استخدم `MultipartRequest` في Flutter وأرسل الملف في حقل `attachment` مع `type` (اختياري).

3. **التحقق (Auth):** جميع الروابط والقنوات تتطلب Bearer Token صالح في الـ Header.
