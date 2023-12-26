<?php
class ModelExtensionTotalHyperDiscount extends Model {
    public function getTotal($total) {
        if (isset($this->session->data['hyper_discount']) && is_array($this->session->data['hyper_discount'])) {
            foreach ($this->session->data['hyper_discount'] as $title => $value) {
                if ($value == 0) continue;
                
                $total['totals'][] = array(
                    'code' => 'hyper_discount',
                    'title' => $title,
                    'value' => '-'.$value,
                    'sort_order' => $this->config->get('total_hyper_discount_sort_order')
                );

                $total['total'] -= $value;
            }
        } else {

            $this->load->language('extension/total/sub_total');
            $value = 0;
            $products = $this->cart->getProducts();

            foreach ($products as $product) {
                $value += ($product['full_total'] - $product['total']);
            }

            if ($value == 0) {
                return;
            }

            $total['totals'][] = array(
                'code' => 'hyper_discount',
                'title' => $this->language->get('text_hyper_discount_total'),
                'value' => '-'.$value,
                'sort_order' => $this->config->get('total_hyper_discount_sort_order')
            );

        }
    }
}
