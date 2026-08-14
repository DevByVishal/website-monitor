# Website Monitor

A Laravel + Vue.js Single Page Application that monitors client websites and sends an email notification when a monitored website becomes unavailable.

## 1. Technology Stack

- Laravel
- PHP 8.x+
- MySQL / MariaDB
- Vue.js
- Vite
- Axios
- Laravel HTTP Client
- Laravel Scheduler
- Laravel Mail #Used LOG for local

## 2. Features

- Client records and multiple websites per client
- Client-to-website one-to-many relationship
- Vue.js SPA
- Client email dropdown
- Client website listing
- Website hyperlinks
- External-link confirmation dialog
- Website monitoring every 15 minutes
- 10-second request timeout
- HTTP error detection
- Connection and timeout handling
- Email alert when a website is unavailable
- Laravel Scheduler
- Automated tests
- Environment-based configuration
- Production/scalability considerations

## 3. System Requirements

Install:

- PHP 8.x or later
- Composer
- Node.js
- npm
- MySQL
- Git
---

# 4. Installation

## Step 1: Clone the Repository

```bash
git clone <repository-url>
cd website-monitor
```

## Step 2: Install Laravel Dependencies

```bash
composer install
```

## Step 3: Create Environment File

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

### Linux / macOS

```bash
cp .env.example .env
```

## Step 4: Generate Application Key

```bash
php artisan key:generate
```

---

# 5. Database Setup

Create a MySQL database.

Example:

```text
website_monitor
```

Configure `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=website_monitor
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

If seed data is available:

```bash
php artisan db:seed
```

Or:

```bash
php artisan migrate --seed
```

For a completely fresh local database:

```bash
php artisan migrate:fresh --seed
```

# 6. Mail Configuration
Use Laravel Log
--check laravel logs

## Local Development

Use Laravel's log mailer so no real email is sent.

Add to `.env`:

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=do-not-reply@example.com
MAIL_FROM_NAME="Website Monitor"
```

Emails can be checked in:

```text
storage/logs/laravel.log
```

# 7. Start Laravel Backend

Run:

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

---

# 8. Frontend Setup

Open a second terminal.

Install frontend dependencies:

```bash
npm install
```

Start Vite:

```bash
npm run dev
```

Keep the Vite development server running during development.

Open:

```text
http://127.0.0.1:8000
```
---

# 10. Frontend User Flow

```text
Select Client
      ↓
Load Client Websites
      ↓
Choose Website
      ↓
Confirmation Dialog
      ↓
Continue / Cancel
      ↓
Continue → Open Website in New Tab
Cancel   → Close Dialog
```

## Client Selection

The SPA home page displays a select input containing all configured client email addresses.

Example:

```text
Select Client

[ client@example.com ]
```

After selecting a client, only that client's websites are displayed.

## Website List

Websites are displayed as a bulleted list of hyperlinks.

Example:

```text
Websites

• https://example.com
• https://google.com
• https://github.com
```

## External Link Confirmation

Clicking a website displays:

```text
You are about to visit {website}.
Do you want to continue?
```

Actions:

```text
[ Cancel ] [ Continue ]
```

**Cancel:** closes the dialog.

**Continue:** opens the website in a new browser tab.

External websites should be opened with:

```javascript
window.open(
    website.url,
    '_blank',
    'noopener,noreferrer'
);
```

---

# 11. Website Monitoring

Every configured website homepage is checked every 15 minutes throgh cron.

A website is considered unavailable when:

- The request cannot complete within 10 seconds.
- The request times out.
- A connection/network error occurs.
- DNS resolution fails.
- The HTTP response returns an error status.

One failed website must not stop the remaining websites from being checked.

---

# 12. Monitoring Command

The monitoring logic is implemented as a Laravel Artisan command.

Run it manually:

```bash
php artisan websites:monitor
```

The command:

1. Retrieves configured websites.
2. Identifies the associated client.
3. Sends an HTTP request.
4. Applies a 10-second timeout.
5. Checks the HTTP response.
6. Handles connection, timeout, and request exceptions.
7. Sends an email if the website is unavailable.
8. Continues processing remaining websites.

Architecture:

```text
Get Websites
     ↓
For Each Website
     ↓
HTTP Request
     ↓
  ┌───────┐
  │       │
 UP      DOWN
  │       │
  ↓       ↓
Continue  Send Email (Check Logs)
```

---

# 13. Test Monitoring Manually

Run:

```bash
php artisan websites:monitor
```

For an available website, the check should complete successfully.

For testing failure handling, configure a deliberately unavailable URL and run:

```bash
php artisan websites:monitor
```

Verify that:

1. The website is detected as unavailable.
2. The associated client is identified.
3. The email is generated.
4. Monitoring continues with other websites.

When using the local mail driver, inspect:

```text
storage/logs/laravel.log
```

---

# 14. Email Alert

When a website is unavailable:

### Recipient

The email address of the website's associated client.

### Sender

```text
do-not-reply@example.com
```

### Subject

```text
{website URL} is down!
```

### Body

```text
{website URL} is down!
```

---

# 15. Scheduler

The monitoring command is scheduled to run every 15 minutes.

Check the configured schedule:

```bash
php artisan schedule:list
```

The monitoring task should be configured as:

```text
websites:monitor
Every 15 minutes
```

## Run Scheduler Locally

For local development:

```bash
php artisan schedule:work
```

Keep this terminal running.

Laravel will execute the monitoring command according to its configured schedule.

## Run Scheduler Manually

You can also run:

```bash
php artisan schedule:run
```

This checks whether any scheduled task is due.

---


# 16. Queue

## Current Architecture

Current architecture:

```text
Laravel Scheduler
       ↓
Monitoring Command
       ↓
Website Checks
       ↓
Email Alert
```

This keeps the assessment implementation simple and easy to deploy.

---

# 17. Testing

Run the complete Laravel test suite:

```bash
php artisan test
```

Alternative:

```bash
vendor/bin/phpunit
```

Tests should cover:

- Client API
- Client/website relationship
- Website monitoring
- Successful website checks
- HTTP error handling
- Timeout handling
- Email notifications
- Monitoring behavior

---

# 18. Recommended Verification Before Submission

Run these commands one by one.

## 1. Fresh Database

```bash
php artisan migrate:fresh --seed
```

## 2. Start Laravel

```bash
php artisan serve
```

## 3. Start Frontend

In another terminal:

```bash
npm run dev
```

## 4. Verify API

Open:

```text
http://127.0.0.1:8000/api/clients
```

## 5. Open Frontend

Open:

```text
http://127.0.0.1:8000
```

## 6. Test Monitoring

```bash
php artisan websites:monitor
```

## 7. Check Scheduler

```bash
php artisan schedule:list
```

## 8. Run Scheduler

```bash
php artisan schedule:work
```

## 9. Run Tests

```bash
php artisan test
```

## 10. Build Frontend

```bash
npm run build
```

---

# 19. Error Handling

The monitoring process handles:

- HTTP errors
- Connection errors
- DNS errors
- Timeout errors
- Laravel HTTP client exceptions
- Unexpected exceptions

A failure for one website should not stop the remaining websites from being checked.

---

# 20. Useful Commands

## Laravel

```bash
composer install

php artisan key:generate

php artisan migrate

php artisan migrate --seed

php artisan serve

php artisan websites:monitor

php artisan schedule:run

php artisan schedule:work

php artisan test
```

## Vue

```bash
npm install

npm run dev

npm run build #* For production
```

## Future Queue Worker

```bash
php artisan queue:work
```

---