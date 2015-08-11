<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_Facebook extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'facebook';
	
    protected $_url = 'https://www.facebook.com/dialog/oauth';

	protected $_fields = array(
					'user_id' => 'id',
		            'firstname' => 'first_name',
		            'lastname' => 'last_name',
		            'email' => 'email',
		            'dob' => 'birthday',
                    'gender' => 'gender',
                    'photo' => 'picture',
				);

	protected $_buttonLinkParams = array(
					'scope' => 'email,user_birthday',
                    'display' => 'popup',
				);

    protected $_popupSize = array(650, 350);

	public function _construct()
    {      
        parent::_construct();
        
        $this->_buttonLinkParams = array_merge($this->_buttonLinkParams, array(
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'response_type' => $this->_responseType
        ));
    }

    public function loadUserData($response)
    {
    	if(empty($response)) {
    		return false;
    	}

        $data = array();

        $params = array(
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        );
    
        $token = null;
        if($response = $this->_call('https://graph.facebook.com/oauth/access_token', $params)) {
            parse_str($response, $token);
        }
        $this->_setLog($response, true);
        $this->_setLog($token, true);
    
        if (isset($token['access_token'])) {
            $params = array(
                'access_token'  => $token['access_token'],
                'fields'        => implode(',', $this->_fields)
            );
    
            if($response = $this->_call('https://graph.facebook.com/me', $params)) {
                $data = json_decode($response, true);
            }
            
            if(!empty($data['id'])) {
                $data['picture'] = 'https://graph.facebook.com/'. $data['id'] .'/picture?return_ssl_resources=true';
            }
            
            $this->_setLog($data, true);
        }
 
        if(!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
    	if(empty($data['id'])) {
    		return false;
    	}

        return parent::_prepareData($data);
    }

}