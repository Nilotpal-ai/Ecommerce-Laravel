Laravel 12 Full-Stack eCommerce Application
A modern, full-stack eCommerce application built with Laravel 12, Laravel Fortify, Blade, Tailwind CSS, and Stripe Checkout. The platform includes a customer-facing storefront with hierarchical category browsing, dynamic price/stock filtering, cart management, customer order tracking, and a dedicated admin management dashboard.
---
Features
Storefront & Catalog:
Hierarchical category tree filtering (parent and subcategories).
Multi-attribute filtering (price range, in-stock availability, text search).
Effective price sorting (`COALESCE(sale\_price, price)`).
Responsive product display with image previews and badges.
Authentication & Authorization:
Powered by Laravel Fortify and Laravel Socialite (OAuth: Google, GitHub, Facebook, LinkedIn).
Custom redirect workflows (`LoginResponse`, `LogoutResponse`, `RegisterResponse`) routing users directly to the storefront (`/shop`) and post-logout to `/login`.
Role-based middleware (`auth`, `admin`) protecting administration areas.
Cart & Checkout:
Session-backed cart management with real-time totals.
Integration with Stripe Checkout and secure webhook processing (`stripe.webhook`).
Customer Portal:
Customer order history and visual order tracking timeline (`/orders/{id}`).
Order confirmation and shipping update transactional emails via queued Markdown Mailables (`x-mail::\*`).
Admin Dashboard:
Metrics overview (pending orders, revenue).
Full CRUD for categories with circular-parenting prevention.
Product management with multi-image handling and primary image selection.
Order status workflow management (Pending, Processing, Shipped, Delivered, Cancelled).
---
Tech Stack & Prerequisites
PHP: ^8.2
Composer: Dependency manager
Database: MySQL / MariaDB (e.g. XAMPP)
Node.js & npm: For Vite frontend asset compilation
Git: Version control
Stripe CLI: (Optional, for local webhook testing)
---
Step-by-Step Installation & Setup
1. Clone the Repository
```bash
git clone https://github.com/your-username/your-repository-name.git
cd your-repository-name
```
2. Install Dependencies
```bash
composer install
npm install
```
3. Environment Configuration
Copy the sample environment file and generate the application encryption key:
```bash
cp .env.example .env
php artisan key:generate
```
Open `.env` and configure your local database and server settings:
```env
APP\_NAME="Laravel eCommerce"
APP\_ENV=local
APP\_KEY=base64:...
APP\_DEBUG=true
APP\_URL=http://localhost:8000

DB\_CONNECTION=mysql
DB\_HOST=127.0.0.1
DB\_PORT=3306
DB\_DATABASE=ecommerce\_db
DB\_USERNAME=root
DB\_PASSWORD=

# Queue Driver
QUEUE\_CONNECTION=database

# Mail Configuration (Example using Gmail SMTP or Mailtrap)
MAIL\_MAILER=smtp
MAIL\_HOST=smtp.gmail.com
MAIL\_PORT=587
MAIL\_USERNAME=your\_email@gmail.com
MAIL\_PASSWORD=your\_16\_character\_app\_password
MAIL\_ENCRYPTION=tls
MAIL\_FROM\_ADDRESS="your\_email@gmail.com"
MAIL\_FROM\_NAME="${APP\_NAME}"

# Stripe Keys
STRIPE\_KEY=pk\_test\_...
STRIPE\_SECRET=sk\_test\_...
STRIPE\_WEBHOOK\_SECRET=whsec\_...
```
4. Database Migration & Seeding
Create your MySQL database (e.g. in phpMyAdmin or MySQL CLI), then run migrations:
```bash
php artisan migrate --seed
```
5. Storage Symlink
Link the public storage directory so uploaded product images display correctly:
```bash
php artisan storage:link
```
6. Build Frontend Assets
Compile Tailwind CSS and JavaScript assets:
```bash
npm run build
# OR for live development:
# npm run dev
```
---
Running the Application Locally
To test all features (storefront, admin, queues, and Stripe webhooks), run the following terminal processes:
Terminal 1: Web Server
```bash
php artisan serve
```
The application will be accessible at: `http://localhost:8000`
Terminal 2: Queue Worker (Required for Emails)
```bash
php artisan queue:work --queue=emails,default
```
Terminal 3: Stripe Webhook Listener (Optional for Checkout)
```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```
---
Useful Artisan Commands
```bash
# Clear all caches (routes, views, config)
php artisan optimize:clear

# Clear specific caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart or flush background jobs
php artisan queue:restart
php artisan queue:flush
```
---
License
This project is open-sourced software licensed under the MIT license
