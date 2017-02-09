<?php

namespace Microsoft\OData;

use Microsoft\Core\Enum;

class RequestHeader extends Enum
{
	const ACCEPT = 'Accept';

	const ODATA_VERSION = 'OData-Version';

	const ODATA_MAX_VERSION = 'OData-MaxVersion';

    const PREFER = 'Prefer';

    const IF_MATCH = 'If-Match';

    const IF_NONE_MATCH = 'If-None-Match';

    const ODATA_ISOLUTION = 'OData-Isolation';

    public function __toString()
    {
        return $this->value();
    }
}
