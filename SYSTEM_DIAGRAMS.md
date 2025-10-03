# مخططات النظام - نظام إدارة الطرود والشحن

## 📊 فهرس المخططات

1. [مخطط هيكل النظام العام](#مخطط-هيكل-النظام-العام)
2. [مخطط قاعدة البيانات](#مخطط-قاعدة-البيانات)
3. [مخطط تدفق العمليات](#مخطط-تدفق-العمليات)
4. [مخطط API Architecture](#مخطط-api-architecture)
5. [مخطط نظام المصادقة](#مخطط-نظام-المصادقة)
6. [مخطط دورة حياة الطرد](#مخطط-دورة-حياة-الطرد)
7. [مخطط نظام التخويل](#مخطط-نظام-التخويل)
8. [مخطط لوحة الإدارة](#مخطط-لوحة-الإدارة)

---

## مخطط هيكل النظام العام

```mermaid
graph TB
    subgraph "Frontend Layer"
        A[Web Browser] --> B[Filament Admin Panel]
        A --> C[Mobile App]
        A --> D[Web App]
    end
    
    subgraph "API Layer"
        E[REST API v1] --> F[Authentication]
        E --> G[Authorization]
        E --> H[Rate Limiting]
    end
    
    subgraph "Business Logic Layer"
        I[Controllers] --> J[Services]
        J --> K[Observers]
        K --> L[Events]
    end
    
    subgraph "Data Layer"
        M[Models] --> N[Database]
        O[Cache] --> N
        P[File Storage] --> N
    end
    
    subgraph "External Services"
        Q[Telegram Bot]
        R[Email Service]
        S[Geocoding API]
    end
    
    B --> E
    C --> E
    D --> E
    E --> I
    I --> M
    J --> Q
    J --> R
    J --> S
```

---

## مخطط قاعدة البيانات

```mermaid
erDiagram
    USERS ||--o{ PARCELS : creates
    USERS ||--o{ PARCEL_AUTHORIZATIONS : creates
    USERS ||--o{ APPOINTMENTS : books
    USERS ||--o{ RATES : gives
    USERS ||--o{ PARCEL_HISTORIES : tracks
    USERS ||--|| EMPLOYEES : becomes
    
    CITIES ||--o{ USERS : contains
    CITIES ||--o{ BRANCHES : contains
    CITIES ||--o{ GUEST_USERS : contains
    CITIES }o--|| COUNTRIES : belongs_to
    
    BRANCHES ||--o{ BRANCH_ROUTES : from
    BRANCHES ||--o{ BRANCH_ROUTES : to
    BRANCHES ||--o{ EMPLOYEES : employs
    BRANCHES ||--o{ APPOINTMENTS : hosts
    BRANCHES ||--o{ RATES : receives
    
    BRANCH_ROUTES ||--o{ PARCELS : carries
    BRANCH_ROUTES ||--o{ BRANCH_ROUTE_DAYS : scheduled_on
    BRANCH_ROUTES ||--o{ SHIPMENTS : uses
    
    PARCELS ||--o{ PARCEL_HISTORIES : has
    PARCELS ||--o{ PARCEL_AUTHORIZATIONS : has
    PARCELS ||--o{ RATES : receives
    PARCELS ||--|| APPOINTMENTS : scheduled_for
    PARCELS ||--o{ PARCEL_SHIPMENT_ASSIGNMENTS : assigned_to
    
    GUEST_USERS ||--o{ PARCELS : creates
    GUEST_USERS ||--o{ PARCEL_AUTHORIZATIONS : authorized_for
    
    EMPLOYEES ||--o{ TRUCKS : drives
    EMPLOYEES ||--o{ PARCEL_SHIPMENT_ASSIGNMENTS : confirms_pickup
    EMPLOYEES ||--o{ PARCEL_SHIPMENT_ASSIGNMENTS : confirms_delivery
    
    TRUCKS ||--o{ BRANCH_ROUTE_DAYS : assigned_to
    TRUCKS ||--o{ SHIPMENTS : carries
    
    SHIPMENTS ||--o{ PARCEL_SHIPMENT_ASSIGNMENTS : contains
    
    USERS {
        int id PK
        string first_name
        string last_name
        string email UK
        string user_name UK
        string password
        string phone UK
        string address
        string national_number UK
        date birthday
        timestamp email_verified_at
        int city_id FK
        string image_profile
        timestamps
    }
    
    PARCELS {
        int id PK
        int sender_id FK
        enum sender_type
        int route_id FK
        string reciver_name
        string reciver_address
        string reciver_phone
        decimal weight
        decimal cost
        tinyint is_paid
        enum parcel_status
        string tracking_number UK
        int appointment_id FK
        timestamps
    }
    
    BRANCHES {
        int id PK
        string branch_name
        int city_id FK
        string address
        string phone
        string email
        decimal latitude
        decimal longitude
        tinyint status
        timestamps
    }
```

---

## مخطط تدفق العمليات

### عملية إنشاء طرد جديد

```mermaid
flowchart TD
    A[المستخدم يطلب إنشاء طرد] --> B{نوع المرسل؟}
    B -->|مستخدم مسجل| C[تسجيل الدخول]
    B -->|ضيف| D[إدخال بيانات الضيف]
    
    C --> E[إدخال بيانات الطرد]
    D --> E
    
    E --> F[اختيار المسار]
    F --> G[حساب التكلفة تلقائياً]
    G --> H[إنشاء الطرد]
    H --> I[توليد رقم التتبع]
    I --> J[حفظ في قاعدة البيانات]
    J --> K[إنشاء سجل في التاريخ]
    K --> L[إرسال إشعار للمستخدم]
    L --> M[عرض رقم التتبع]
```

### عملية التخويل

```mermaid
flowchart TD
    A[المستخدم يطلب تخويل طرد] --> B{نوع المستخدم المفوض؟}
    B -->|مستخدم مسجل| C[اختيار المستخدم]
    B -->|ضيف| D[إدخال بيانات الضيف]
    
    C --> E[إنشاء التخويل]
    D --> F[إنشاء حساب ضيف]
    F --> E
    
    E --> G[توليد رمز التخويل]
    G --> H[تعيين تاريخ انتهاء الصلاحية]
    H --> I[حفظ في قاعدة البيانات]
    I --> J[إرسال رمز التخويل]
    J --> K[انتظار الاستخدام]
    
    K --> L{تم استخدام التخويل؟}
    L -->|نعم| M[تسجيل وقت الاستخدام]
    L -->|لا| N[انتهاء الصلاحية]
```

---

## مخطط API Architecture

```mermaid
graph TB
    subgraph "Client Layer"
        A[Mobile App]
        B[Web App]
        C[Admin Panel]
    end
    
    subgraph "API Gateway"
        D[Rate Limiting]
        E[Authentication]
        F[CORS]
    end
    
    subgraph "API Controllers"
        G[AuthController]
        H[ParcelController]
        I[AuthorizationController]
        J[AppointmentController]
        K[TelegramOtpController]
    end
    
    subgraph "Service Layer"
        L[AuthService]
        M[ParcelService]
        N[AuthorizationService]
        O[TelegramOtpService]
        P[GeocodingService]
    end
    
    subgraph "Data Layer"
        Q[Models]
        R[Database]
        S[Cache]
    end
    
    subgraph "External APIs"
        T[Telegram API]
        U[Email Service]
        V[Geocoding API]
    end
    
    A --> D
    B --> D
    C --> D
    D --> E
    E --> F
    F --> G
    F --> H
    F --> I
    F --> J
    F --> K
    
    G --> L
    H --> M
    I --> N
    J --> O
    K --> O
    
    L --> Q
    M --> Q
    N --> Q
    O --> Q
    P --> Q
    
    Q --> R
    Q --> S
    
    O --> T
    L --> U
    P --> V
```

---

## مخطط نظام المصادقة

```mermaid
sequenceDiagram
    participant U as User
    participant A as API
    participant S as AuthService
    participant D as Database
    participant T as Token
    
    U->>A: POST /api/v1/login
    A->>S: login(credentials)
    S->>D: check user credentials
    D-->>S: user data
    S->>T: create access token
    T-->>S: token
    S-->>A: user + token
    A-->>U: success response
    
    Note over U,T: User is now authenticated
    
    U->>A: GET /api/v1/parcel (with token)
    A->>T: validate token
    T-->>A: token valid
    A->>D: fetch parcels
    D-->>A: parcels data
    A-->>U: parcels response
```

---

## مخطط دورة حياة الطرد

```mermaid
stateDiagram-v2
    [*] --> PENDING: Create Parcel
    PENDING --> CONFIRMED: Employee Confirms
    CONFIRMED --> IN_TRANSIT: Start Shipping
    IN_TRANSIT --> OUT_FOR_DELIVERY: Out for Delivery
    OUT_FOR_DELIVERY --> READY_FOR_PICKUP: Ready for Pickup
    READY_FOR_PICKUP --> DELIVERED: Successfully Delivered
    READY_FOR_PICKUP --> FAILED: Delivery Failed
    FAILED --> RETURNED: Return to Sender
    DELIVERED --> [*]
    RETURNED --> [*]
    CANCELLED --> [*]
    
    PENDING --> CANCELLED: Cancel by User
    CONFIRMED --> CANCELLED: Cancel by Admin
    IN_TRANSIT --> CANCELLED: Cancel by Admin
```

---

## مخطط نظام التخويل

```mermaid
graph TB
    subgraph "إنشاء التخويل"
        A[المستخدم يطلب تخويل] --> B{نوع المستخدم المفوض}
        B -->|مسجل| C[اختيار من قائمة المستخدمين]
        B -->|ضيف| D[إدخال بيانات الضيف]
        C --> E[إنشاء سجل تخويل]
        D --> F[إنشاء حساب ضيف]
        F --> E
        E --> G[توليد رمز تخويل فريد]
        G --> H[تعيين تاريخ انتهاء الصلاحية]
    end
    
    subgraph "استخدام التخويل"
        I[المستخدم المفوض يقدم الرمز] --> J[التحقق من صحة الرمز]
        J --> K{الرمز صحيح؟}
        K -->|نعم| L{لم يتم استخدامه؟}
        K -->|لا| M[رفض الطلب]
        L -->|نعم| N{لم تنته صلاحيته؟}
        L -->|لا| O[الرمز مستخدم مسبقاً]
        N -->|نعم| P[تسجيل وقت الاستخدام]
        N -->|لا| Q[انتهت صلاحية الرمز]
        P --> R[تسليم الطرد]
    end
```

---

## مخطط لوحة الإدارة

```mermaid
graph TB
    subgraph "Filament Admin Panel"
        A[Dashboard] --> B[Parcels Management]
        A --> C[Users Management]
        A --> D[Branches Management]
        A --> E[Employees Management]
        A --> F[Appointments Management]
        A --> G[Authorizations Management]
        A --> H[Reports & Analytics]
    end
    
    subgraph "Parcels Management"
        B --> B1[Create Parcel]
        B --> B2[Edit Parcel]
        B --> B3[View Parcel Details]
        B --> B4[Track Parcel Status]
        B --> B5[Delete Parcel]
    end
    
    subgraph "Users Management"
        C --> C1[Create User]
        C --> C2[Edit User]
        C --> C3[View User Profile]
        C --> C4[Manage Permissions]
        C --> C5[User Restrictions]
    end
    
    subgraph "Branches Management"
        D --> D1[Create Branch]
        D --> D2[Edit Branch]
        D --> D3[Manage Routes]
        D --> D4[Location Services]
        D --> D5[Branch Analytics]
    end
    
    subgraph "Reports & Analytics"
        H --> H1[Parcel Statistics]
        H --> H2[User Analytics]
        H --> H3[Revenue Reports]
        H --> H4[Performance Metrics]
        H --> H5[Export Data]
    end
```

---

## مخطط نظام الإشعارات

```mermaid
graph TB
    subgraph "Notification Sources"
        A[Parcel Status Change]
        B[Appointment Booking]
        C[Authorization Created]
        D[System Alerts]
    end
    
    subgraph "Notification Channels"
        E[Email Notifications]
        F[Telegram Messages]
        G[In-App Notifications]
        H[SMS Notifications]
    end
    
    subgraph "Notification Processing"
        I[Notification Service]
        J[Queue System]
        K[Template Engine]
        L[Delivery Service]
    end
    
    A --> I
    B --> I
    C --> I
    D --> I
    
    I --> J
    J --> K
    K --> L
    
    L --> E
    L --> F
    L --> G
    L --> H
```

---

## مخطط الأمان والحماية

```mermaid
graph TB
    subgraph "Security Layers"
        A[API Rate Limiting]
        B[Authentication]
        C[Authorization]
        D[Input Validation]
        E[Data Encryption]
    end
    
    subgraph "Protection Mechanisms"
        F[CSRF Protection]
        G[XSS Prevention]
        H[SQL Injection Prevention]
        I[Password Hashing]
        J[Token Management]
    end
    
    subgraph "Monitoring & Logging"
        K[Security Logs]
        L[Access Logs]
        M[Error Tracking]
        N[Audit Trail]
        O[Alert System]
    end
    
    A --> F
    B --> I
    C --> J
    D --> G
    E --> H
    
    F --> K
    G --> L
    H --> M
    I --> N
    J --> O
```

---

## مخطط التكامل مع الخدمات الخارجية

```mermaid
graph TB
    subgraph "Internal System"
        A[Laravel Application]
        B[Database]
        C[File Storage]
        D[Cache System]
    end
    
    subgraph "External Services"
        E[Telegram Bot API]
        F[Email Service Provider]
        G[Geocoding Service]
        H[Payment Gateway]
        I[SMS Service]
    end
    
    subgraph "Integration Points"
        J[TelegramOtpService]
        K[EmailNotificationService]
        L[GeocodingService]
        M[PaymentService]
        N[SmsService]
    end
    
    A --> J
    A --> K
    A --> L
    A --> M
    A --> N
    
    J --> E
    K --> F
    L --> G
    M --> H
    N --> I
```

---

**هذه المخططات توفر رؤية شاملة لهيكل النظام وتدفق العمليات والتفاعلات بين المكونات المختلفة.**
