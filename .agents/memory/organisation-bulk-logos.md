---
name: Organisation bulk logos
description: Safe storage behavior when assigning one uploaded logo to multiple organisations.
---

A logo assigned in bulk must be copied to a separate stored file for every selected organisation, even though the visible image is identical.

**Why:** Sharing one physical file path means removing or replacing the logo while editing one organisation could delete the image used by every other organisation in that bulk assignment.

**How to apply:** Store the bulk upload on the public disk, create one uniquely named copy per organisation, update each organisation to its own path, and remove the now-unused source upload only after all copies exist.