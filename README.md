invite
======

[wip] Invite service API. Aimed to be used by AndroidReviews.
Simplest API ever. Not RESTful at all. I don't give a f*ck here.

API
===

#### Add Service

`POST /services`
- name = string
- password = string
- nb_invite = int (opt, default = 100)

return true (the new service has been added)/false (error)

#### Generate new invites for a service

#### Get 1 invite

`GET /invites`
- password
- service_name

return 1 string (the invite code)

#### Check 1 invite

`GET /invites/{invite_code}`
- service_name

return true (exist and still available)/false (doesnt exist or has already been used)

#### Use 1 invite

`PUT /invites/{invite_code}`
- service_name

return true (invite used)/false (invite could not be used: invalid or already used)

#### Cancel/Remove invite

`DELETE /invites/{invite_code}`
- password
- service_name

return true (has been deleted)/false (has not been deleted)

#### Remove all invites

`DELETE /invites`
- password
- service_name

#### Remove service

`DELETE /services/{name}`
- password
