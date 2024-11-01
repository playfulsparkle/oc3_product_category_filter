<?php
class ModelExtensionModulePsProductCategoryFilter extends Model
{
    public function cleanupCategoryFilters($category_id)
    {
        $query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int) $category_id . "'");

        if ($query->rows) {
            $removeFilters = array();

            foreach ($query->rows as $result) {
                $sql = "SELECT COUNT(DISTINCT p.product_id) AS total" .
                    " FROM " . DB_PREFIX . "product_to_category p2c" .
                    " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id AND pf.filter_id = '" . $result['filter_id'] . "') " .
                    " LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pf.product_id)" .
                    " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "') " .
                    " WHERE p2c.category_id = '" . (int) $category_id . "'";

                $query = $this->db->query($sql);

                if ($query->rows && $query->row['total'] == 0) {
                    $removeFilters[] = $result['filter_id'];
                }
            }

            if ($removeFilters) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int) $category_id . "' AND filter_id IN (" . implode(", ", $removeFilters) . ")");
            }
        }
    }
}
