## Notifications ERD (normalized)

Entities:

- notification_templates
  - id (bigint, PK)
  - uuid (uuid, unique)
  - channel (string: 'email')
  - name (string, unique)
  - subject (string, nullable)
  - body (longText or text)
  - variables (json, nullable) // list or schema hints
  - is_active (boolean, default true)
  - created_by (nullable FK users.id)
  - updated_by (nullable FK users.id)
  - timestamps

- notification_rules
  - id (bigint, PK)
  - uuid (uuid, unique)
  - name (string)
  - event_type (string) // e.g. 'user.created', 'product.*'
  - channel (string: 'email')
  - template_id (FK notification_templates.id)
  - subject_override (string, nullable)
  - is_active (boolean, default true)
  - is_system (boolean, default false)
  - tenant_id (nullable bigint) // if multi-tenant
  - created_by (nullable FK users.id)
  - updated_by (nullable FK users.id)
  - timestamps
  - indexes: (event_type, is_active), (tenant_id)

- notification_rule_recipients
  - id (bigint, PK)
  - rule_id (FK notification_rules.id)
  - recipient_type (enum: 'user', 'role', 'group', 'special')
  - recipient_value (string) // user_id, role_id, group_id, or 'actor'
  - priority (integer, default 0)
  - is_active (boolean, default true)
  - timestamps
  - unique composite: (rule_id, recipient_type, recipient_value)

- notification_logs
  - id (bigint, PK)
  - uuid (uuid, unique)
  - event_id (uuid) // idempotency key from domain event
  - rule_id (nullable FK notification_rules.id)
  - recipient_user_id (nullable FK users.id)
  - channel (string)
  - subject (string, nullable)
  - body (longText or text, nullable)
  - payload (json, nullable) // event payload snapshot
  - status (string enum: 'queued','sent','failed','skipped','retrying')
  - error (text, nullable)
  - sent_at (timestamp, nullable)
  - attempt (integer, default 0)
  - tenant_id (nullable bigint)
  - timestamps
  - indexes: (event_id), (rule_id), (recipient_user_id), (status)

Notes:
- Use wildcard matching for event_type ('product.*'). Consider pre-expansion cache.
- Enforce actor receipts at orchestration layer if needed, independent of rules.
- For groups, integrate when/if a groups table exists; until then, allow 'role' and 'user' plus 'special:actor'.
- JSON columns use MySQL JSON or Postgres JSONB depending on database.

