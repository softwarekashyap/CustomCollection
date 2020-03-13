<?php

/**
 * @author Kashyap Team
 * @copyright Copyright (c) 2018 Kashyap (softwarekashyap@gmail.com)
 * @package Kashyap_Customcollection
*/

namespace Kashyap\Customcollection\Block\Index;
class Index extends \Magento\Framework\View\Element\Template 
{
    protected $_productCollectionFactory;
    protected $_listProduct;
    protected $_productFactory;
    protected $_storeManager;
    protected $_categoryFactory;

        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Block\Product\ListProduct $listProduct, 
        \Magento\Catalog\Model\ProductFactory $productFactory,  
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
         \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
         \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Magento\Reports\Block\Product\Viewed $recentlyViewed,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory; 
        $this->_collectionFactory = $collectionFactory;     
        $this->_listProduct = $listProduct;    
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager; 
        $this->_categoryFactory = $categoryFactory;   
        $this->_categoryRepository = $categoryRepository;
        $this->_stdTimezone =  $stdTimezone;
        $this->_productsFactory = $productsFactory;
        $this->_recentlyViewed = $recentlyViewed;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /*
    **
    *** Getting category information by category id
    **
    */

    public function getCategory($categoryId)
    {    
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);        
        return $this->_category;
    }

    /*
    **
    *** Get child categories by parent category id
    **
    */

    public function getChildCategoryCollection($categoryId,$noOfCate=NULL)
    {    
        if(empty($noOfCate))
        {
            $noOfCate = 10;
        }

        $categoryObj = $this->_categoryRepository->get($categoryId);
        return $categoryObj->getChildrenCategories()->setPageSize($noOfCate);
           
    }

    /*
    **
    *** Getting product collection by product id
    **
    */

    public function getProductCollectionById($id)
    {
    	return $collection = $this->_productCollectionFactory->create()
    						->addAttributeToSelect('*')
    						->addFieldToFilter('entity_id', array('in' => $id));
    }

 	/*
    **
    *** Getting product collections by product attribute
    **
    */

    public function getCustomCollection($attributeId,$noOfProduct=NULL)
    {       
        if(empty($attributeId))
        {
            return false;
        } 
        else
        {
           $attr = $this->_productFactory->create()->getResource()->getAttribute($attributeId);
           if(!$attr){
                return false;
           }
        }

        if(empty($noOfProduct))
        {
            $noOfProduct = 10;
        }

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        
        if(($attributeId == "news_from_date") || ($attributeId == "news_to_date"))
        {
            $currentTime = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
            $collection
            ->addFieldToFilter('news_from_date', ['lteq' => $currentTime])
            ->addFieldToFilter('news_to_date', ['gteq' => $currentTime])
            ->setOrder('sort_order', 'ASC')
            ->getData();
        }
        else
        {
            $collection->addAttributeToFilter($attributeId, 1);
        }

        $collection->setPageSize($noOfProduct); // fetching only 3 products
        return $collection;
    }

    /*
    **
    *** Getting Bestseller products
    **
    */

    public function getBestSellerCollection($noOfProduct=NULL){

        if(empty($noOfProduct))
        {
            $noOfProduct = 10;
        }

        $bestSellerProdcutCollection = $this->_collectionFactory->create()
                    ->setModel('Magento\Catalog\Model\Product')
                    ->setPeriod('month') //you can add period daily,yearly
                    //->setPeriod('year');
                    //->setPeriod('day');
                    ->setPageSize($noOfProduct);
                   ;
        return $bestSellerProdcutCollection;
    }

    /*
    **
    *** Getting most viewed products
    **
    */

    public function getMostViewCollection()
    {

        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_productsFactory->create()
        ->addAttributeToSelect(
            '*'
        )->addViewsCount()->setStoreId(
                $currentStoreId
        )->addStoreFilter(
                $currentStoreId
        );
        $items = $collection->getItems();
        return $items;
    }

    /*
    **
    *** Getting recently viewed products
    **
    */

    public function getRecentlyViewCollection()
    {

        return $this->_recentlyViewed->getItemsCollection();
    }

    /*
    **
    *** Getting object of produt listing page
    **
    */

    public function getListBlock()
    {
        return $this->_listProduct;
    }

}