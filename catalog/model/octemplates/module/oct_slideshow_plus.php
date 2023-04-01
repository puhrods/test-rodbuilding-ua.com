<?php
/**************************************************************/
/*	@copyright	OCTemplates 2019.							  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ModelOCTemplatesModuleOctSlideshowPlus extends Model {
	public function getSlideshow($slideshow_id) {
		$query = $this->db->query("
			SELECT
				*
			FROM `" . DB_PREFIX . "oct_slideshow_plus` s
			LEFT JOIN `" . DB_PREFIX . "oct_slideshow_plus_image` si
				ON (s.slideshow_id = si.slideshow_id)
			LEFT JOIN `" . DB_PREFIX . "oct_slideshow_plus_image_description` sid
				ON (si.slideshow_image_id = sid.slideshow_image_id)
			WHERE
				s.slideshow_id = '" . (int)$slideshow_id . "'
				AND sid.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND s.status = '1'
			ORDER BY
				si.sort_order ASC");

		return $query->rows;
	}

	public function getSlideshowBanners($slideshow_id) {
		$query = $this->db->query("
			SELECT
				*
			FROM `" . DB_PREFIX . "oct_slideshow_plus_banner` b
			LEFT JOIN `" . DB_PREFIX . "oct_slideshow_plus_banner_description` bd
				ON (b.slideshow_id = bd.slideshow_id)
			WHERE
				b.slideshow_id = '" . (int)$slideshow_id . "'
				AND bd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
}
