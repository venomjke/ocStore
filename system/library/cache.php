<?php
final class Cache {
	private $memcache;
	private $ismemcache = false;
	private $expire = 3600;

	public function get($key) {
	    if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache)
	    {
		return($this->memcache->get(MEMCACHE_NAMESPACE . $key, 0));
	    }
	    else
	    {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
    		foreach ($files as $file) {
      			$cache = '';

				$handle = fopen($file, 'r');

				if ($handle) {
					$cache = fread($handle, filesize($file));

					fclose($handle);
				}

	      		return unserialize($cache);
   		 	}
		}
	    }
  	}

  	public function set($key, $value) {
	    if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache)
	    {
		$this->memcache->set(MEMCACHE_NAMESPACE . $key, $value, 0, $this->expire);
	    }
	    else
	    {

    		    $this->delete($key);

		    $file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);

		    $handle = fopen($file, 'w');

    		    fwrite($handle, serialize($value));

    		    fclose($handle);
    	    };
  	}

  	public function delete($key) {
	    if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache)
	    {
		$this->memcache->delete(MEMCACHE_NAMESPACE . $key);
	    }
	    else
	    {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {
					@unlink($file);
					clearstatcache();
				}
    		}
		}
	    }
  	}
}
?>
