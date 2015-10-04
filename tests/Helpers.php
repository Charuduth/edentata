<?php
/*
 * Copyright (C) 2014 Michael Herold <quabla@hemio.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Unit test utils for html
 */
class Helpers extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @param hemio\html\Interface_\HtmlCode $objHtml
     * @param string $strFile
     */
    protected function _assertEqualsXmlFile(hemio\html\Interface_\HtmlCode $objHtml,
                                            $strFile)
    {
        $path = __DIR__.DIRECTORY_SEPARATOR.$strFile;

        $actual       = new DOMDocument;
        $actualString = (string) $objHtml;
        $actual->loadXML($actualString);

        if (!file_exists($path))
            file_put_contents($path, /* 'NEW-AUTOGENERATED' . */
                              PHP_EOL.$actualString);

        $expected = new DOMDocument;
        $expected->load($path);

        $this->assertEqualXMLStructure($expected->documentElement,
                                       $actual->documentElement, true);


        $this->assertEquals($this->toXml($expected), $this->toXml($actual));
    }

    private function toXml(DOMDocument $obj)
    {
        $obj->normalizeDocument();
        return str_replace(PHP_EOL, '', str_replace(' ', '', $obj->saveHTML()));
    }

    public static function getDocumentBody()
    {
        return (new hemio\html\Document(new hemio\html\Str('Title')))->getHtml()->getBody();
    }
}
