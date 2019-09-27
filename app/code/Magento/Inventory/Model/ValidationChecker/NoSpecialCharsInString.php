<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Model\ValidationChecker;

/**
 * Checks whether given string is empty
 */
class NoSpecialCharsInString
{
    /**
     * @param mixed $value
     * @return array
     */
    public function execute($value): array
    {
        $errors = [];

        if (preg_match('/\$[:]*{(.)*}/', $value)) {
            $errors[] = __('Validation Failed');
        }

        return $errors;
    }
}
