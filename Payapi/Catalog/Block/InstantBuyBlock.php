<?php

namespace Payapi\Catalog\Block;

class InstantBuyBlock extends \Magento\Framework\View\Element\Template {
	
	protected $_payapiPublicId;
	protected $_payapiApiKey;
    protected $_instantBuyDefaultShipping;
    protected $_objectManager;

    public function checkPayApiConfiguration(){
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$paymentHelper =  $objectManager->get('\Magento\Payment\Helper\Data');
        $paymentMethod = $paymentHelper->getMethodInstance("payapi_checkoutpayment_secure_form_post");

        $this->_payapiApiKey = $paymentMethod->getConfigData('payapi_api_key');
        $this->_payapiPublicId = $paymentMethod->getConfigData('payapi_public_id');
        $this->_instantBuyDefaultShipping = $paymentMethod->getConfigData('instantbuy_shipping_method');
        $this->_objectManager = $objectManager;
        return isset($this->_payapiPublicId) && isset($this->_payapiApiKey) && isset($this->_instantBuyDefaultShipping) && is_string($this->_instantBuyDefaultShipping) && strlen($this->_instantBuyDefaultShipping) > 0;
    }

    public function getPublicId(){
        return $this->_payapiPublicId;
    }   

    public function getApiKey(){
        return $this->_payapiApiKey;
    }   

    public function getVisitorIp($checkParams = true) {  
        $ipaddress = '';
        $paramIp = $this->getRequest()->getQueryValue('ip');
        if($checkParams && $paramIp){
            return $paramIp;
        }
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');        
        
        return $ipaddress;
    }

    public function checkMandatoryFields(){
        if($this->getProduct()){
            $customOptions = $this->_objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($this->getProduct());
            if($customOptions){
                foreach ($customOptions as $o) {
                    if ($o->getIsRequire()) { // or another title of option
                        return 1;
                    }
                }
            }
        }
        return 0;
    }
}