<?php
class ModelModuleCSVPrice extends Model {
	private $CSV_SEPARATOR = ';';
	private $CSV_ENCLOSURE = '"';
	private $data = array();

	public function import($fn) {

		if (($handle = fopen($fn, "r")) !== FALSE) {
			$row = 0;

		    while (($data = fgetcsv($handle, 1000, $this->CSV_SEPARATOR, $this->CSV_ENCLOSURE)) !== FALSE) {
				$num = count($data);
				$row++;
				$item = array();

				for ($c=0; $c < $num; $c++) {
					$item[] = $data[$c];
				}

				// Update Price
				if( count($item) == 6 ) {
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[4].'", price = '.$item[5].' WHERE product_id = '.(int)$item[0];
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[4].'", price = '.$item[5].' WHERE product_id = '.(int)$item[0]);
				} elseif ( count($item) == 3 ){
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[1].'", price = '.$item[2].' WHERE model = "'.$item[0].'"';
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[1].'", price = '.$item[2].' WHERE model = "'.iconv('cp1251', 'UTF-8', $item[0]).'"');
				}elseif ( count($item) == 2 ){
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET price = '.$item[1].' WHERE model = "'.$item[0].'"';
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET price = '.$item[1].' WHERE model = "'.iconv('cp1251', 'UTF-8', $item[0]).'"');
				}

				unset($item);
			}
		    fclose($handle);
		}
		$this->cache->delete('product');
	}

	public function export($product_category) {
		$output = '';
		$search = array(';',"\n");

		if($product_category) {
			$where = ' AND (';
			foreach ($product_category as $category) {
				$where .= " p2c.category_id = '".$category."' OR ";
			}
			$where .= " p2c.category_id = '".$category."')";
			$sql = "SELECT DISTINCT p.product_id, p.model, p.quantity, p.price, pd.name, m.name AS manufacturer FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id'). "'" . $where." ORDER BY pd.name";
		}else {
			$sql = "SELECT p.product_id, p.model, p.quantity, p.price, pd.name, m.name AS manufacturer FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name DESC" ;
		}
		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$output .= $result['product_id'] . ';' . str_replace($search, '', $result['name']) . ';' . str_replace($search, '', $result['model']) . ';' . $result['manufacturer'] . ';' . $result['quantity'] . ';' . $result['price'] . "\n";
		}
		return iconv('UTF-8', 'cp1251', $output);
		//return $output;
	}
}
?>
