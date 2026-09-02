---
name: Organisation approval states
description: Compatibility rule for organisation review status and public visibility.
---

Organisation review uses three states: pending, approved, and rejected. A rejection requires a reason. Rejected organisations must not be treated as pending.

**Why:** Public visibility and older application code rely on the legacy approval boolean, while administration needs to distinguish organisations still awaiting review from those explicitly rejected.

**How to apply:** Keep the review status authoritative, synchronize the approval boolean and approval timestamp whenever it changes, and scope pending admin queues explicitly to the pending status.