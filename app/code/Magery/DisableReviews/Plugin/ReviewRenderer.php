<?php
/**
 * @author     MageryThemes Team
 * @copyright  Copyright (c) 2018 MageryThemes (https://magery-themes.com)
 * @package    Magery_DisableReviews
 */

namespace Magery\DisableReviews\Plugin;

class ReviewRenderer
{
    /**
     * @var \Magery\DisableReviews\Helper\Data
     */
    private $helper;

    public function __construct(\Magery\DisableReviews\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Disable review summary block
     * (ratings, link with with reviews count and add review link)
     *
     * @param \Magento\Review\Block\Product\ReviewRenderer $renderer
     * @param string $html
     *
     * @return string
     */
    public function afterGetReviewsSummaryHtml($renderer, $html)
    {
        if ($this->helper->reviewIsDisabled()) {
            return '';
        }

        return $html;
    }
}
