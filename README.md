# RBPL-Project-SI-E
Tugas AKhir RBPL Sistem Dealer Mobil

/dealer-management-system
│
├── /app
│   ├── /controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── CarController.php
│   │   ├── CustomerController.php
│   │   ├── SalesController.php
│   │   ├── ServiceController.php
│   │   ├── ReportController.php
│   │   └── UserController.php
│   │
│   ├── /models
│   │   ├── User.php
│   │   ├── Car.php
│   │   ├── Customer.php
│   │   ├── Sale.php
│   │   ├── Service.php
│   │   └── Report.php
│   │
│   ├── /views
│   │   ├── /layouts
│   │   │   ├── header.php
│   │   │   ├── sidebar.php
│   │   │   ├── navbar.php
│   │   │   └── footer.php
│   │   │
│   │   ├── /auth
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   │
│   │   ├── /dashboard
│   │   │   └── index.php
│   │   │
│   │   ├── /cars
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── detail.php
│   │   │
│   │   ├── /customers
│   │   ├── /sales
│   │   ├── /services
│   │   └── /reports
│   │
│   ├── /core
│   │   ├── Database.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── Router.php
│   │   ├── Auth.php
│   │   ├── Middleware.php
│   │   └── Session.php
│   │
│   ├── /helpers
│   │   ├── url_helper.php
│   │   ├── auth_helper.php
│   │   ├── format_helper.php
│   │   └── upload_helper.php
│   │
│   ├── /middlewares
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── ManagerMiddleware.php
│   │
│   └── /config
│       ├── config.php
│       ├── database.php
│       └── routes.php
│
├── /public
│   ├── index.php
│   │
│   ├── /assets
│   │   ├── /css
│   │   ├── /js
│   │   ├── /images
│   │   └── /uploads
│   │
│   └── /.htaccess
│
├── /database
│   ├── dealer.sql
│   └── seed.sql
│
├── /storage
│   ├── logs
│   └── temp
│
├── /vendor
│
├── composer.json
├── README.md
└── .env
