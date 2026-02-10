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
