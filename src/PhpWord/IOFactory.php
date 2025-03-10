<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Reader\ReaderInterface;
use PhpOffice\PhpWord\Writer\WriterInterface;
use ReflectionClass;

abstract class IOFactory
{
    /**
     * Create new writer.
     *
     * @param string $name
     *
     * @return WriterInterface
     */
    public static function createWriter(PhpWord $phpWord, $name = 'Word2007', $writerConfigs=[])
    {
        if ($name !== 'WriterInterface' && !in_array($name, ['ODText', 'RTF', 'Word2007', 'HTML', 'PDF'], true)) {
            throw new Exception("\"{$name}\" is not a valid writer.");
        }

        $fqName = "PhpOffice\\PhpWord\\Writer\\{$name}";

        return new $fqName($phpWord, $writerConfigs);
    }

    /**
     * Create new reader.
     *
     * @param string $name
     *
     * @return ReaderInterface
     */
    public static function createReader($name = 'Word2007')
    {
        return self::createObject('Reader', $name);
    }

    /**
     * Create new object.
     *
     * @param string $type
     * @param string $name
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     *
     * @return \PhpOffice\PhpWord\Reader\ReaderInterface|\PhpOffice\PhpWord\Writer\WriterInterface
     */
    private static function createObject($type, $name, $phpWord = null)
    {
        $class = "PhpOffice\\PhpWord\\{$type}\\{$name}";
        if (class_exists($class) && self::isConcreteClass($class)) {
            return new $class($phpWord);
        }

        throw new Exception("\"{$name}\" is not a valid {$type}.");
    }

    /**
     * Loads PhpWord from file.
     *
     * @param string $filename The name of the file
     * @param string $readerName
     *
     * @return \PhpOffice\PhpWord\PhpWord $phpWord
     */
    public static function load($filename, $readerName = 'Word2007')
    {
        /** @var \PhpOffice\PhpWord\Reader\ReaderInterface $reader */
        $reader = self::createReader($readerName);

        return $reader->load($filename);
    }

    /**
     * Check if it's a concrete class (not abstract nor interface).
     *
     * @param string $class
     *
     * @return bool
     */
    private static function isConcreteClass($class)
    {
        $reflection = new ReflectionClass($class);

        return !$reflection->isAbstract() && !$reflection->isInterface();
    }
}
