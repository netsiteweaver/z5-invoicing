## Notifications Runbook

Setup:
- Configure mailer in `.env` (MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS, MAIL_FROM_NAME).
- Run migrations and seeders:
  - php artisan migrate
  - php artisan db:seed --class=NotificationSeeder

Key pieces:
- Models: `NotificationTemplate`, `NotificationRule`, `NotificationRuleRecipient`, `NotificationLog`
- Services: `NotificationOrchestrator`, `RecipientResolver`, `EventMatcher`
- Observers: `UserObserver`, `ProductObserver` (registered in `AppServiceProvider`)
- Mail: `GenericNotificationMail` using `resources/views/emails/plain_generic.blade.php`

Adding a rule:
1. Create a `NotificationTemplate` for the channel (email).
2. Create a `NotificationRule` with `event_type` (e.g., `product.updated`) and link the template.
3. Add `NotificationRuleRecipient` rows for `user`, `role`, or `special: actor`.

Events covered out of the box:
- user.created, user.updated, user.deleted
- product.created, product.updated, product.deleted

Operational notes:
- Idempotency prevents duplicate sends for the same event+rule+recipient.
- Logs are stored in `notification_logs` with status and error details.
- To disable a rule, set `is_active = false` on `notification_rules`.

