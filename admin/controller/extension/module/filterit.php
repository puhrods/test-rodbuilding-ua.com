<?php
/*
 * WTFPL https://ucrack.com
 */
class ControllerModuleFilterit extends Controller
{
    private $wtfpl_lord_lanky_hack = "2.7.5";

    private function wtfpl_cargo_lush_scar()
    {
        $this->wtfpl_hurry_nutty_show($this->wtfpl_range_blond_scan());
    }

    private function wtfpl_chase_near_align()
    {
        $this->load->model('localisation/currency');
        $this->wtfpl_hurry_nutty_show($this->model_localisation_currency->getCurrencies());
    }

    private function wtfpl_clan_paved_seat()
    {
        $wtfpl_sheep_mint_title = [];
        $this->load->model('localisation/weight_class');
        $wtfpl_sheep_mint_title['weight'] = $this->model_localisation_weight_class->getWeightClass($this->config->get('config_weight_class_id'));
        $this->load->model('localisation/length_class');
        $wtfpl_sheep_mint_title['length'] = $this->model_localisation_length_class->getLengthClass($this->config->get('config_length_class_id'));
        $wtfpl_sheep_mint_title['datetime'] = date('d.m.Y H:i');
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_cloak_sober_freak()
    {
        $this->load->model('localisation/order_status');
        $this->wtfpl_hurry_nutty_show($this->model_localisation_order_status->getOrderStatuses());
    }

    private function wtfpl_dream_silky_keep()
    {
        $wtfpl_fault_sober_flap = 'module/filterit';
        if ($this->wtfpl_raid_extra_spout() < 230) {
            $wtfpl_fault_sober_flap = 'module/filterit';
        } else {
            $wtfpl_fault_sober_flap = 'extension/module/filterit';
        }
        if (!$this->user->hasPermission('modify', $wtfpl_fault_sober_flap)) {
            return false;
        }
        return true;
    }

    private function wtfpl_firm_vague_gripe($wtfpl_share_woody_lobby)
    {
        $wtfpl_share_woody_lobby = str_replace('www.', "", str_replace('http://', "", str_replace('https://', "", $wtfpl_share_woody_lobby)));
        $wtfpl_dread_burnt_imply = new Puny();
        $wtfpl_cube_moist_found = $wtfpl_dread_burnt_imply->strpos($wtfpl_share_woody_lobby, '/');
        if ($wtfpl_cube_moist_found) {
            $wtfpl_share_woody_lobby = $wtfpl_dread_burnt_imply->substr($wtfpl_share_woody_lobby, 0, $wtfpl_cube_moist_found);
        }
        if ($wtfpl_dread_burnt_imply->strpos($wtfpl_share_woody_lobby, ':') !== false) {
            $wtfpl_sperm_irish_grunt = explode(':', $wtfpl_share_woody_lobby);
            if (!empty($wtfpl_sperm_irish_grunt) && is_array($wtfpl_sperm_irish_grunt) && count($wtfpl_sperm_irish_grunt) == 2 && preg_match('/^[0-9]+$/usi', $wtfpl_sperm_irish_grunt[1])) {
                $wtfpl_share_woody_lobby = $wtfpl_sperm_irish_grunt[0];
            }
        }
        return strtolower($wtfpl_dread_burnt_imply->getPunycode(trim(trim($wtfpl_share_woody_lobby, '/'))));
    }

    private function wtfpl_flake_tidy_drum()
    {
        $this->document->setTitle('Filterit ' . $this->wtfpl_lord_lanky_hack);
        $wtfpl_court_prone_heed = [];
        if ($this->wtfpl_raid_extra_spout() < 300) {
            $wtfpl_court_prone_heed['token'] = $this->session->data['token'];
        } else {
            $wtfpl_court_prone_heed['token'] = $this->session->data['user_token'];
        }
        $wtfpl_court_prone_heed['stoken'] = md5($wtfpl_court_prone_heed['token']);
        $this->cache->set('stoken', $wtfpl_court_prone_heed['stoken']);
        $this->session->data['stoken'] = $wtfpl_court_prone_heed['stoken'];
        $wtfpl_court_prone_heed['simple'] = file_exists(constant('DIR_SYSTEM') . 'library/simple/simple.php');
        $wtfpl_court_prone_heed['version'] = $this->wtfpl_lord_lanky_hack;
        $wtfpl_court_prone_heed['version_hash'] = md5('filterit' . $this->wtfpl_joint_focal_tape('config_http') . strrev($this->wtfpl_joint_focal_tape('config_http')));
        $wtfpl_court_prone_heed['opencart_version'] = $this->wtfpl_raid_extra_spout();
        $wtfpl_width_waxed_bask = $this->wtfpl_tree_tense_kayak(constant('HTTP_SERVER'));
        $wtfpl_play_outer_iron = $this->wtfpl_tree_tense_kayak(constant('HTTP_CATALOG'));
        if ($this->wtfpl_raid_extra_spout() < 230) {
            $wtfpl_court_prone_heed['exit_url'] = $wtfpl_width_waxed_bask . 'index.php?route=extension/module&token=' . $this->session->data['token'];
            $wtfpl_court_prone_heed['admin_api'] = $wtfpl_width_waxed_bask . 'index.php?route=module/filterit';
            $wtfpl_court_prone_heed['catalog_api'] = $wtfpl_play_outer_iron . 'index.php?route=module/filterit';
            $this->wtfpl_guilt_whole_peel('module/filterit', $wtfpl_court_prone_heed);
        } else {
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $wtfpl_court_prone_heed['exit_url'] = $wtfpl_width_waxed_bask . 'index.php?route=extension/extension&token=' . $this->session->data['token'] . '&type=module';
                $wtfpl_court_prone_heed['admin_api'] = $wtfpl_width_waxed_bask . 'index.php?route=extension/module/filterit';
                $wtfpl_court_prone_heed['catalog_api'] = $wtfpl_play_outer_iron . 'index.php?route=extension/module/filterit';
                $this->wtfpl_guilt_whole_peel('extension/module/filterit', $wtfpl_court_prone_heed);
            } else {
                $wtfpl_court_prone_heed['exit_url'] = $wtfpl_width_waxed_bask . 'index.php?route=marketplace/extension&user_token=' . $this->session->data['user_token'] . '&type=module';
                $wtfpl_court_prone_heed['admin_api'] = $wtfpl_width_waxed_bask . 'index.php?route=extension/module/filterit';
                $wtfpl_court_prone_heed['catalog_api'] = $wtfpl_play_outer_iron . 'index.php?route=extension/module/filterit';
                $this->wtfpl_guilt_whole_peel('extension/module/filterit', $wtfpl_court_prone_heed);
            }
        }
    }

    private function wtfpl_guilt_whole_peel($wtfpl_oath_nude_mill, $wtfpl_court_prone_heed)
    {
        if ($this->wtfpl_raid_extra_spout() < 200) {
            $this->data = array_merge(isset($this->data) && is_array($this->data) ? $this->data : [], $wtfpl_court_prone_heed);
            $this->data['column_left'] = "";
            $this->template = $wtfpl_oath_nude_mill . '.tpl';
            $this->children = ['common/header', 'common/footer'];
            $this->response->setOutput($this->render());
        } else {
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $wtfpl_court_prone_heed['header'] = $this->load->controller('common/header');
                $wtfpl_court_prone_heed['column_left'] = $this->load->controller('common/column_left');
                $wtfpl_court_prone_heed['footer'] = $this->load->controller('common/footer');
                $this->response->setOutput($this->load->view($wtfpl_oath_nude_mill . '.tpl', $wtfpl_court_prone_heed));
            } else {
                $wtfpl_court_prone_heed['header'] = $this->load->controller('common/header');
                $wtfpl_court_prone_heed['column_left'] = $this->load->controller('common/column_left');
                $wtfpl_court_prone_heed['footer'] = $this->load->controller('common/footer');
                $this->config->set('template_engine', 'template');
                $this->response->setOutput($this->load->view($wtfpl_oath_nude_mill, $wtfpl_court_prone_heed));
            }
        }
    }

    private function wtfpl_halt_other_gear($wtfpl_lawn_foul_prove, $wtfpl_works_weary_stump)
    {
        $wtfpl_grade_lanky_hash = $this->db->query('SELECT * FROM ' . constant('DB_PREFIX') . 'setting WHERE `code` = \'filterit\' AND `store_id` = \'' . (int)$wtfpl_lawn_foul_prove . '\' AND `key` = \'module_filterit_status\'');
        if ($wtfpl_grade_lanky_hash->num_rows) {
            $this->db->query('UPDATE ' . constant('DB_PREFIX') . 'setting SET `value` = \'' . (int)$wtfpl_works_weary_stump . '\' WHERE `code` = \'filterit\' AND `store_id` = \'' . (int)$wtfpl_lawn_foul_prove . '\' AND `key` = \'module_filterit_status\'');
        } else {
            $this->db->query('INSERT INTO ' . constant('DB_PREFIX') . 'setting SET `value` = \'' . (int)$wtfpl_works_weary_stump . '\', `code` = \'filterit\', `store_id` = \'' . (int)$wtfpl_lawn_foul_prove . '\', `key` = \'module_filterit_status\'');
        }
    }

    private function wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($wtfpl_sheep_mint_title);
        exit;
    }

    private function wtfpl_joint_focal_tape($wtfpl_hunt_like_warp)
    {
        $wtfpl_share_woody_lobby = "";
        switch ($wtfpl_hunt_like_warp) {
            case 'config_http':
                $wtfpl_share_woody_lobby = defined('HTTP_CATALOG') ? constant('HTTP_CATALOG') : "";
                break;
            case 'config_https':
                $wtfpl_share_woody_lobby = defined('HTTP_CATALOG') ? constant('HTTP_CATALOG') : "";
                break;
            case 'server':
                $wtfpl_share_woody_lobby = isset($this->request->server['HTTP_HOST']) ? $this->request->server['HTTP_HOST'] : "";
                break;
        }
        return $this->wtfpl_firm_vague_gripe($wtfpl_share_woody_lobby);
    }

    private function wtfpl_lease_tiny_clash()
    {
        if ($this->wtfpl_raid_extra_spout() < 210) {
            $this->load->model('sale/customer_group');
            $wtfpl_sheep_mint_title = $this->model_sale_customer_group->getCustomerGroups();
        } else {
            $this->load->model('customer/customer_group');
            $wtfpl_sheep_mint_title = $this->model_customer_customer_group->getCustomerGroups();
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_noise_aged_vest()
    {
        $wtfpl_grade_lanky_hash = $this->db->query('SHOW COLUMNS FROM `' . constant('DB_PREFIX') . 'session`');
        $wtfpl_scale_airy_rinse = false;
        foreach ($wtfpl_grade_lanky_hash->rows as $wtfpl_show_soggy_trash) {
            if ($wtfpl_show_soggy_trash['Field'] == 'data' && $wtfpl_show_soggy_trash['Type'] == 'text') {
                $wtfpl_scale_airy_rinse = true;
            }
        }
        if ($wtfpl_scale_airy_rinse) {
            $this->db->query('ALTER TABLE `' . constant('DB_PREFIX') . 'session` CHANGE `data` `data` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        }
    }

    private function wtfpl_onset_away_dress()
    {
        $wtfpl_grade_lanky_hash = $this->db->query('SHOW COLUMNS FROM `' . constant('DB_PREFIX') . 'setting`');
        $wtfpl_scale_airy_rinse = false;
        foreach ($wtfpl_grade_lanky_hash->rows as $wtfpl_show_soggy_trash) {
            if ($wtfpl_show_soggy_trash['Field'] == 'value' && $wtfpl_show_soggy_trash['Type'] == 'text') {
                $wtfpl_scale_airy_rinse = true;
            }
        }
        if ($wtfpl_scale_airy_rinse) {
            $this->db->query('ALTER TABLE `' . constant('DB_PREFIX') . 'setting` CHANGE `value` `value` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        }
    }

    private function wtfpl_ozone_petty_thud()
    {
        $wtfpl_fruit_rural_last = $this->config->get('filterit_license');
        $wtfpl_spark_mute_seize = $this->wtfpl_joint_focal_tape('config_http');
        $wtfpl_urge_hasty_hone = strlen($wtfpl_spark_mute_seize);
        $wtfpl_park_lumpy_amble = defined('DB_DATABASE') ? constant('DB_DATABASE') : "";
        $wtfpl_bust_anglo_plot = defined('DIR_SYSTEM') ? realpath(constant('DIR_SYSTEM')) : 'catalog_system';
        return md5($wtfpl_spark_mute_seize . substr($wtfpl_fruit_rural_last, $wtfpl_urge_hasty_hone, $wtfpl_urge_hasty_hone) . $wtfpl_park_lumpy_amble . $wtfpl_bust_anglo_plot);
    }

    private function wtfpl_panic_novel_ripen()
    {
        $wtfpl_sheep_mint_title = [];
        if ($this->wtfpl_raid_extra_spout() < 200 || 300 <= $this->wtfpl_raid_extra_spout()) {
            $this->load->model('setting/extension');
            $wtfpl_verge_grand_steam = $this->model_setting_extension->getInstalled('payment');
        } else {
            $this->load->model('extension/extension');
            $wtfpl_verge_grand_steam = $this->model_extension_extension->getInstalled('payment');
        }
        foreach ($wtfpl_verge_grand_steam as $wtfpl_lease_frank_ferry) {
            if ($wtfpl_lease_frank_ferry == 'filterit') {
                continue;
            }
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $wtfpl_works_weary_stump = $this->config->get($wtfpl_lease_frank_ferry . '_status');
            } else {
                $wtfpl_works_weary_stump = $this->config->get('payment_' . $wtfpl_lease_frank_ferry . '_status');
            }
            if ($wtfpl_works_weary_stump) {
                if ($this->wtfpl_raid_extra_spout() < 230) {
                    $this->language->load('payment/' . $wtfpl_lease_frank_ferry);
                } else {
                    $this->language->load('extension/payment/' . $wtfpl_lease_frank_ferry);
                }
                $wtfpl_sheep_mint_title[] = [
                    'code' => $wtfpl_lease_frank_ferry,
                    'name' => strip_tags($this->language->get('heading_title'))
                ];
            }
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_plus_lunar_farm()
    {
        $wtfpl_sheep_mint_title = ['language' => $this->config->get('config_admin_language'), 'languages' => []];
        $wtfpl_doll_swiss_snipe = "";
        if (defined('OVERRIDE_LANGUAGE_CODE')) {
            $wtfpl_doll_swiss_snipe = constant('OVERRIDE_LANGUAGE_CODE');
        }
        $wtfpl_grade_lanky_hash = $this->db->query('SELECT * FROM `' . constant('DB_PREFIX') . 'language` WHERE status = \'1\'');
        foreach ($wtfpl_grade_lanky_hash->rows as $wtfpl_pilot_sharp_prowl) {
            if ($wtfpl_doll_swiss_snipe && $wtfpl_doll_swiss_snipe != $wtfpl_pilot_sharp_prowl['code']) {
                continue;
            }
            $wtfpl_view_solar_value = "";
            if ($this->wtfpl_raid_extra_spout() < 220) {
                $wtfpl_view_solar_value = 'view/image/flags/' . $wtfpl_pilot_sharp_prowl['image'];
            } else {
                $wtfpl_view_solar_value = 'language/' . $wtfpl_pilot_sharp_prowl['code'] . '/' . $wtfpl_pilot_sharp_prowl['code'] . '.png';
            }
            $wtfpl_sheep_mint_title['languages'][$wtfpl_pilot_sharp_prowl['code']] = [
                'image' => $wtfpl_view_solar_value,
                'name' => $wtfpl_pilot_sharp_prowl['name']
            ];
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_raid_extra_spout()
    {
        static $wtfpl_burn_fatty_rope = "";
        if (empty($wtfpl_burn_fatty_rope)) {
            $wtfpl_coop_bare_chain = explode('.', constant('VERSION'));
            $wtfpl_burn_fatty_rope = floatval($wtfpl_coop_bare_chain[0] . $wtfpl_coop_bare_chain[1] . $wtfpl_coop_bare_chain[2] . '.' . (isset($wtfpl_coop_bare_chain[3]) ? $wtfpl_coop_bare_chain[3] : 0));
        }
        return $wtfpl_burn_fatty_rope;
    }

    private function wtfpl_rally_sole_peer()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $wtfpl_fruit_rural_last = isset($this->request->post['license']) ? trim($this->request->post['license']) : "";
            if ($wtfpl_fruit_rural_last) {
                $wtfpl_guide_shaky_thank = $this->wtfpl_sole_toxic_wheel($wtfpl_fruit_rural_last);
                if ($wtfpl_guide_shaky_thank) {
                    $this->load->model('setting/setting');
                    $wtfpl_cage_token_fill = $this->wtfpl_range_blond_scan();
                    foreach ($wtfpl_cage_token_fill as $wtfpl_kind_famed_mount) {
                        $wtfpl_face_lousy_coat = $this->model_setting_setting->getSetting('filterit', $wtfpl_kind_famed_mount['store_id']);
                        $this->model_setting_setting->editSetting('filterit', [
                            'filterit_license' => $wtfpl_fruit_rural_last,
                            'filterit_domain' => $this->wtfpl_joint_focal_tape('config_http'),
                            'filterit_shipping' => isset($wtfpl_face_lousy_coat['filterit_shipping']) ? $wtfpl_face_lousy_coat['filterit_shipping'] : [],
                            'filterit_payment' => isset($wtfpl_face_lousy_coat['filterit_payment']) ? $wtfpl_face_lousy_coat['filterit_payment'] : [],
                            'filterit_total' => isset($wtfpl_face_lousy_coat['filterit_total']) ? $wtfpl_face_lousy_coat['filterit_total'] : [],
                            'filterit_sort_order' => isset($wtfpl_face_lousy_coat['filterit_sort_order']) ? $wtfpl_face_lousy_coat['filterit_sort_order'] : "",
                            'filterit_status' => isset($wtfpl_face_lousy_coat['filterit_status']) ? $wtfpl_face_lousy_coat['filterit_status'] : true
                        ], $wtfpl_kind_famed_mount['store_id']);
                    }
                    $this->wtfpl_hurry_nutty_show(['success' => true]);
                } else {
                    $this->wtfpl_hurry_nutty_show(['error' => true]);
                }
            }
        }
        $this->wtfpl_hurry_nutty_show([
            'domain' => $this->wtfpl_joint_focal_tape('config_http'),
            'verified' => $this->wtfpl_sole_toxic_wheel($this->config->get('filterit_license'))
        ]);
    }

    private function wtfpl_range_blond_scan()
    {
        $this->load->model('setting/store');
        $wtfpl_cage_token_fill[] = ['store_id' => 0, 'name' => $this->config->get('config_name')];
        $wtfpl_cage_token_fill = array_merge($wtfpl_cage_token_fill, $this->model_setting_store->getStores());
        return $wtfpl_cage_token_fill;
    }

    private function wtfpl_shirt_rusty_smart()
    {
        $wtfpl_sheep_mint_title = ['success' => true];
        if ($this->wtfpl_raid_extra_spout() < 200) {
            $wtfpl_spin_okay_seek = str_replace('/system/', '/vqmod/', constant('DIR_SYSTEM'));
            if (file_exists($wtfpl_spin_okay_seek . 'xml/filterit_opencart_1.5.x.xml') && file_exists($wtfpl_spin_okay_seek . 'vqcache/')) {
                $wtfpl_sheep_mint_title['success'] = true;
            } else {
                $wtfpl_sheep_mint_title['success'] = false;
            }
        } else {
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $this->load->model('extension/modification');
                $wtfpl_crow_damn_pitch = $this->model_extension_modification->getModificationByCode('Filterit');
                if (empty($wtfpl_crow_damn_pitch)) {
                    $wtfpl_sheep_mint_title['success'] = false;
                }
            } else {
                $this->load->model('setting/modification');
                $wtfpl_crow_damn_pitch = $this->model_setting_modification->getModificationByCode('Filterit');
                if (empty($wtfpl_crow_damn_pitch)) {
                    $wtfpl_sheep_mint_title['success'] = false;
                }
            }
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_smile_whole_gasp()
    {
        if ($this->wtfpl_raid_extra_spout() < 300) {
            $wtfpl_stint_local_halt = $this->session->data['token'];
        } else {
            $wtfpl_stint_local_halt = $this->session->data['user_token'];
        }
        $wtfpl_stint_local_halt = md5($wtfpl_stint_local_halt);
        $this->cache->set('stoken', $wtfpl_stint_local_halt);
        $this->session->data['stoken'] = $wtfpl_stint_local_halt;
        $this->wtfpl_hurry_nutty_show(['stoken' => $wtfpl_stint_local_halt]);
    }

    private function wtfpl_sole_toxic_wheel($wtfpl_coup_ample_yield)
    {
        return true;
    }

    private function wtfpl_stain_greek_snare()
    {
        $wtfpl_drum_upset_amass = [];
        if ($this->wtfpl_raid_extra_spout() < 200 || 300 <= $this->wtfpl_raid_extra_spout()) {
            $this->load->model('setting/extension');
            $wtfpl_verge_grand_steam = $this->model_setting_extension->getInstalled('total');
        } else {
            $this->load->model('extension/extension');
            $wtfpl_verge_grand_steam = $this->model_extension_extension->getInstalled('total');
        }
        foreach ($wtfpl_verge_grand_steam as $wtfpl_lease_frank_ferry) {
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $wtfpl_tick_tiny_bulge = $this->config->get($wtfpl_lease_frank_ferry . '_status');
                $wtfpl_fish_naive_speak = $this->config->get($wtfpl_lease_frank_ferry . '_sort_order');
            } else {
                $wtfpl_tick_tiny_bulge = $this->config->get('total_' . $wtfpl_lease_frank_ferry . '_status');
                $wtfpl_fish_naive_speak = $this->config->get('total_' . $wtfpl_lease_frank_ferry . '_sort_order');
            }
            if ($wtfpl_tick_tiny_bulge) {
                if ($this->wtfpl_raid_extra_spout() < 230) {
                    $this->language->load('total/' . $wtfpl_lease_frank_ferry);
                } else {
                    $this->language->load('extension/total/' . $wtfpl_lease_frank_ferry);
                }
                $wtfpl_drum_upset_amass[] = [
                    'code' => $wtfpl_lease_frank_ferry,
                    'name' => strip_tags($this->language->get('heading_title')),
                    'sort_order' => $wtfpl_fish_naive_speak
                ];
            }
        }
        $wtfpl_fish_naive_speak = [];
        foreach ($wtfpl_drum_upset_amass as $wtfpl_cage_tame_kneel => $wtfpl_maple_ripe_image) {
            $wtfpl_fish_naive_speak[$wtfpl_cage_tame_kneel] = $wtfpl_maple_ripe_image['sort_order'];
        }
        array_multisort($wtfpl_fish_naive_speak, constant('SORT_ASC'), $wtfpl_drum_upset_amass);
        $this->wtfpl_hurry_nutty_show($wtfpl_drum_upset_amass);
    }

    private function wtfpl_thyme_safe_blare()
    {
        if ($this->wtfpl_raid_extra_spout() < 200 || 300 <= $this->wtfpl_raid_extra_spout()) {
            $this->load->model('setting/extension');
            $wtfpl_verge_grand_steam = $this->model_setting_extension->getInstalled('total');
            if (!in_array('filterit', $wtfpl_verge_grand_steam)) {
                $this->model_setting_extension->install('total', 'filterit');
                return NULL;
            }
        } else {
            $this->load->model('extension/extension');
            $wtfpl_verge_grand_steam = $this->model_extension_extension->getInstalled('total');
            if (!in_array('filterit', $wtfpl_verge_grand_steam)) {
                $this->model_extension_extension->install('total', 'filterit');
            }
        }
    }

    private function wtfpl_toast_muted_fail()
    {
        $wtfpl_sheep_mint_title = [];
        if ($this->wtfpl_raid_extra_spout() < 200 || 300 <= $this->wtfpl_raid_extra_spout()) {
            $this->load->model('setting/extension');
            $wtfpl_verge_grand_steam = $this->model_setting_extension->getInstalled('shipping');
        } else {
            $this->load->model('extension/extension');
            $wtfpl_verge_grand_steam = $this->model_extension_extension->getInstalled('shipping');
        }
        foreach ($wtfpl_verge_grand_steam as $wtfpl_lease_frank_ferry) {
            if ($this->wtfpl_raid_extra_spout() < 300) {
                $wtfpl_tick_tiny_bulge = $this->config->get($wtfpl_lease_frank_ferry . '_status');
            } else {
                $wtfpl_tick_tiny_bulge = $this->config->get('shipping_' . $wtfpl_lease_frank_ferry . '_status');
            }
            if ($wtfpl_tick_tiny_bulge) {
                if ($this->wtfpl_raid_extra_spout() < 230) {
                    $this->language->load('shipping/' . $wtfpl_lease_frank_ferry);
                } else {
                    $this->language->load('extension/shipping/' . $wtfpl_lease_frank_ferry);
                }
                $wtfpl_sheep_mint_title[] = [
                    'code' => $wtfpl_lease_frank_ferry,
                    'name' => strip_tags($this->language->get('heading_title')),
                    'methods' => []
                ];
            }
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_sheep_mint_title);
    }

    private function wtfpl_trap_fine_head()
    {
        $wtfpl_drum_upset_amass = [];
        if ($this->wtfpl_raid_extra_spout() < 220) {
            $wtfpl_grade_lanky_hash = $this->db->query('SELECT * FROM `' . constant('DB_PREFIX') . 'language` WHERE code = \'' . $this->db->escape($this->config->get('config_admin_language')) . '\'');
            if ($wtfpl_grade_lanky_hash->num_rows) {
                $wtfpl_smoke_heavy_stand = constant('DIR_LANGUAGE') . $wtfpl_grade_lanky_hash->row['directory'] . '/module/filterit.php';
            }
        } else {
            if ($this->wtfpl_raid_extra_spout() < 230) {
                $wtfpl_smoke_heavy_stand = constant('DIR_LANGUAGE') . $this->config->get('config_admin_language') . '/module/filterit.php';
            } else {
                $wtfpl_smoke_heavy_stand = constant('DIR_LANGUAGE') . $this->config->get('config_admin_language') . '/extension/module/filterit.php';
            }
        }
        if (!empty($wtfpl_smoke_heavy_stand) && file_exists($wtfpl_smoke_heavy_stand)) {
            $wtfpl_bread_risky_fork = '_';
            ${$wtfpl_bread_risky_fork} = [];
            require $wtfpl_smoke_heavy_stand;
            $wtfpl_drum_upset_amass = ${$wtfpl_bread_risky_fork};
        }
        $this->wtfpl_hurry_nutty_show($wtfpl_drum_upset_amass);
    }

    private function wtfpl_tray_damn_mold()
    {
        $this->load->model('localisation/tax_class');
        $this->wtfpl_hurry_nutty_show($this->model_localisation_tax_class->getTaxClasses());
    }

    private function wtfpl_tree_tense_kayak($wtfpl_share_woody_lobby)
    {
        return '//' . str_replace('http://', "", str_replace('https://', "", $wtfpl_share_woody_lobby));
    }

    private function wtfpl_wool_very_bolt()
    {
        $wtfpl_track_sandy_mock = $this->wtfpl_sole_toxic_wheel($this->config->get('filterit_license'));
        $wtfpl_cage_token_fill = $this->wtfpl_range_blond_scan();
        $wtfpl_sheep_mint_title = [];
        if ($wtfpl_track_sandy_mock) {
            $this->load->model('setting/setting');
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                if ($this->wtfpl_dream_silky_keep()) {
                    $this->wtfpl_onset_away_dress();
                    if (300 <= $this->wtfpl_raid_extra_spout()) {
                        $this->wtfpl_noise_aged_vest();
                    }
                    $this->wtfpl_thyme_safe_blare();
                    $wtfpl_cage_tame_kneel = $this->wtfpl_ozone_petty_thud();
                    $wtfpl_face_lousy_coat = isset($this->request->post['settings']) ? @json_decode(@htmlspecialchars_decode($this->{@'request'}->{@'post'}[@'settings']), true) : [];
                    $wtfpl_crumb_alone_close = isset($this->request->post['key']) ? $this->request->post['key'] : "";
                    foreach ($wtfpl_cage_token_fill as $wtfpl_kind_famed_mount) {
                        $this->model_setting_setting->editSetting('filterit', [
                            'filterit_license' => $this->config->get('filterit_license'),
                            'filterit_domain' => $this->wtfpl_joint_focal_tape('config_http'),
                            'filterit_shipping' => isset($wtfpl_face_lousy_coat['shipping']) ? $wtfpl_face_lousy_coat['shipping'] : [],
                            'filterit_payment' => isset($wtfpl_face_lousy_coat['payment']) ? $wtfpl_face_lousy_coat['payment'] : [],
                            'filterit_total' => isset($wtfpl_face_lousy_coat['total']) ? $wtfpl_face_lousy_coat['total'] : [],
                            'filterit_sort_order' => isset($wtfpl_face_lousy_coat['sort_order']) ? $wtfpl_face_lousy_coat['sort_order'] : 0,
                            'filterit_status' => isset($wtfpl_face_lousy_coat['status']) ? $wtfpl_face_lousy_coat['status'] : true,
                            'module_filterit_status' => isset($wtfpl_face_lousy_coat['status']) ? $wtfpl_face_lousy_coat['status'] : true,
                            'filterit_key' => $wtfpl_cage_tame_kneel
                        ], $wtfpl_kind_famed_mount['store_id']);
                        if (300 <= $this->wtfpl_raid_extra_spout()) {
                            $this->model_setting_setting->editSetting('total_filterit', [
                                'total_filterit_sort_order' => isset($wtfpl_face_lousy_coat['sort_order']) ? $wtfpl_face_lousy_coat['sort_order'] : 0,
                                'total_filterit_status' => isset($wtfpl_face_lousy_coat['status']) ? $wtfpl_face_lousy_coat['status'] : true
                            ], $wtfpl_kind_famed_mount['store_id']);
                            $this->wtfpl_halt_other_gear($wtfpl_kind_famed_mount['store_id'], isset($wtfpl_face_lousy_coat['status']) ? $wtfpl_face_lousy_coat['status'] ? 1 : 0 : 1);
                        }
                    }
                    $this->wtfpl_hurry_nutty_show(['success' => true]);
                } else {
                    $this->wtfpl_hurry_nutty_show(['error' => 'forbidden']);
                }
            }
            $this->wtfpl_hurry_nutty_show([
                'shipping' => $this->config->get('filterit_shipping'),
                'payment' => $this->config->get('filterit_payment'),
                'total' => $this->config->get('filterit_total'),
                'sort_order' => $this->config->get('filterit_sort_order'),
                'status' => $this->config->get('filterit_status')
            ]);
        }
        $this->wtfpl_hurry_nutty_show([
            'shipping' => [],
            'payment' => [],
            'total' => [],
            'sort_order' => 0,
            'status' => true
        ]);
    }

    public function currencies()
    {
        return $this->wtfpl_chase_near_align();
    }

    public function groups()
    {
        return $this->wtfpl_lease_tiny_clash();
    }

    public function index()
    {
        return $this->wtfpl_flake_tidy_drum();
    }

    public function language()
    {
        return $this->wtfpl_trap_fine_head();
    }

    public function languages()
    {
        return $this->wtfpl_plus_lunar_farm();
    }

    public function license()
    {
        return $this->wtfpl_rally_sole_peer();
    }

    public function localisation()
    {
        return $this->wtfpl_clan_paved_seat();
    }

    public function payment()
    {
        return $this->wtfpl_panic_novel_ripen();
    }

    public function refresh()
    {
        return $this->wtfpl_smile_whole_gasp();
    }

    public function settings()
    {
        return $this->wtfpl_wool_very_bolt();
    }

    public function shipping()
    {
        return $this->wtfpl_toast_muted_fail();
    }

    public function statuses()
    {
        return $this->wtfpl_cloak_sober_freak();
    }

    public function stores()
    {
        return $this->wtfpl_cargo_lush_scar();
    }

    public function tax_classes()
    {
        return $this->wtfpl_tray_damn_mold();
    }

    public function test_mod()
    {
        return $this->wtfpl_shirt_rusty_smart();
    }

    public function total()
    {
        return $this->wtfpl_stain_greek_snare();
    }

    public function jsonp()
    {
        header("Content-type: text/javascript");
        echo $_GET['callback'] . '(' . "{'version':'".$this->wtfpl_door_main_howl."','text':{'ru|ru_ru':[]}}" . ')';
    }
}

class ControllerExtensionModuleFilterit extends ControllerModuleFilterit
{
}

final class Puny
{
    private function wtfpl_diver_torn_pore($wtfpl_dusk_young_ripen, $wtfpl_mama_cuban_bitch = 0, &$wtfpl_blur_stale_grasp = NULL)
    {
        $wtfpl_theme_brown_like = strlen($wtfpl_dusk_young_ripen);
        $wtfpl_blur_stale_grasp = 0;
        if ($wtfpl_theme_brown_like <= $wtfpl_mama_cuban_bitch) {
            return false;
        }
        $wtfpl_place_taped_broil = ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch]);
        if ($wtfpl_place_taped_broil <= 127) {
            $wtfpl_blur_stale_grasp = 1;
            return $wtfpl_place_taped_broil;
        }
        if ($wtfpl_place_taped_broil < 194) {
            return false;
        }
        if ($wtfpl_place_taped_broil <= 223 && $wtfpl_mama_cuban_bitch < $wtfpl_theme_brown_like - 1) {
            $wtfpl_blur_stale_grasp = 2;
            return ($wtfpl_place_taped_broil & 31) << 6 | ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 1]) & 63;
        }
        if ($wtfpl_place_taped_broil <= 239 && $wtfpl_mama_cuban_bitch < $wtfpl_theme_brown_like - 2) {
            $wtfpl_blur_stale_grasp = 3;
            return ($wtfpl_place_taped_broil & 15) << 12 | (ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 1]) & 63) << 6 | ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 2]) & 63;
        }
        if ($wtfpl_place_taped_broil <= 244 && $wtfpl_mama_cuban_bitch < $wtfpl_theme_brown_like - 3) {
            $wtfpl_blur_stale_grasp = 4;
            return ($wtfpl_place_taped_broil & 15) << 18 | (ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 1]) & 63) << 12 | (ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 2]) & 63) << 6 | ord($wtfpl_dusk_young_ripen[$wtfpl_mama_cuban_bitch + 3]) & 63;
        }
        return false;
    }
    private function wtfpl_fury_funny_price($wtfpl_alpha_khaki_cable)
    {
        if (extension_loaded('mbstring')) {
            mb_internal_encoding('UTF-8');
            return mb_strlen($wtfpl_alpha_khaki_cable);
        }
        if (function_exists('iconv')) {
            return iconv_strlen($wtfpl_alpha_khaki_cable, 'UTF-8');
        }
    }

    private function wtfpl_metro_blunt_torch($wtfpl_alpha_khaki_cable, $wtfpl_break_armed_preen, $wtfpl_poem_phony_worry = NULL)
    {
        if (extension_loaded('mbstring')) {
            mb_internal_encoding('UTF-8');
            if ($wtfpl_poem_phony_worry === NULL) {
                return mb_substr($wtfpl_alpha_khaki_cable, $wtfpl_break_armed_preen, $this->wtfpl_fury_funny_price($wtfpl_alpha_khaki_cable));
            }
            return mb_substr($wtfpl_alpha_khaki_cable, $wtfpl_break_armed_preen, $wtfpl_poem_phony_worry);
        }
        if (function_exists('iconv')) {
            if ($wtfpl_poem_phony_worry === NULL) {
                return iconv_substr($wtfpl_alpha_khaki_cable, $wtfpl_break_armed_preen, $this->wtfpl_fury_funny_price($wtfpl_alpha_khaki_cable), 'UTF-8');
            }
            return iconv_substr($wtfpl_alpha_khaki_cable, $wtfpl_break_armed_preen, $wtfpl_poem_phony_worry, 'UTF-8');
        }
    }
    private function wtfpl_pecan_stern_thaw($wtfpl_alpha_khaki_cable, $wtfpl_plea_calm_huff, $wtfpl_break_armed_preen = 0)
    {
        if (extension_loaded('mbstring')) {
            mb_internal_encoding('UTF-8');
            return mb_strpos($wtfpl_alpha_khaki_cable, $wtfpl_plea_calm_huff, $wtfpl_break_armed_preen);
        }
        if (function_exists('iconv')) {
            return iconv_strpos($wtfpl_alpha_khaki_cable, $wtfpl_plea_calm_huff, $wtfpl_break_armed_preen, 'UTF-8');
        }
    }
    private function wtfpl_tribe_ripe_toast($wtfpl_venue_moral_scorn)
    {
        $wtfpl_sleep_civic_buck = explode('.', $wtfpl_venue_moral_scorn);
        if (1 < count($wtfpl_sleep_civic_buck)) {
            $wtfpl_meat_lumpy_boat = "";
            foreach ($wtfpl_sleep_civic_buck as $wtfpl_mesh_soft_labor) {
                $wtfpl_meat_lumpy_boat .= '.' . $this->wtfpl_tribe_ripe_toast($wtfpl_mesh_soft_labor);
            }
            return substr($wtfpl_meat_lumpy_boat, 1);
        } else {
            $wtfpl_dear_rigid_argue = 128;
            $wtfpl_maam_bare_crest = 0;
            $wtfpl_plant_like_refer = 72;
            $wtfpl_crowd_left_shred = array();
            $wtfpl_path_main_envy = array();
            $wtfpl_tent_moody_bulk = $wtfpl_venue_moral_scorn;
            while (0 < $this->wtfpl_fury_funny_price($wtfpl_tent_moody_bulk)) {
                array_push($wtfpl_path_main_envy, $this->wtfpl_metro_blunt_torch($wtfpl_tent_moody_bulk, 0, 1));
                $wtfpl_tent_moody_bulk = version_compare(constant('PHP_VERSION'), '5.4.8', '<') ? $this->wtfpl_metro_blunt_torch($wtfpl_tent_moody_bulk, 1, $this->wtfpl_fury_funny_price($wtfpl_tent_moody_bulk)) : $this->wtfpl_metro_blunt_torch($wtfpl_tent_moody_bulk, 1, NULL);
            }
            $wtfpl_clump_khaki_crop = preg_grep('/[\\x00-\\x7f]/', $wtfpl_path_main_envy);
            $wtfpl_curl_beige_shall = $wtfpl_clump_khaki_crop;
            if ($wtfpl_curl_beige_shall == $wtfpl_path_main_envy) {
                return $wtfpl_venue_moral_scorn;
            }
            $wtfpl_curl_beige_shall = count($wtfpl_curl_beige_shall);
            if (0 < $wtfpl_curl_beige_shall) {
                $wtfpl_crowd_left_shred = $wtfpl_clump_khaki_crop;
                $wtfpl_crowd_left_shred[] = '-';
            }
            unset($wtfpl_clump_khaki_crop);
            array_unshift($wtfpl_crowd_left_shred, 'xn--');
            $wtfpl_razor_sober_loll = count($wtfpl_path_main_envy);
            $wtfpl_place_taped_broil = $wtfpl_curl_beige_shall;
            for ($wtfpl_cadet_soft_rent = array(); $wtfpl_place_taped_broil < $wtfpl_razor_sober_loll; $wtfpl_dear_rigid_argue++) {
                $wtfpl_plum_goofy_waft = 1114111;
                for ($wtfpl_joke_poor_clone = 0; $wtfpl_joke_poor_clone < $wtfpl_razor_sober_loll; $wtfpl_joke_poor_clone++) {
                    $wtfpl_cadet_soft_rent[$wtfpl_joke_poor_clone] = $this->ordUtf8($wtfpl_path_main_envy[$wtfpl_joke_poor_clone]);
                    if ($wtfpl_dear_rigid_argue <= $wtfpl_cadet_soft_rent[$wtfpl_joke_poor_clone] && $wtfpl_cadet_soft_rent[$wtfpl_joke_poor_clone] < $wtfpl_plum_goofy_waft) {
                        $wtfpl_plum_goofy_waft = $wtfpl_cadet_soft_rent[$wtfpl_joke_poor_clone];
                    }
                }
                if (1114111 / ($wtfpl_place_taped_broil + 1) < $wtfpl_plum_goofy_waft - $wtfpl_dear_rigid_argue) {
                    return $wtfpl_venue_moral_scorn;
                }
                $wtfpl_maam_bare_crest += ($wtfpl_plum_goofy_waft - $wtfpl_dear_rigid_argue) * ($wtfpl_place_taped_broil + 1);
                $wtfpl_dear_rigid_argue = $wtfpl_plum_goofy_waft;
                for ($wtfpl_joke_poor_clone = 0; $wtfpl_joke_poor_clone < $wtfpl_razor_sober_loll; $wtfpl_joke_poor_clone++) {
                    $wtfpl_dusk_young_ripen = $wtfpl_cadet_soft_rent[$wtfpl_joke_poor_clone];
                    if ($wtfpl_dusk_young_ripen < $wtfpl_dear_rigid_argue) {
                        $wtfpl_maam_bare_crest++;
                        if ($wtfpl_maam_bare_crest == 0) {
                            return $wtfpl_venue_moral_scorn;
                        }
                    }
                    if ($wtfpl_dusk_young_ripen == $wtfpl_dear_rigid_argue) {
                        $wtfpl_fury_shut_pore = $wtfpl_maam_bare_crest;
                        $wtfpl_yacht_crazy_deck = 36;
                        while (true) {
                            if ($wtfpl_yacht_crazy_deck <= $wtfpl_plant_like_refer) {
                                $wtfpl_chill_scant_evade = 1;
                            } else {
                                if ($wtfpl_plant_like_refer + 26 <= $wtfpl_yacht_crazy_deck) {
                                    $wtfpl_chill_scant_evade = 26;
                                } else {
                                    $wtfpl_chill_scant_evade = $wtfpl_yacht_crazy_deck - $wtfpl_plant_like_refer;
                                }
                            }
                            if ($wtfpl_fury_shut_pore < $wtfpl_chill_scant_evade) {
                                break;
                            }
                            $wtfpl_snake_given_purse = $wtfpl_chill_scant_evade + ($wtfpl_fury_shut_pore - $wtfpl_chill_scant_evade) % (36 - $wtfpl_chill_scant_evade);
                            $wtfpl_crowd_left_shred[] = chr($wtfpl_snake_given_purse + 22 + 75 * ($wtfpl_snake_given_purse < 26));
                            $wtfpl_fury_shut_pore = ($wtfpl_fury_shut_pore - $wtfpl_chill_scant_evade) / (36 - $wtfpl_chill_scant_evade);
                            $wtfpl_yacht_crazy_deck += 36;
                        }
                        $wtfpl_crowd_left_shred[] = chr($wtfpl_fury_shut_pore + 22 + 75 * ($wtfpl_fury_shut_pore < 26));
                        $wtfpl_maam_bare_crest = $wtfpl_place_taped_broil == $wtfpl_curl_beige_shall ? $wtfpl_maam_bare_crest / 700 : $wtfpl_maam_bare_crest >> 1;
                        $wtfpl_maam_bare_crest += intval($wtfpl_maam_bare_crest / ($wtfpl_place_taped_broil + 1));
                        $wtfpl_idea_civic_block = 0;
                        while (455 < $wtfpl_maam_bare_crest) {
                            $wtfpl_maam_bare_crest /= 35;
                            $wtfpl_idea_civic_block += 36;
                        }
                        $wtfpl_plant_like_refer = intval($wtfpl_idea_civic_block + 36 * $wtfpl_maam_bare_crest / ($wtfpl_maam_bare_crest + 38));
                        $wtfpl_maam_bare_crest = 0;
                        $wtfpl_place_taped_broil++;
                    }
                }
                $wtfpl_maam_bare_crest++;
            }
            return implode("", $wtfpl_crowd_left_shred);
        }
    }
    public function getPunycode($value = "")
    {
        return $this->wtfpl_tribe_ripe_toast($value);
    }
    public function ordUTF8($c = "", $index = "0", $bytes = "null")
    {
        return $this->wtfpl_diver_torn_pore($c, $index, $bytes);
    }
    public function strlen($string = "")
    {
        return $this->wtfpl_fury_funny_price($string);
    }
    public function strpos($string = "", $needle = "", $offset = "0")
    {
        return $this->wtfpl_pecan_stern_thaw($string, $needle, $offset);
    }
    public function substr($string = "", $offset = "", $length = "null")
    {
        return $this->wtfpl_metro_blunt_torch($string, $offset, $length);
    }
}