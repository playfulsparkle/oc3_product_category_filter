<?php
class ModelExtensionModulePsProductCategoryFilter extends Model
{
    public function cleanupCategoryFilters($category_id)
    {
        $category_filter_query = $this->db->query("SELECT `filter_id` FROM `" . DB_PREFIX . "category_filter` WHERE `category_id` = '" . (int) $category_id . "'");

        $category_filter_ids = array_column($category_filter_query->rows, 'filter_id');

        if ($category_filter_ids) {
            $product_filter_query = $this->db->query("SELECT DISTINCT pf.filter_id FROM `oc_product_to_category` p2c
                INNER JOIN oc_product_filter pf ON pf.product_id = p2c.product_id AND pf.filter_id IN (" . implode(", ", $category_filter_ids) . ")
                WHERE p2c.category_id = '" . (int) $category_id . "'");

            $product_filter_ids = array_column($product_filter_query->rows, 'filter_id');

            $delete_filter_id = array_diff($category_filter_ids, $product_filter_ids);

            if ($delete_filter_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int) $category_id . "' AND filter_id IN (" . implode(", ", $delete_filter_id) . ")");
            }
        }
    }
}
