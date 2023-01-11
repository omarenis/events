<?php
namespace Omarenis\Events;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Text;
use Geniem\ACF\Field\Textarea;

/**
 * @throws Exception
 */

class Fields
{
    /**
     * @param string $label
     * @param string $name
     * @param string $key
     * @param string $placeholder
     * @param string $type
     * @param int $maxLength
     * @return Textarea|Text
     * @throws Exception
     */
    public function createTextField(
        string $label,
        string $name,
        string $key,
        string $placeholder,
        string $type = 'input',
        int $maxLength = 2500
    ) {
        $text = $type == 'input' ? new Text($label) : new Textarea($label);
        $text->set_key($key);
        $text->set_maxlength($maxLength);
        $text->set_placeholder($placeholder);
        $text->set_wrapper_classes(['container']);
        $text->set_name($name);
        return $text;
    }
}
