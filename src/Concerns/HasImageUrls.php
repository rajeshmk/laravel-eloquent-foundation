<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use function Hatchyu\Support\image_url;

trait HasImageUrls
{
    public function imageDimensions(): array
    {
        return [
            'url' => 150,
            'thumb_url' => 75,
        ];

        // return [
        //     'url' => [150, 150],
        //     'thumb_url' => [75, 75],
        // ];
    }

    /**
     * Override the getAttribute method to handle dynamic properties.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        foreach ($this->resolveCustomAccessors() as $accessorName => $accessor) {
            if ($key === $accessorName) {
                return image_url($this->{$accessor['field']}, $accessor['width'], $accessor['height']);
            }
        }

        // Fall back to the default getAttribute behavior
        return parent::getAttribute($key);
    }

    /**
     * Override the attributesToArray method to include dynamic properties.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        // Add dynamic properties to the array
        foreach ($this->resolveCustomAccessors() as $accessorName => $accessor) {
            $attributes[$accessorName] = $this->{$accessorName};
        }

        return $attributes;
    }

    protected function imageFields(): array
    {
        return property_exists($this, 'imageFields') ? $this->imageFields : [];
    }

    private function resolveCustomAccessors(): array
    {
        $accessors = [];

        foreach ($this->imageFields() as $field) {
            foreach ($this->imageDimensions() as $sizeType => $value) {
                if (is_array($value)) {
                    [$width, $height] = $value;
                } else {
                    $width = $height = $value;
                }

                $accessorName = "{$field}_{$sizeType}";

                $accessors[$accessorName] = [
                    'field' => $field,
                    'sizeType' => $sizeType,
                    'width' => $width,
                    'height' => $height,
                ];
            }
        }

        return $accessors;
    }
}
