<?php

namespace App\Aggregates;

use App\Contracts\Aggregates\UserAggregate;
use App\Aggregates\Manage\{
    ManageEmails,
    ManageLogins,
    ManageOrganizations,
    ManagePhones,
    ManageUsers
};
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class StandardUserAggregate extends AggregateRoot implements UserAggregate
{
    use ManageEmails;
    use ManageLogins;
    use ManageUsers;
    use ManageOrganizations;
    use ManagePhones;
}
