<?php

/*
 * Copyright 2016-2019 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


/** @var \DCarbone\PHPFHIR\Config\VersionConfig $config */
/** @var \DCarbone\PHPFHIR\Definition\Types $types */
/** @var \DCarbone\PHPFHIR\Definition\Type $type */
/** @var \DCarbone\PHPFHIR\Enum\PrimitiveTypeEnum $primitiveType */
/** @var string $fhirName */
/** @var string $typeClassName */

ob_start(); ?>
    const VALUE_REGEX           = // language=RegExp
        '([0-9]([0-9]([0-9][1-9]|[1-9]0)|[1-9]00)|[1-9]000)(-(0[1-9]|1[0-2])(-(0[1-9]|[1-2][0-9]|3[0-1]))?)?';
    const FORMAT_YEAR           = 'Y';
    const FORMAT_YEAR_MONTH     = 'Y-m';
    const FORMAT_YEAR_MONTH_DAY = 'Y-m-d';

    /**
     * <?php echo $typeClassName; ?> Constructor
     * @param null|string $value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * @var null|string $value
     * @return <?php echo $type->getFullyQualifiedClassName(true); ?>

     */
    public function setValue($value)
    {
        if (null === $value) {
            $this->value = $this->dateTime = null;
            return $this;
        }
        if (is_string($value)) {
            $this->value = $value;
            $this->dateTime = null;
            return $this;
        }
        throw new \InvalidArgumentException(sprintf('Value must be null or string, %s seen.', gettype($value)));
    }

    /**
     * @return null|\DateTime
     */
    public function getDateTime()
    {
        if (!isset($this->dateTime)) {
            $value = $this->getValue();
            if (null === $value) {
                return null;
            }
            if (!$this->isValid()) {
                throw new \DomainException(sprintf('Cannot convert "%s" to \\DateTime as it does not conform to "%s"', $value, self::VALUE_REGEX));
            }
            switch(strlen($value)) {
                case 4:
                    $parsed = \DateTime::createFromFormat(self::FORMAT_YEAR, $value);
                    break;
                case 7:
                    $parsed = \DateTime::createFromFormat(self::FORMAT_YEAR_MONTH, $value);
                    break;
                case 10:
                    $parsed = \DateTime::createFromFormat(self::FORMAT_YEAR_MONTH_DAY, $value);
                    break;

                default:
                    throw new \DomainException(sprintf('Value expected to meet %s, %s seen', self::VALUE_REGEX, $value));
            }
            if (false === $parsed) {
                throw new \DomainException(sprintf('Value "%s" could not be parsed as <?php echo $fhirName; ?>: %s', $value, implode(', ', \DateTime::getLastErrors())));
            }
            $this->dateTime = $parsed;
        }
        return $this->dateTime;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $value = $this->getValue();
        return null === $value || preg_match('/' . self::VALUE_REGEX . '/', $value);
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->value;
    }

<?php return ob_get_clean();