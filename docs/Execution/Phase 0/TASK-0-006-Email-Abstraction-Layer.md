# [TASK-0-006] Email Abstraction Layer Setup

## Overview
Create an email abstraction layer that allows easy switching between email providers. Configure MailTrap for development and create interfaces that support future production email service integration.

## Prerequisites
- [x] TASK-0-001 completed (Laravel project initialized)
- [x] MailTrap account created (free tier)

## Goals
- Create email service interface for abstraction
- Configure MailTrap for development
- Create example notification emails (order confirmation, CoA ready, etc.)
- Test email sending and viewing in MailTrap
- Document email architecture for future production swap

## Step-by-Step Instructions

### 1. Create MailTrap Account

1. Go to https://mailtrap.io/
2. Sign up for free account
3. Navigate to "Email Testing" → "Inboxes"
4. Create a new inbox called "Pacific Edge Labs Dev"
5. Click on the inbox and go to "SMTP Settings"
6. Copy the credentials (we'll use them next)

### 2. Configure MailTrap in Environment

Edit `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pacificedgelabs.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Replace `your_mailtrap_username` and `your_mailtrap_password` with actual MailTrap credentials.

### 3. Update `.env.example`

Edit `.env.example`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pacificedgelabs.test"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Create Email Service Interface

```bash
mkdir -p app/Contracts
```

Create `app/Contracts/EmailServiceInterface.php`:

```php
<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Order;
use Illuminate\Mail\Mailable;

interface EmailServiceInterface
{
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): bool;

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation(Order $order): bool;

    /**
     * Send CoA ready notification
     */
    public function sendCoaReadyNotification(User $user, string $productName, string $coaUrl): bool;

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(User $user, string $token): bool;

    /**
     * Send generic mailable
     */
    public function send(Mailable $mailable, User $user): bool;
}
```

### 5. Create Email Service Implementation

Create `app/Services/EmailService.php`:

```php
<?php

namespace App\Services;

use App\Contracts\EmailServiceInterface;
use App\Models\User;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService implements EmailServiceInterface
{
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            // Will implement actual mailable in Phase 5
            Log::info("Welcome email would be sent to: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            // Will implement in Phase 4
            Log::info("Order confirmation would be sent to: {$order->user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send CoA ready notification
     */
    public function sendCoaReadyNotification(User $user, string $productName, string $coaUrl): bool
    {
        try {
            // Will implement in Phase 3
            Log::info("CoA ready notification would be sent to: {$user->email}");
            Log::info("Product: {$productName}, URL: {$coaUrl}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send CoA notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(User $user, string $token): bool
    {
        try {
            // Laravel handles this via Notifications
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send password reset: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send generic mailable
     */
    public function send(Mailable $mailable, User $user): bool
    {
        try {
            Mail::to($user->email)->send($mailable);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: {$e->getMessage()}");
            return false;
        }
    }
}
```

### 6. Register Email Service in Service Provider

Edit `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\EmailServiceInterface;
use App\Services\EmailService;
use App\Services\StorageService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Email service binding
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);
        
        // Storage service binding (from TASK-0-004)
        $this->app->singleton(StorageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

### 7. Create Test Mailable

```bash
sail artisan make:mail TestEmail
```

Edit `app/Mail/TestEmail.php`:

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email from Pacific Edge Labs',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
```

### 8. Create Email View Template

```bash
mkdir -p resources/views/emails
```

Create `resources/views/emails/test.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Pacific Edge Labs</h1>
        <p style="margin: 10px 0 0 0;">Premium Research Peptides</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $userName }}!</h2>
        
        <p>This is a test email from Pacific Edge Labs. If you're seeing this in MailTrap, it means our email system is working correctly!</p>
        
        <p>This email template demonstrates:</p>
        <ul>
            <li>Branded header with Pacific Edge Labs styling</li>
            <li>Clean, professional design</li>
            <li>Responsive layout</li>
            <li>Dynamic content (your name: {{ $userName }})</li>
        </ul>
        
        <a href="https://pacificedgelabs.test" class="button">Visit Our Store</a>
        
        <p>This is the email abstraction layer in action. We're currently using MailTrap for development, but the same code will work with any email provider in production.</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Pacific Edge Labs. All rights reserved.</p>
        <p>This email was sent from our development environment.</p>
    </div>
</body>
</html>
```

### 9. Create Test Route

Edit `routes/web.php` and add:

```php
use App\Mail\TestEmail;
use App\Contracts\EmailServiceInterface;

// Email test route (REMOVE IN PRODUCTION)
Route::get('/test-email', function (EmailServiceInterface $emailService) {
    $user = auth()->user() ?? \App\Models\User::first();
    
    if (!$user) {
        return 'No users found. Create a user first.';
    }
    
    $mailable = new TestEmail($user->name);
    $result = $emailService->send($mailable, $user);
    
    return $result 
        ? 'Email sent successfully! Check MailTrap inbox.' 
        : 'Email failed to send. Check logs.';
})->name('test-email');
```

### 10. Test Email Sending

**Make sure you're logged in or have at least one user in the database.**

Visit: http://localhost/test-email

You should see: "Email sent successfully! Check MailTrap inbox."

### 11. Verify Email in MailTrap

1. Go to https://mailtrap.io/
2. Open your "Pacific Edge Labs Dev" inbox
3. You should see the test email
4. Open it and verify the styling and content

### 12. Create Email Layout Component

For future emails, create a reusable layout.

Create `resources/views/emails/layouts/default.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Pacific Edge Labs' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f3f4f6;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3b82f6;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background: #2563eb;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Pacific Edge Labs</h1>
            <p>Premium Research Peptides</p>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Pacific Edge Labs. All rights reserved.</p>
            <p>
                <a href="https://pacificedgelabs.test">Visit our website</a> | 
                <a href="https://pacificedgelabs.test/contact">Contact Support</a>
            </p>
            @if(isset($unsubscribeUrl))
                <p><a href="{{ $unsubscribeUrl }}">Unsubscribe from emails</a></p>
            @endif
        </div>
    </div>
</body>
</html>
```

### 13. Document Email Architecture

Create `docs/email-architecture.md`:

```markdown
# Email Architecture - Pacific Edge Labs

## Overview
Email system is abstracted to allow easy switching between providers without changing application code.

## Current Setup (Development)
- **Provider:** MailTrap
- **Purpose:** Email testing and development
- **Cost:** Free (up to 500 emails/month)
- **URL:** https://mailtrap.io/

## Architecture Pattern

### Interface: `App\Contracts\EmailServiceInterface`
Defines all email methods the application needs.

### Implementation: `App\Services\EmailService`
Current implementation using Laravel's built-in Mail facade + MailTrap SMTP.

### Service Binding
Registered in `AppServiceProvider`:
```php
$this->app->singleton(EmailServiceInterface::class, EmailService::class);
```

## Email Types

### Transactional (Required)
1. **Welcome Email** - Sent on registration
2. **Order Confirmation** - Sent when order placed
3. **CoA Ready** - Sent when Certificate of Analysis is uploaded
4. **Password Reset** - Sent via Laravel's built-in system
5. **Order Shipped** - Sent when order ships
6. **Support Ticket** - Sent when customer contacts support

### Marketing (Future - Phase 8+)
1. **New Product Announcements**
2. **Sales/Promotions**
3. **Newsletter**

## Switching Email Providers (Production)

### Option 1: AWS SES (Recommended)
**Cost:** $0.10 per 1,000 emails  
**Setup:**
1. Install AWS SES package: `composer require aws/aws-sdk-php`
2. Update `.env`:
   ```env
   MAIL_MAILER=ses
   AWS_ACCESS_KEY_ID=your_key
   AWS_SECRET_ACCESS_KEY=your_secret
   AWS_DEFAULT_REGION=us-west-2
   ```
3. No code changes needed!

### Option 2: SendGrid
**Cost:** $14.95/month (40,000 emails)  
**Setup:**
1. Install SendGrid package: `composer require sendgrid/sendgrid`
2. Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.sendgrid.net
   MAIL_PORT=587
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=your_sendgrid_api_key
   ```

### Option 3: Postmark
**Cost:** $15/month (10,000 emails)  
**Setup:**
1. Install Postmark package: `composer require wildbit/swiftmailer-postmark`
2. Update `.env` with Postmark credentials

## Email Templates

### Location
`resources/views/emails/`

### Layout
All emails extend `emails.layouts.default` for consistent branding.

### Styling
- Inline styles (email clients don't support external CSS)
- Mobile-responsive
- Pacific Edge Labs brand colors (blue gradient)

## Testing

### Local Testing
Use MailTrap to view emails without sending them to real users.

### Production Testing
Create a staging inbox in production email provider for pre-deployment testing.

## Compliance Notes

### Unsubscribe Links
All marketing emails MUST include unsubscribe links (CAN-SPAM Act).

### Data Protection
Store email logs in `compliance_logs` table for audit trail.

### Opt-Out Management
Track user email preferences in `user_preferences` table (Phase 5).

## Queue Configuration

For production, emails should be queued:
```php
Mail::to($user)->queue(new OrderConfirmation($order));
```

Queue worker will be configured in Phase 7.

## Rate Limiting

### Development (MailTrap)
- 500 emails/month
- 2 emails/second

### Production (AWS SES)
- 200 emails/day (sandbox)
- 50,000 emails/day (production - after verification)
- 14 emails/second max

## Monitoring

### MailTrap (Development)
- View all sent emails in web interface
- Check spam scores
- Validate HTML/CSS

### Production
- AWS SES: CloudWatch metrics
- SendGrid: Built-in analytics dashboard
- Postmark: Message events API

## Future Enhancements (Phase 8+)

1. **Email Templates in Database**
   - Admins can edit email templates via Filament
   - Version control for email content

2. **A/B Testing**
   - Test different subject lines and content
   - Track open rates and click-through rates

3. **Personalization**
   - Product recommendations based on order history
   - Birthday/anniversary emails

4. **Email Sequences**
   - Abandoned cart recovery (3-email sequence)
   - Post-purchase follow-up
   - Re-engagement campaigns
```

### 14. Commit Changes

```bash
git add .
git commit -m "Set up email abstraction layer with MailTrap integration"
git push
```

## Validation Checklist

- [ ] MailTrap account created and configured
- [ ] `.env` has correct MailTrap credentials
- [ ] Test email sends successfully
- [ ] Email appears in MailTrap inbox
- [ ] Email styling renders correctly
- [ ] Email service interface created
- [ ] Email service implementation created
- [ ] Service bound in AppServiceProvider
- [ ] Email layout component created
- [ ] Documentation created (email-architecture.md)

## Common Issues & Solutions

### Issue: "Connection could not be established with host"
**Solution:**
Verify MailTrap credentials in `.env`:
```bash
cat .env | grep MAIL_
```

### Issue: Email not appearing in MailTrap
**Solution:**
Check Laravel logs:
```bash
sail artisan tail
```

### Issue: "Swift_TransportException: Expected response code 250"
**Solution:**
Verify MAIL_PORT and MAIL_ENCRYPTION:
```env
MAIL_PORT=2525
MAIL_ENCRYPTION=tls
```

### Issue: Styling not rendering in email
**Solution:**
Use inline styles only. Email clients don't support external CSS or `<style>` tags well.

## Email Best Practices

### Subject Lines
- Keep under 50 characters
- Avoid spam trigger words (FREE, URGENT, ACT NOW)
- Personalize when possible: "Your order #1234 has shipped"

### Content
- Clear call-to-action
- Mobile-friendly (50%+ of emails opened on mobile)
- Plain text alternative (some users disable HTML)
- Unsubscribe link (legal requirement for marketing emails)

### Deliverability
- Verify sender domain (SPF, DKIM records)
- Monitor bounce rates
- Clean email list regularly
- Don't buy email lists

### Testing
- Test in multiple email clients (Gmail, Outlook, Apple Mail)
- Use MailTrap's spam score analysis
- Test on mobile devices
- Verify all links work

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-006 as complete
- ➡️ Proceed to TASK-0-007 (Payment Abstraction Layer)

## Time Estimate
**30-45 minutes**

## Success Criteria
- Email abstraction layer created and working
- MailTrap configured for development
- Test email sends and displays correctly
- Email templates created with Pacific Edge Labs branding
- Documentation complete
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001  
**Blocks:** None (emails are used throughout app but not blocking)  
**Priority:** Medium
