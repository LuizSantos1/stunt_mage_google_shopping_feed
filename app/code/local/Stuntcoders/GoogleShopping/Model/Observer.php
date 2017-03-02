<?php

class Stuntcoders_GoogleShopping_Model_Observer
{
    public function generateFeeds()
    {
        $feeds = Mage::getModel('stuntcoders_googleshopping/feed')->getCollection();
        foreach ($feeds as $feed) {
            /** @var Stuntcoders_GoogleShopping_Model_Feed $feed */
            $file = new Varien_Io_File();
            $file->mkdir(dirname($feed->getPath()), 755, true);
            $file->write($feed->getPath(), $feed->generateXml());
        }
    }
}
