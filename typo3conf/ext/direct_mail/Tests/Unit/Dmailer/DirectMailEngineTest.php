<?php
namespace DirectMailTeam\DirectMail\Tests\Unit\Mailer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Bernhard Kraft <kraft@webconsulting.at>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Testcase for class "dmailer"
 *
 * @author Bernhard Kraft <kraft@webconsulting.at>
 */
class DirectMailEngineTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 * @dataProvider extractHyperLinksDataProvider
	 */
	public function test_extractHyperLinks($content, $path, $expected) {
		// This test also tests "tag_regex", "get_tag_attributes" and "absRef"
		// TODO: Write units tests also for those methods and provide mocked methods here.

		// The method "extractMediaLinks" will also get called but its result does not get tested.
		// TODO: Expand this test to make sure media with the "use_jumpurl" attribute will
		// also get added to the extracted hyperlinks

		// Create an instance of "dmailer" with only the "extractMediaLinks" being mocked.
		$dmailer = $this->getMock('dmailer', array('extractMediaLinks'));
		$dmailer->expects($this->once())->method('extractMediaLinks');
		$dmailer->theParts['html']['content'] = $content;
		$dmailer->theParts['html']['path'] = $path;
		$dmailer->theParts['html']['media'] = array();
		$dmailer->extractHyperLinks();

		$this->assertEquals($expected, $dmailer->theParts['html']['hrefs']);
	}

	/**
	 * Data provider for test_extractHyperLinks
	 *
	 * @return array
	 */
	public function extractHyperLinksDataProvider() {
		return array(
			'no hyperlinks found' => array('This is a simple test', '', NULL),
			'no hyperlinks in anchor' => array('This is a <a name="anchor">simple</a> test', '', NULL),
			'absolute url' => array('
				This is a <a name="link" href="http://google.com">simple</a> test',
				'http://www.server.com/',
				array(
					array(
						'ref' => 'http://google.com',
						'quotes' => '"',
						'subst_str' => '"http://google.com"',
						'absRef' => 'http://google.com',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'absolute url (fails currently, #54459)' => array('
				This is a <a name=http://google.com href="http://google.com">simple</a> test',
				'http://www.server.com/',
				array(
					array(
						'ref' => 'http://google.com',
						'quotes' => '"',
						'subst_str' => '"http://google.com"',
						'absRef' => 'http://google.com',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'relative link #1' => array('
				This is a <a name="link" href="fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com/',
				array(
					array(
						'ref' => 'fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'relative link #2' => array('
				This is a <a name="link" href="fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com',
				array(
					array(
						'ref' => 'fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'relative link #3' => array('
				This is a <a name="link" href="fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com/subdirectory/',
				array(
					array(
						'ref' => 'fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/subdirectory/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'relative link #4' => array('
				This is a <a name="link" href="fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com/subdirectory',
				array(
					array(
						'ref' => 'fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'absolute link #1' => array('
				This is a <a name="link" href="/fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com/subdirectory',
				array(
					array(
						'ref' => '/fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"/fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'absolute link #2' => array('
				This is a <a name="link" href="/fileadmin/simple.pdf">simple</a> test',
				'http://www.server.com/subdirectory/',
				array(
					array(
						'ref' => '/fileadmin/simple.pdf',
						'quotes' => '"',
						'subst_str' => '"/fileadmin/simple.pdf"',
						'absRef' => 'http://www.server.com/fileadmin/simple.pdf',
						'tag' => 'a',
						'no_jumpurl' => 0,
					),
				)
			),
			'absolute link #3 (no_jumpurl)' => array('
				This is a <a name="link" href="image.png" no_jumpurl="1">simple</a> test',
				'http://www.server.com/subdirectory',
				array(
					array(
						'ref' => 'image.png',
						'quotes' => '"',
						'subst_str' => '"image.png"',
						'absRef' => 'http://www.server.com/image.png',
						'tag' => 'a',
						'no_jumpurl' => 1,
					),
				)
			),
			'form action #1' => array('
				Hello.<br />
				Here you can send us your comment<br />
				<form name="formname" action="index.php?id=123" method="POST" no_jumpurl=1>
					<input type="text" name="comment" value="">
				</form>
				Thanks!',
				'http://www.server.com/subdirectory/',
				array(
					array(
						'ref' => 'index.php?id=123',
						'quotes' => '"',
						'subst_str' => '"index.php?id=123"',
						'absRef' => 'http://www.server.com/subdirectory/index.php?id=123',
						'tag' => 'form',
						'no_jumpurl' => 1,
					),
				)
			),
		);
	}


}

