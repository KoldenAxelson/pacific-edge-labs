@extends('emails.layouts.default')

@section('content')
    <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $userName }}!</h2>
    
    <p>This is a test email from Pacific Edge Labs to verify that our email system is working correctly.</p>
    
    <p>If you're seeing this email in your MailTrap inbox, congratulations! The email abstraction layer is set up and functioning properly.</p>
    
    <div style="background: #f3f4f6; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">Email System Status: ✓ Working</p>
        <p style="margin: 10px 0 0 0; color: #6b7280;">Your email configuration is ready for development.</p>
    </div>
    
    <p>This email abstraction layer allows you to easily switch between email providers without changing any application code.</p>
    
    <a href="https://pacificedgelabs.test/dashboard" class="button">Visit Dashboard</a>
    
    <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
        <strong>Note:</strong> This is a test email sent from your local development environment. 
        In production, emails will be sent through your configured email service provider.
    </p>
@endsection
