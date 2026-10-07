---
name: Organisation portal scope
description: Organisation account permissions and dashboard semantics requested by the user
---

Organisation accounts use their organisation-table credentials through the shared frontend login, but get their own administration rather than FWZ admin privileges. They may see only members who selected their organisation and edit only their own organisation details.

**Why:** The user requested organisation-specific menus and membership management; sharing full FWZ access would expose unrelated organisations and members.

**How to apply:** Keep account and permission separation when extending the portal. New registrations mean registrations since the previous successful organisation login, not merely members still awaiting approval. The first login includes all existing registrations.

Organisation blocking is a reversible member-login restriction, separate from FWZ approval.

**Why:** The user requested a manual block and an indication whether members can log in. Unblocking must not implicitly approve pending or rejected members.

**How to apply:** Enforce restrictions on existing member sessions as well as password login. Preserve approval status during both blocking and unblocking.
