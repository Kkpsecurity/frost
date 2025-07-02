<?php

namespace App\RCache;


trait RCacheTraitLoader
{

    use RCacheBoot;
    use RCacheRedis;
    use RCacheSerializer;

    use RCacheModels;
    use RCacheUsers;

    use RCacheQueries;

    use RCacheLocker;

}
