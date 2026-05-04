# Queue Jobs Setup - Kosmetik E-Commerce

## Overview

This application uses Laravel's queue system with the database driver to send emails asynchronously. All email notifications are dispatched as queue jobs to improve application performance and user experience.

## Queue Jobs

The following queue jobs are implemented:

### 1. SendEmailVerificationJob
- **Purpose**: Send email verification link after user registration
- **Requirement**: 1.1
- **Dispatched**: 
  - After user registration (`RegisteredUserController`)
  - When user requests resend verification (`EmailVerificationNotificationController`)
- **Retry**: 3 attempts with 60-second backoff

### 2. SendOrderConfirmationJob
- **Purpose**: Send order confirmation email with order summary and payment instructions
- **Requirement**: 4.8
- **Dispatched**: After order is created (`OrderService::createOrder()`)
- **Retry**: 3 attempts with 60-second backoff

### 3. SendPaymentConfirmationJob
- **Purpose**: Send payment confirmation email with shipping estimate
- **Requirement**: 5.8
- **Dispatched**: When payment is confirmed via Midtrans webhook (`MidtransWebhookController`)
- **Retry**: 3 attempts with 60-second backoff

### 4. SendOrderStatusUpdateJob
- **Purpose**: Send email notification when order status changes
- **Requirement**: 11.3
- **Dispatched**: 
  - When admin updates order status (`Admin\OrderController::updateStatus()`)
  - When admin adds tracking number (`Admin\OrderController::updateTracking()`)
- **Retry**: 3 attempts with 60-second backoff

## Configuration

### Queue Driver

The queue is configured to use the **database** driver. Configuration is in `.env`:

```env
QUEUE_CONNECTION=database
```

### Database Tables

The queue system uses the following tables:
- `jobs` - Stores pending jobs
- `failed_jobs` - Stores failed jobs for debugging

These tables are created by the migration:
```
database/migrations/0001_01_01_000002_create_jobs_table.php
```

## Running the Queue Worker

To process queued jobs, you need to run the queue worker:

### Development

```bash
php artisan queue:work
```

This will process jobs as they are added to the queue. The worker will continue running until manually stopped.

### Production

For production, use a process monitor like **Supervisor** to keep the queue worker running:

1. Install Supervisor:
```bash
sudo apt-get install supervisor
```

2. Create a configuration file `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/kosmetik-ecommerce/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/kosmetik-ecommerce/storage/logs/worker.log
stopwaitsecs=3600
```

3. Start Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Queue Worker Options

- `--sleep=3` - Sleep for 3 seconds when no jobs are available
- `--tries=3` - Attempt each job 3 times before marking as failed
- `--max-time=3600` - Restart worker after 1 hour (prevents memory leaks)
- `--queue=default` - Process jobs from specific queue (optional)

## Monitoring

### View Pending Jobs

```bash
php artisan queue:monitor
```

### View Failed Jobs

```bash
php artisan queue:failed
```

### Retry Failed Jobs

Retry a specific failed job:
```bash
php artisan queue:retry {id}
```

Retry all failed jobs:
```bash
php artisan queue:retry all
```

### Clear Failed Jobs

```bash
php artisan queue:flush
```

## Testing

Run the queue jobs tests:

```bash
php artisan test --filter=QueueJobsTest
```

The test suite covers:
- Job dispatch verification
- Email sending functionality
- Retry configuration
- Integration with controllers

## Email Configuration

Emails are sent using Laravel's Mail facade. Configure your mail driver in `.env`:

### Development (Log Driver)
```env
MAIL_MAILER=log
```
Emails will be written to `storage/logs/laravel.log`

### Production (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@kosmetik.example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Troubleshooting

### Jobs Not Processing

1. Check if queue worker is running:
```bash
ps aux | grep "queue:work"
```

2. Check for errors in logs:
```bash
tail -f storage/logs/laravel.log
```

3. Verify database connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Failed Jobs

If jobs are failing repeatedly:

1. Check the `failed_jobs` table for error messages
2. Review the job's `handle()` method for issues
3. Verify email configuration
4. Check network connectivity for external services

### Performance Issues

If the queue is processing slowly:

1. Increase the number of queue workers (in Supervisor config)
2. Use Redis instead of database driver for better performance
3. Optimize database queries in job handlers
4. Consider using job batching for bulk operations

## Best Practices

1. **Always use queues for emails** - Never send emails synchronously in controllers
2. **Monitor failed jobs** - Set up alerts for failed job notifications
3. **Keep jobs small** - Each job should do one thing well
4. **Use job chaining** - For complex workflows, chain multiple jobs
5. **Test thoroughly** - Always test job dispatch and execution
6. **Log appropriately** - Log important events but avoid excessive logging

## Additional Resources

- [Laravel Queue Documentation](https://laravel.com/docs/11.x/queues)
- [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - Advanced queue monitoring (Redis only)
- [Supervisor Documentation](http://supervisord.org/)
