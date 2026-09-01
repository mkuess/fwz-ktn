---
name: User-area login audit
description: Scope and privacy rules for auditing user-area authentication attempts.
---

The login audit covers attempts to enter the public member area, including successful logins and failures such as incorrect credentials, pending approval, or an admin using the wrong login area. The management view is an immutable audit list.

**Why:** Administrators need to identify who accessed the member area and diagnose login problems, but authentication logs must not become a source of credential leakage or editable history.

**How to apply:** Record identity snapshots, outcome, understandable failure reason, timestamp, IP address, and user agent. Never record passwords, password hashes, activation tokens, session identifiers, or submitted credentials other than the login email. Extend the same audit model if another public user-area guard is introduced later.