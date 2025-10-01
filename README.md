feat: Completed APIs for authentication, blogs, authors, and categories.

Implemented Login and Registration endpoints

Added full CRUD operations for Blogs

Added full CRUD operations for Blog Authors

Added full CRUD operations for Categories

STRIPE_KEY=pk_test_51Q67z3J3iozVoSiONGuImyz2T1gvs3p5DYE67kdDjfkBl3kFcsQKotue68EV2bOByRCcsvULNiLL5zr2wO9jBVKq00ca6jYEZo
STRIPE_SECRET=sk_test_51Q67z3J3iozVoSiOCV9087J6fQEWqMaMC976QrE7k0y2XYgRDTSr8GvmQsPAvEFuvAkXy4GZ2ssvbO5fwsv1B7jM00prnpd3QZ

GOOGLE_CLIENT_ID=540695287885-pvmk1jmidm749stqu6rffhalps2bnbpr.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI_LOGIN=http://localhost:5173/google/callback
GOOGLE_REDIRECT_URI=http://localhost:5173/dashboard

STRIPE_WEBHOOK_SECRET=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourgmail@gmail.com
MAIL_PASSWORD=your_app_password # use app password, not Gmail password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourgmail@gmail.com
MAIL_FROM_NAME="Your App"

MAIL_TO_ADDRESS=support@yourdomain.com
MAIL_TO_NAME=

<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://localhost:5174',
        'http://127.0.0.1:5174',
        'https://snapshotalbums.vercel.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
