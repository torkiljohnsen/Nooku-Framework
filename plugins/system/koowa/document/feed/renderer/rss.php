<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Document
 * @copyright   Copyright (C) 2007 - 2009 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Feed renderer that implements RSS 2.0 Specification
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package 	Koowa_Document
 * @subpackage	Feed
 * @uses 		KFactory
 * @see http://www.rssboard.org/rss-specification
 */

class KDocumentFeedRendererRSS extends KDocumentRenderer
{
	/**
	 * Render the feed
	 *
	 * @return	string
	 */
	public function render($name, array $params = array(), $content = null)
	{
		$now	= KFactory::get('lib.joomla.date');
		$data	= $this->_doc;

		$uri = KFactory::get('lib.joomla.uri');
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		$feed = "<rss version=\"2.0\">\n";
		$feed.= "	<channel>\n";
		$feed.= "		<title>".$data->title."</title>\n";
		$feed.= "		<description>".$data->description."</description>\n";
		$feed.= "		<link>".$url.$data->link."</link>\n";
		$feed.= "		<lastBuildDate>".htmlspecialchars($now->toRFC822(), ENT_COMPAT, 'UTF-8')."</lastBuildDate>\n";
		$feed.= "		<generator>".$data->getGenerator()."</generator>\n";

		if ($data->image!=null)
		{
			$feed.= "		<image>\n";
			$feed.= "			<url>".$data->image->url."</url>\n";
			$feed.= "			<title>".htmlspecialchars($data->image->title, ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$data->image->link."</link>\n";
			if ($data->image->width != "") {
				$feed.= "			<width>".$data->image->width."</width>\n";
			}
			if ($data->image->height!="") {
				$feed.= "			<height>".$data->image->height."</height>\n";
			}
			if ($data->image->description!="") {
				$feed.= "			<description><![CDATA[".$data->image->description."]]></description>\n";
			}
			$feed.= "		</image>\n";
		}
		if ($data->language!="") {
			$feed.= "		<language>".$data->language."</language>\n";
		}
		if ($data->copyright!="") {
			$feed.= "		<copyright>".htmlspecialchars($data->copyright,ENT_COMPAT, 'UTF-8')."</copyright>\n";
		}
		if ($data->editor!="") {
			$feed.= "		<managingEditor>".htmlspecialchars($data->editor, ENT_COMPAT, 'UTF-8')."</managingEditor>\n";
		}
		if ($data->webmaster!="") {
			$feed.= "		<webMaster>".htmlspecialchars($data->webmaster, ENT_COMPAT, 'UTF-8')."</webMaster>\n";
		}
		if ($data->pubDate!="") {
			$pubDate =& JFactory::getDate($data->pubDate);
			$feed.= "		<pubDate>".htmlspecialchars($pubDate->toRFC822(),ENT_COMPAT, 'UTF-8')."</pubDate>\n";
		}
		if ($data->category!="") {
			$feed.= "		<category>".htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8')."</category>\n";
		}
		if ($data->docs!="") {
			$feed.= "		<docs>".htmlspecialchars($data->docs, ENT_COMPAT, 'UTF-8')."</docs>\n";
		}
		if ($data->ttl!="") {
			$feed.= "		<ttl>".htmlspecialchars($data->ttl, ENT_COMPAT, 'UTF-8')."</ttl>\n";
		}
		if ($data->rating!="") {
			$feed.= "		<rating>".htmlspecialchars($data->rating, ENT_COMPAT, 'UTF-8')."</rating>\n";
		}
		if ($data->skipHours!="") {
			$feed.= "		<skipHours>".htmlspecialchars($data->skipHours, ENT_COMPAT, 'UTF-8')."</skipHours>\n";
		}
		if ($data->skipDays!="") {
			$feed.= "		<skipDays>".htmlspecialchars($data->skipDays, ENT_COMPAT, 'UTF-8')."</skipDays>\n";
		}

		for ($i=0; $i<count($data->items); $i++)
		{
			if ((strpos($data->items[$i]->link, 'http://') === false) and (strpos($data->items[$i]->link, 'https://') === false)) {
				$data->items[$i]->link = $url.$data->items[$i]->link;
			}
			$feed.= "		<item>\n";
			$feed.= "			<title>".htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$data->items[$i]->link."</link>\n";
			$feed.= "			<description><![CDATA[".$this->_relToAbs($data->items[$i]->description)."]]></description>\n";

			if ($data->items[$i]->author!="") {
				$feed.= "			<author>".htmlspecialchars($data->items[$i]->author, ENT_COMPAT, 'UTF-8')."</author>\n";
			}
			/*
			// on hold
			if ($data->items[$i]->source!="") {
					$data.= "			<source>".htmlspecialchars($data->items[$i]->source, ENT_COMPAT, 'UTF-8')."</source>\n";
			}
			*/
			if ($data->items[$i]->category!="") {
				$feed.= "			<category>".htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8')."</category>\n";
			}
			if ($data->items[$i]->comments!="") {
				$feed.= "			<comments>".htmlspecialchars($data->items[$i]->comments, ENT_COMPAT, 'UTF-8')."</comments>\n";
			}
			if ($data->items[$i]->date!="") {
			$itemDate =& JFactory::getDate($data->items[$i]->date);
				$feed.= "			<pubDate>".htmlspecialchars($itemDate->toRFC822(), ENT_COMPAT, 'UTF-8')."</pubDate>\n";
			}
			if ($data->items[$i]->guid!="") {
				$feed.= "			<guid>".htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8')."</guid>\n";
			}
			if ($data->items[$i]->enclosure != NULL)
			{
					$feed.= "			<enclosure url=\"";
					$feed.= $data->items[$i]->enclosure->url;
					$feed.= "\" length=\"";
					$feed.= $data->items[$i]->enclosure->length;
					$feed.= "\" type=\"";
					$feed.= $data->items[$i]->enclosure->type;
					$feed.= "\"/>\n";
			}

			$feed.= "		</item>\n";
		}
		$feed.= "	</channel>\n";
		$feed.= "</rss>\n";
		return $feed;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @param 	string $text	Text to be converted
	 * @return 	string
	 */
	public function _relToAbs($text)
	{
		$base = JURI::base();
  		$text = preg_replace("/(href|src)=\"(?!http|ftp|https)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
