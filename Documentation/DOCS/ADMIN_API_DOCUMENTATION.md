# توثيق نقاط الوصول الخاصة بالمدير والموظف (Admin/Employee API Documentation)

هذا الملف يشرح جميع نقاط الوصول (Endpoints) التي تم بناؤها لإدارة عمليات النظام من قبل الموظفين والمديرين. تعتمد هذه العمليات على هيكلية (Controller -> Service -> Model) وتستخدم صلاحيات خاصة بالموظفين.

---

## 1. الأمان والمصادقة (Authentication & Security)

جميع نقاط الوصول في هذا القسم محمية بواسطة:
1.  **Laravel Sanctum**: يجب إرسال `Bearer Token` في الترويسة (Header) الخاص بالطلب.
2.  **CheckEmployeeRole Middleware**: هذا الوسيط يتحقق من أن المستخدم المسجل لديه سجل في جدول `employees`.

**الترويسة المطلوبة لجميع الطلبات:**
```http
Authorization: Bearer {your_token}
Accept: application/json
Content-Type: application/json
```

---

## 2. معلومات الفرع (Branch Information)

### الحصول على بيانات فرع الموظف الحالي
*   **المسار:** `GET /api/v1/admin/my-branch`
*   **الوصف:** استرجاع كافة التفاصيل المتعلقة بالفرع الذي ينتمي إليه الموظف الحالي.
*   **مثال للطلب (Request):**
    `GET /api/v1/admin/my-branch`
*   **مثال للاستجابة (Response):**
    ```json
    {
      "status": "success",
      "data": {
        "branch": {
          "id": 1,
          "name": "فرع المزة",
          "city": { "id": 1, "name": "دمشق" },
          "routes_from": [...],
          "routes_to": [...]
        }
      },
      "message": "Branch details retrieved"
    }
    ```

---

## 3. إدارة الطرود (Parcel Management)

### عرض كافة الطرود المرتبطة بالفرع
*   **المسار:** `GET /api/v1/admin/parcels`
*   **الوصف:** عرض الطرود الصادرة من فرع الموظف أو المتجهة إليه.
*   **مثال للطلب:**
    `GET /api/v1/admin/parcels`

### سجل تتبع الطرد
*   **المسار:** `GET /api/v1/admin/parcels/{id}/history`
*   **الوصف:** الحصول على تاريخ الحالات التي مر بها الطرد.
*   **مثال للطلب:**
    `GET /api/v1/admin/parcels/10/history`

### تأكيد استلام طرد في الفرع
*   **المسار:** `POST /api/v1/admin/parcels/{id}/confirm-reception`
*   **الوصف:** تأكيد وصول الطرد فعلياً للفرع.
*   **مثال للطلب:**
    `POST /api/v1/admin/parcels/10/confirm-reception` (لا يوجد Body)

### تحديث حالة الطرد يدوياً
*   **المسار:** `POST /api/v1/admin/parcels/{id}/update-status`
*   **محتويات الطلب (Body):**
    ```json
    {
      "status": "delivered" 
    }
    ```
*   **الحالات الممكنة:** `pending`, `picked_up`, `at_branch`, `in_transit`, `delivered`, `returned`.
*   **مثال للطلب:**
    `POST /api/v1/admin/parcels/10/update-status`

---

## 4. إدارة المواعيد (Appointment Management)

### عرض مواعيد الفرع
*   **المسار:** `GET /api/v1/admin/appointments`
*   **الوصف:** عرض جميع مواعيد الاستلام/التسليم الخاصة بفرع الموظف.
*   **مثال للطلب:**
    `GET /api/v1/admin/appointments`

### تحديث حالة الموعد
*   **المسار:** `POST /api/v1/admin/appointments/{id}/status`
*   **محتويات الطلب (Body):**
    ```json
    {
      "status": "completed"
    }
    ```
*   **الحالات الممكنة:** `pending`, `completed`, `cancelled`.
*   **مثال للطلب:**
    `POST /api/v1/admin/appointments/5/status`

---

## 5. إدارة الشحنات (Shipment Management)

### عرض الشحنات (الرحلات)
*   **المسار:** `GET /api/v1/admin/shipments`
*   **الوصف:** عرض الشحنات التي تنطلق من الفرع أو تصل إليه.
*   **مثال للطلب:**
    `GET /api/v1/admin/shipments`

### تسجيل انطلاق الشحنة
*   **المسار:** `POST /api/v1/admin/shipments/{id}/depart`
*   **الوصف:** تغيير حالة الشحنة إلى "قيد النقل".
*   **مثال للطلب:**
    `POST /api/v1/admin/shipments/2/depart`

### تسجيل وصول الشحنة
*   **المسار:** `POST /api/v1/admin/shipments/{id}/arrive`
*   **الوصف:** تسجيل وصول الشحنة للفرع الوجهة وتحديث حالة الطرود بداخلها تلقائياً.
*   **مثال للطلب:**
    `POST /api/v1/admin/shipments/2/arrive`

---

## 6. إدارة الشاحنات (Truck Management)

### عرض قائمة الشاحنات
*   **المسار:** `GET /api/v1/admin/trucks`
*   **الوصف:** عرض الشاحنات المرتبطة بالفرع.
*   **مثال للطلب:**
    `GET /api/v1/admin/trucks`

### تفاصيل شاحنة
*   **المسار:** `GET /api/v1/admin/trucks/{id}`
*   **مثال للطلب:**
    `GET /api/v1/admin/trucks/1`

### تبديل حالة توفر الشاحنة
*   **المسار:** `POST /api/v1/admin/trucks/{id}/toggle-status`
*   **الوصف:** تحويل الشاحنة من `available` إلى `unavailable` والعكس.
*   **مثال للطلب:**
    `POST /api/v1/admin/trucks/1/toggle-status`

---

## ملاحظات هامة للمبرمجين:
1.  **قاعدة البيانات:** يتم تسجيل الوقت والمستخدم في كل عملية تغيير حالة لضمان الشفافية.
2.  **الفلترة:** النظام يتعرف تلقائياً على فرع الموظف من خلال التوكن المرسل، لذا لا داعي لإرسال `branch_id` في الطلبات.
3.  **الأخطاء:** في حال حدوث خطأ، ستكون الاستجابة بالتنسيق التالي:
    ```json
    {
      "status": "error",
      "message": "رسالة الخطأ هنا"
    }
    ```

---

## 7. صلاحيات المدير الأكبر (Super Admin)

المدير الأكبر لديه صلاحيات كاملة وشاملة على مستوى النظام بالكامل، ولا تقتصر رؤيته أو تحكمه على فرع محدد.

### 7.1 إحصائيات النظام العامة
*   **المسار:** `GET /api/v1/super-admin/stats`
*   **الوصف:** استرجاع نظرة عامة شاملة عن أداء النظام وأرقام إجمالية.
*   **مثال للطلب:**
    `GET /api/v1/super-admin/stats`
*   **الاستجابة المتوقعة (Full Response):**
    ```json
    {
      "status": "success",
      "data": {
        "stats": {
          "total_parcels": 1500, // integer
          "total_branches": 12,  // integer
          "total_employees": 45, // integer
          "total_users": 300,    // integer
          "recent_parcels": [    // array of objects (Parcel Schema)
            {
              "id": 101,
              "parcel_status": "pending",
              "created_at": "2026-02-03T10:00:00Z"
            }
          ]
        }
      },
      "message": "System stats retrieved"
    }
    ```

### 7.2 إدارة الفروع (Branches Management)

#### عرض جميع الفروع
*   **المسار:** `GET /api/v1/super-admin/branches`
*   **الاستجابة المتوقعة:**
    ```json
    {
      "status": "success",
      "data": {
        "branches": [ // array of objects (Branch Schema)
          {
            "id": 1,
            "branch_name": "فرع المزة",
            "city_id": 1,
            "address": "دمشق - المزة",
            "phone": "011-222333",
            "city": {
              "id": 1,
              "name": "دمشق",
              "country": { "id": 1, "name": "سوريا" }
            }
          }
        ]
      },
      "message": "All branches retrieved"
    }
    ```

#### إنشاء فرع جديد
*   **المسار:** `POST /api/v1/super-admin/branches`
*   **محتويات الطلب (Body):**
    ```json
    {
      "branch_name": "string",
      "city_id": "integer (exists in cities)",
      "address": "string (optional)",
      "phone": "string (optional)"
    }
    ```
*   **الاستجابة المتوقعة:** نفس هيكل الفرع (Branch Schema) مع كود الحالة 201.

### 7.3 إدارة الموظفين (Employees Management)

#### عرض جميع الموظفين
*   **المسار:** `GET /api/v1/super-admin/employees`
*   **الاستجابة المتوقعة:**
    ```json
    {
      "status": "success",
      "data": {
        "employees": [ // array of objects (Employee Schema)
          {
            "id": 5,
            "user_id": 15,
            "branch_id": 1,
            "status": "active",
            "user": {
              "id": 15,
              "name": "أحمد محمد",
              "email": "ahmed@example.com"
            },
            "branch": {
              "id": 1,
              "branch_name": "فرع المزة"
            }
          }
        ]
      },
      "message": "All employees retrieved"
    }
    ```

#### تعيين موظف لفرع
*   **المسار:** `POST /api/v1/super-admin/assign-employee`
*   **محتويات الطلب (Body):**
    ```json
    {
      "user_id": "integer (exists in users)",
      "branch_id": "integer (exists in branches)"
    }
    ```
*   **الاستجابة المتوقعة:** كائن الموظف الجديد (Employee Schema).

### 7.4 الرقابة الشاملة على الطرود (Global Parcel Monitoring)
*   **المسار:** `GET /api/v1/super-admin/all-parcels`
*   **الاستجابة المتوقعة:**
    ```json
    {
      "status": "success",
      "data": {
        "parcels": {
          "current_page": 1,
          "data": [ // array of objects (Parcel Schema)
            {
              "id": 10,
              "sender_id": 50,
              "receiver_id": 60,
              "branch_id": 1,
              "parcel_status": "in_transit",
              "sender": { "id": 50, "name": "المرسل" },
              "receiver": { "id": 60, "name": "المستقبل" },
              "branch": { "id": 1, "branch_name": "فرع المزة" }
            }
          ],
          "total": 1500,
          "per_page": 15
        }
      },
      "message": "Global parcels retrieved"
    }
    ```

---

## 8. نماذج البيانات (Data Schemas & Types)

| الحقل | نوع البيانات | الوصف |
| :--- | :--- | :--- |
| `id` | Integer | المعرف الفريد |
| `name` / `branch_name` | String | الاسم |
| `status` | String | الحالة (نشط، غير نشط، إلخ) |
| `created_at` | DateTime (ISO 8601) | تاريخ الإنشاء |
| `user_id` | Integer | معرف المستخدم المرتبط |
| `branch_id` | Integer | معرف الفرع المرتبط |
| `parcel_status` | Enum | حالات الطرد (pending, at_branch, in_transit, delivered) |
| `phone` | String | رقم الهاتف |
| `city_id` | Integer | معرف المدينة |
