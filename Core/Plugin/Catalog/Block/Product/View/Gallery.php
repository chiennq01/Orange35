<?php

namespace Orange35\Core\Plugin\Catalog\Block\Product\View;

class Gallery
{
    /**
     * Add an Image ID which will be used in JavaScript (constructor.js) to merge image with layers
     * @noinspection PhpUnused
     */
    public function afterGetGalleryImagesJson(\Magento\Catalog\Block\Product\View\Gallery $subject, $result)
    {
        $json = json_decode($result, true);
        foreach ($subject->getGalleryImages() as $image) {
            $img = $image->getData('medium_image_url');
            foreach ($json as &$jsonImage) {
                if ($jsonImage['img'] === $img) {
                    $jsonImage['id'] = (int) $image->getId();
                    break;
                }
            }
        }
        return json_encode($json);
    }
}
