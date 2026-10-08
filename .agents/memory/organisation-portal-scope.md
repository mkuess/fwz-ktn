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

CSV roster comparison is a read-only membership review, not an import or automatic access decision. Compare all registrations of the current organisation, not just new registrations. Organisations can use differently named and ordered CSV columns.

**Why:** The user wants to check whether registered users really belong to their association. Missing or differing emails and names are not sufficient evidence to revoke access.

**How to apply:** Allow explicit column mapping for email, first name and last name. Treat name-only or conflicting matches as manual-review cases. Never automatically change approval or login blocking from a CSV comparison.

FWZ admins should have the same read-only CSV comparison, with a searchable association selector. Organisation accounts must still be restricted to their own organisation.

**Why:** The user requested an admin comparison under “Verwaltung” with autocomplete selection of the association to check.

**How to apply:** Keep comparison behaviour identical across both areas. Changing the selected association must discard the old comparison so results cannot be mistaken for those of another association.
