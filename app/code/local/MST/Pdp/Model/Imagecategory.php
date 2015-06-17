<?php
class MST_Pdp_Model_Imagecategory extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('pdp/imagecategory');
	}
	public function getCategoryOptions()
	{
		return array (
			'animals'=> 'Animals',
			'people' => 'People',
			'nature' => 'Nature',
			'funny' => 'Funny',
			'kid' => 'Kid',
			'love' => 'Love',
			'music' => 'Music',
			'cartoon' => 'Cartoon',
			'places' => 'Places',
			'other' => 'Other',
		);
	}
}