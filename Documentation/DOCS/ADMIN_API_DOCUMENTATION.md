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
*   **مثال للاستجابة:**
    ```json
    {
      "status": "success",
      "data": {
        "stats": {
          "total_parcels": 1500,
          "total_branches": 12,
          "total_employees": 45,
          "total_users": 300,
          "recent_parcels": [ ... ]
        }
      },
      "message": "System stats retrieved"
    }
    ```

### 7.2 إدارة الفروع (Branches Management)

#### عرض جميع الفروع
*   **المسار:** `GET /api/v1/super-admin/branches`
*   **الوصف:** الحصول على قائمة بكافة الفروع المسجلة في النظام مع بيانات المدينة والدولة.
*   **مثال للطلب:**
    `GET /api/v1/super-admin/branches`

#### إنشاء فرع جديد
*   **المسار:** `POST /api/v1/super-admin/branches`
*   **محتويات الطلب (Body):**
    ```json
    {
      "branch_name": "فرع حلب الرئيسي",
      "city_id": 2,
      "address": "حي الفرقان - الشارع العام",
      "phone": "021-1234567"
    }
    ```
*   **الاستجابة:** تفاصيل الفرع الذي تم إنشاؤه بنجاح.

### 7.3 إدارة الموظفين (Employees Management)

#### عرض جميع الموظفين
*   **المسار:** `GET /api/v1/super-admin/employees`
*   **الوصف:** استعراض قائمة بكافة الموظفين في النظام مع تحديد الفرع الذي يعمل به كل موظف.
*   **مثال للطلب:**
    `GET /api/v1/super-admin/employees`

#### تعيين موظف لفرع
*   **المسار:** `POST /api/v1/super-admin/assign-employee`
*   **الوصف:** تحويل مستخدم عادي إلى موظف وربطه بفرع محدد، أو نقل موظف من فرع لآخر.
*   **محتويات الطلب (Body):**
    ```json
    {
      "user_id": 15,
      "branch_id": 3
    }
    ```
*   **الاستجابة:** بيانات الموظف والفرع المرتبط به.

### 7.4 الرقابة الشاملة على الطرود (Global Parcel Monitoring)
*   **المسار:** `GET /api/v1/super-admin/all-parcels`
*   **الوصف:** عرض كافة الطرود الموجودة في النظام بغض النظر عن الفرع، مما يتيح للمدير الأكبر مراقبة حركة الشحن على مستوى الدولة.
*   **مثال للطلب:**
    `GET /api/v1/super-admin/all-parcels?page=1`
*   **الاستجابة:** قائمة مرقمة (Paginated) بكافة الطرود مع تفاصيل المرسل والمستقبل والفرع الحالي.
